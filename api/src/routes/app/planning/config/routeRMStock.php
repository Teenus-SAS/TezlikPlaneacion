<?php

use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsMaterialsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\RMStockDao;

$stockDao = new RMStockDao();
$generalStockDao = new GeneralRMStockDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$generalProductsDao = new GeneralProductsDao();
$minimumStockDao = new MinimumStockDao();
$productMaterialsDao = new ProductsMaterialsDao();
$generalProductsMaterialsDao = new GeneralProductsMaterialsDao();
$generalClientsDao = new GeneralClientsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/rMStock', function (Request $request, Response $response, $args) use ($stockDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $stock = $stockDao->findAllStockByCompany($id_company);
    $response->getBody()->write(json_encode($stock, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/stockMaterials', function (Request $request, Response $response, $args) use ($generalMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $generalMaterialsDao->findAllMaterialsStockByCompany($id_company);
    $response->getBody()->write(json_encode($materials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/rMStockDataValidation', function (Request $request, Response $response, $args) use (
    $generalStockDao,
    $generalMaterialsDao,
    $generalClientsDao
) {
    $dataStock = $request->getParsedBody();

    if (isset($dataStock)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $stock = $dataStock['importStock'];

        for ($i = 0; $i < sizeof($stock); $i++) {
            if (
                empty($stock[$i]['refRawMaterial']) || empty($stock[$i]['nameRawMaterial']) ||
                $stock[$i]['max'] == '' || $stock[$i]['usual'] == ''
            ) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            if (
                empty(trim($stock[$i]['refRawMaterial'])) || empty(trim($stock[$i]['nameRawMaterial'])) ||
                trim($stock[$i]['max']) == '' || trim($stock[$i]['usual']) == ''
            ) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            $max = str_replace(',', '.', $stock[$i]['max']);
            $usual = str_replace(',', '.', $stock[$i]['usual']);

            $data = $max * $usual;

            if ($data <= 0 || is_nan($data)) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "La cantidad debe ser mayor a cero (0)<br>Fila: {$i}");
                break;
            }

            // Obtener id materia prima
            $findMaterial = $generalMaterialsDao->findMaterial($stock[$i], $id_company);
            if (!$findMaterial) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "Materia prima no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $stock[$i]['idMaterial'] = $findMaterial['id_material'];

            // Obtener id proveedor
            $findClient = $generalClientsDao->findClientByName($stock[$i], $id_company, 2);
            if (!$findClient) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "Cliente no existe en la base de datos o es tipo cliente.<br>Fila: {$i}");
                break;
            } else $stock[$i]['idProvider'] = $findClient['id_client'];

            $findstock = $generalStockDao->findStock($stock[$i]);
            if (!$findstock) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportStock['insert'] = $insert;
            $dataImportStock['update'] = $update;
        }
    } else
        $dataImportStock = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportStock, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addRMStock', function (Request $request, Response $response, $args) use (
    $stockDao,
    $generalStockDao,
    $generalMaterialsDao,
    $generalClientsDao,
    $generalProductsMaterialsDao,
    $generalProductsDao,
    $minimumStockDao,
) {
    session_start();
    $dataStock = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    if (empty($dataStock['importStock'])) {

        $findStock = $generalStockDao->findStock($dataStock);

        if (!$findStock) {
            $resolution = $stockDao->insertStockByCompany($dataStock, $id_company);

            if ($resolution == null) {
                $arr = $minimumStockDao->calcStockByMaterial($dataStock['idMaterial']);
                if (isset($arr['stock']))
                    $resolution = $generalMaterialsDao->updateStockMaterial($dataStock['idMaterial'], $arr['stock']);
            }

            // if ($resolution == null) {
            //     $products = $generalProductsMaterialsDao->findAllProductByMaterial($dataStock['idMaterial']);

            //     foreach ($products as $arr) {
            //         $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
            //         if (isset($product['stock']))
            //             $resolution = $generalProductsDao->updateStockByProduct($arr['id_product'], $product['stock']);

            //         if (isset($resolution['info'])) break;
            //     }
            // }


            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Stock creado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Stock ya existe. Ingrese uno nuevo');
    } else {
        $stock = $dataStock['importStock'];

        $resolution = 1;

        for ($i = 0; $i < sizeof($stock); $i++) {
            if (isset($resolution['info'])) break;
            // Obtener id materia prima
            $findMaterial = $generalMaterialsDao->findMaterial($stock[$i], $id_company);
            $stock[$i]['idMaterial'] = $findMaterial['id_material'];

            // Obtener id proveedor
            $findClient = $generalClientsDao->findClientByName($stock[$i], $id_company, 2);
            $stock[$i]['idProvider'] = $findClient['id_client'];

            $findstock = $generalStockDao->findstock($stock[$i], $id_company);
            if (!$findstock)
                $resolution = $stockDao->insertStockByCompany($stock[$i], $id_company);
            else {
                $stock[$i]['idStock'] = $findstock['id_stock_material'];
                $resolution = $stockDao->updateStock($stock[$i]);
            }

            if (isset($resolution['info'])) break;
            $arr = $minimumStockDao->calcStockByMaterial($stock[$i]['idMaterial']);
            if (isset($arr['stock']))
                $resolution = $generalMaterialsDao->updateStockMaterial($stock[$i]['idMaterial'], $arr['stock']);

            if (isset($resolution['info'])) break;

            // $products = $generalProductsMaterialsDao->findAllProductByMaterial($stock[$i]['idMaterial']);

            // foreach ($products as $arr) {
            //     $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
            //     if (isset($product['stock']))
            //         $resolution = $generalProductsDao->updateStockByProduct($arr['id_product'], $product['stock']);

            //     if (isset($resolution['info'])) break;
            // }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Stock importado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateRMStock', function (Request $request, Response $response, $args) use (
    $stockDao,
    $generalStockDao,
    $generalProductsMaterialsDao,
    $generalProductsDao,
    $generalMaterialsDao,
    $minimumStockDao
) {
    $dataStock = $request->getParsedBody();

    $stock = $generalStockDao->findStock($dataStock);
    !is_array($stock) ? $data['id_stock_material'] = 0 : $data = $stock;

    if ($data['id_stock_material'] == $dataStock['idStock'] || $data['id_stock_material'] == 0) {
        $resolution = $stockDao->updateStock($dataStock);

        if ($resolution == null) {
            $arr = $minimumStockDao->calcStockByMaterial($dataStock['idMaterial']);
            if (isset($arr['stock']))
                $resolution = $generalMaterialsDao->updateStockMaterial($dataStock['idMaterial'], $arr['stock']);
        }

        // if ($resolution == null) {
        //     $products = $generalProductsMaterialsDao->findAllProductByMaterial($dataStock['idMaterial']);

        //     foreach ($products as $arr) {
        //         $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
        //         if (isset($product['stock']))
        //             $resolution = $generalProductsDao->updateStockByProduct($arr['id_product'], $product['stock']);

        //         if (isset($resolution['info'])) break;
        //     }
        // }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Stock actualizado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Stock ya existe. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

// $app->get('/deletePlanstock/{id_stock}', function (Request $request, Response $response, $args) use ($stockDao) {
//     $stock = $stockDao->deletestock($args['id_stock']);

//     if ($stock == null)
//         $resp = array('success' => true, 'message' => 'Stock eliminado correctamente');

//     if ($stock != null)
//         $resp = array('error' => true, 'message' => 'No es posible eliminar el Stock, existe información asociada a él');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });
