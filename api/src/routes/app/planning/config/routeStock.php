<?php

use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralStockDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\StockDao;

$stockDao = new StockDao();
$generalStockDao = new GeneralStockDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$generalProductsDao = new GeneralProductsDao();
$minimumStockDao = new MinimumStockDao();
$productMaterialsDao = new ProductsMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/stock', function (Request $request, Response $response, $args) use ($stockDao) {
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

$app->post('/stockDataValidation', function (Request $request, Response $response, $args) use (
    $generalStockDao,
    $generalMaterialsDao
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

$app->post('/addStock', function (Request $request, Response $response, $args) use (
    $stockDao,
    $generalStockDao,
    $generalMaterialsDao,
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

        for ($i = 0; $i < sizeof($stock); $i++) {
            if (isset($resolution['info'])) break;
            // Obtener id materia prima
            $findMaterial = $generalMaterialsDao->findMaterial($stock[$i], $id_company);
            $stock[$i]['idMaterial'] = $findMaterial['id_material'];

            $findstock = $generalStockDao->findstock($stock[$i], $id_company);
            if (!$findstock)
                $resolution = $stockDao->insertStockByCompany($stock[$i], $id_company);
            else {
                $stock[$i]['idStock'] = $findstock['id_stock'];
                $resolution = $stockDao->updateStock($stock[$i]);
            }

            if (isset($resolution['info'])) break;
            $arr = $minimumStockDao->calcStockByMaterial($stock[$i]['idMaterial']);
            if (isset($arr['stock']))
                $resolution = $generalMaterialsDao->updateStockMaterial($stock[$i]['idMaterial'], $arr['stock']);
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

$app->post('/updateStock', function (Request $request, Response $response, $args) use (
    $stockDao,
    $generalStockDao,
    $generalMaterialsDao,
    $minimumStockDao
) {
    $dataStock = $request->getParsedBody();

    $stock = $generalStockDao->findStock($dataStock);
    !is_array($stock) ? $data['id_stock'] = 0 : $data = $stock;

    if ($data['id_stock'] == $dataStock['idStock'] || $data['id_stock'] == 0) {
        $resolution = $stockDao->updateStock($dataStock);

        if ($resolution == null) {
            $arr = $minimumStockDao->calcStockByMaterial($dataStock['idMaterial']);
            if (isset($arr['stock']))
                $resolution = $generalMaterialsDao->updateStockMaterial($dataStock['idMaterial'], $arr['stock']);
        }

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
