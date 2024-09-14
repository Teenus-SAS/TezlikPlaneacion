<?php

use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsMaterialsDao;
use TezlikPlaneacion\dao\GeneralPStockDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\PStockDao;

$stockDao = new PStockDao();
$compositeProductsDao = new CompositeProductsDao();
$generalStockDao = new GeneralPStockDao();
$generalProductsDao = new GeneralProductsDao();
$minimumStockDao = new MinimumStockDao();
$productMaterialsDao = new ProductsMaterialsDao();
$generalProductsMaterialsDao = new GeneralProductsMaterialsDao();
$generalClientsDao = new GeneralClientsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/pStock', function (Request $request, Response $response, $args) use ($stockDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $stock = $stockDao->findAllStockByCompany($id_company);
    $response->getBody()->write(json_encode($stock));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/stockProducts', function (Request $request, Response $response, $args) use ($generalProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $generalProductsDao->findAllProductsStockByCompany($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/pStockDataValidation', function (Request $request, Response $response, $args) use (
    $generalStockDao,
    $generalProductsDao,
    $generalClientsDao
) {
    $dataStock = $request->getParsedBody();

    if (isset($dataStock)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $stock = $dataStock['importStock'];
        $dataImportStock = [];
        $debugg = [];

        for ($i = 0; $i < sizeof($stock); $i++) {
            if (
                empty($stock[$i]['referenceProduct']) || empty($stock[$i]['product']) ||
                $stock[$i]['max'] == '' || $stock[$i]['min'] == ''
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Columna vacia"));
            }

            if (
                empty(trim($stock[$i]['referenceProduct'])) || empty(trim($stock[$i]['product'])) ||
                trim($stock[$i]['max']) == '' || trim($stock[$i]['min']) == ''
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Columna vacia"));
            }

            $max = str_replace(',', '.', $stock[$i]['max']);
            $min = str_replace(',', '.', $stock[$i]['min']);

            $data = $max * $min;

            if ($data <= 0 || is_nan($data)) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: La cantidad debe ser mayor a cero (0)"));
            }

            if ($min > $max) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Tiempo minimo mayor al tiempo máximo de Producción"));
            }

            // Obtener id producto
            $findProduct = $generalProductsDao->findProduct($stock[$i], $id_company);
            if (!$findProduct) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Producto no Existe"));
            } else $stock[$i]['idProduct'] = $findProduct['id_product'];

            if (sizeof($debugg) == 0) {
                $findstock = $generalStockDao->findStock($stock[$i]);
                if (!$findstock) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportStock['insert'] = $insert;
                $dataImportStock['update'] = $update;
            }
        }
    } else
        $dataImportStock = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportStock;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPStock', function (Request $request, Response $response, $args) use (
    $stockDao,
    $generalStockDao,
    $generalMaterialsDao,
    $generalClientsDao,
    $compositeProductsDao,
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

            // if ($resolution == null) {
            //     $arr = $minimumStockDao->calcStockByMaterial($dataStock['idMaterial']);
            //     if (isset($arr['stock']))
            //         $resolution = $generalMaterialsDao->updateStockMaterial($dataStock['idMaterial'], $arr['stock']);
            // }

            if ($resolution == null) {
                $product = $generalProductsDao->findProductById($dataStock['idProduct']);

                if ($product['composite'] == 0)
                    $arr = $minimumStockDao->calcStockByProduct($dataStock['idProduct']);
                else
                    $arr = $minimumStockDao->calcStockByComposite($dataStock['idProduct']);

                if (isset($arr['stock']))
                    $resolution = $generalProductsDao->updateStockByProduct($dataStock['idProduct'], $arr['stock']);
            }

            // if ($resolution == null) {
            //     $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataStock['idProduct'], $id_company);

            //     foreach ($compositeProducts as $k) {
            //         $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

            //         $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

            //         if (isset($arr['stock']) && isset($product['stock'])) {
            //             $stock = $product['stock'] + $arr['stock'];

            //             $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
            //         }
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
            // Obtener id producto
            $findMaterial = $generalProductsDao->findProduct($stock[$i], $id_company);
            $stock[$i]['idProduct'] = $findMaterial['id_product'];

            $findstock = $generalStockDao->findstock($stock[$i], $id_company);
            if (!$findstock)
                $resolution = $stockDao->insertStockByCompany($stock[$i], $id_company);
            else {
                $stock[$i]['idStock'] = $findstock['id_stock_product'];
                $resolution = $stockDao->updateStock($stock[$i]);
            }

            if (isset($resolution['info'])) break;

            $product = $generalProductsDao->findProductById($stock[$i]['idProduct']);

            if ($product['composite'] == 0)
                $arr = $minimumStockDao->calcStockByProduct($stock[$i]['idProduct']);
            else
                $arr = $minimumStockDao->calcStockByComposite($stock[$i]['idProduct']);

            if (isset($arr['stock']))
                $resolution = $generalProductsDao->updateStockByProduct($stock[$i]['idProduct'], $arr['stock']);

            if (isset($resolution['info'])) break;

            // $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($stock[$i]['idProduct'], $id_company);

            // foreach ($compositeProducts as $k) {
            //     $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

            //     $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

            //     if (isset($arr['stock']) && isset($product['stock'])) {
            //         $stock = $product['stock'] + $arr['stock'];

            //         $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
            //     }
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

$app->post('/updatePStock', function (Request $request, Response $response, $args) use (
    $stockDao,
    $generalStockDao,
    $generalProductsMaterialsDao,
    $generalProductsDao,
    $generalMaterialsDao,
    $compositeProductsDao,
    $minimumStockDao
) {
    // session_start();
    // $id_company = $_SESSION['id_company'];
    $dataStock = $request->getParsedBody();

    $stock = $generalStockDao->findStock($dataStock);
    !is_array($stock) ? $data['id_stock_product'] = 0 : $data = $stock;

    if ($data['id_stock_product'] == $dataStock['idStock'] || $data['id_stock_product'] == 0) {
        $resolution = $stockDao->updateStock($dataStock);

        if ($resolution == null) {
            $product = $generalProductsDao->findProductById($dataStock['idProduct']);

            if ($product['composite'] == 0)
                $arr = $minimumStockDao->calcStockByProduct($dataStock['idProduct']);
            else
                $arr = $minimumStockDao->calcStockByComposite($dataStock['idProduct']);

            if (isset($arr['info'])) {
                $resolution = $arr;
            } else {
                if (isset($arr['stock']))
                    $resolution = $generalProductsDao->updateStockByProduct($dataStock['idProduct'], $arr['stock']);
            }
        }

        // if ($resolution == null) {
        //     $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataStock['idProduct'], $id_company);

        //     foreach ($compositeProducts as $k) {
        //         $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

        //         $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

        //         if (isset($arr['stock']) && isset($product['stock'])) {
        //             $stock = $product['stock'] + $arr['stock'];

        //             $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
        //         }
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
