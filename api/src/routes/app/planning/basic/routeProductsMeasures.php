<?php

use TezlikPlaneacion\dao\GeneralPMeasuresDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\ProductsMeasuresDao;

$productsMeasuresDao = new ProductsMeasuresDao();
$generalPMeasuresDao = new GeneralPMeasuresDao();
$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/productsMeasures', function (Request $request, Response $response, $args) use ($productsMeasuresDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsMeasuresDao->findAllProductsMeasuresByCompany($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos importados */
$app->post('/productsMeasuresDataValidation', function (Request $request, Response $response, $args) use (
    $generalPMeasuresDao,
    $generalProductsDao,
) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $products = $dataProduct['importProducts'];

        $dataImportProduct = [];

        for ($i = 0; $i < count($products); $i++) {
            /*  if (
                empty($products[$i]['referenceProduct']) || empty($products[$i]['product']) || empty($products[$i]['width']) ||
                empty($products[$i]['high']) || empty($products[$i]['length']) || empty($products[$i]['usefulLength']) ||
                empty($products[$i]['totalWidth']) || empty($products[$i]['window'])
            ) {
                $i = $i + 2;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }
            if (
                empty(trim($products[$i]['referenceProduct'])) || empty(trim($products[$i]['product'])) || empty(trim($products[$i]['width'])) ||
                empty(trim($products[$i]['high'])) || empty(trim($products[$i]['length'])) || empty(trim($products[$i]['usefulLength'])) ||
                empty(trim($products[$i]['totalWidth'])) || empty(trim($products[$i]['window']))
            ) {
                $i = $i + 2;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            } */

            $fieldsToCheck = ['referenceProduct', 'product', 'width', 'high', 'length', 'usefulLength', 'totalWidth', 'window'];

            foreach ($fieldsToCheck as $field) {
                if (empty(trim($products[$i][$field]))) {
                    $i += 2;
                    $dataImportProduct = [
                        'error' => true,
                        'message' => "Campos vacíos, fila: $i"
                    ];
                    break;
                }
            }

            $item = $products[$i];
            $refProduct = trim($item['referenceProduct']);
            $nameProduct = trim($item['product']);

            if (isset($duplicateTracker[$refProduct]) || isset($duplicateTracker[$nameProduct])) {
                $i = $i + 2;
                $dataImportProduct =  array('error' => true, 'message' => "Duplicación encontrada en la fila: $i.<br>- Referencia: $refProduct<br>- Producto: $nameProduct");
                break;
            } else {
                $duplicateTracker[$refProduct] = true;
                $duplicateTracker[$nameProduct] = true;
            }

            $findProduct = $generalProductsDao->findProductByReferenceOrName($products[$i], $id_company);

            if (sizeof($findProduct) > 1) {
                $i = $i + 2;
                $dataImportProduct =  array('error' => true, 'message' => "Referencia y nombre de producto ya existente, fila: $i.<br>- Referencia: $refProduct<br>- Producto: $nameProduct");
                break;
            }

            if ($findProduct) {
                if ($findProduct[0]['product'] != $nameProduct || $findProduct[0]['reference'] != $refProduct) {
                    $i = $i + 2;
                    $dataImportProduct =  array('error' => true, 'message' => "Referencia o nombre de producto ya existente, fila: $i.<br>- Referencia: $refProduct<br>- Producto: $nameProduct");
                    break;
                }
            }
        }

        $insert = 0;
        $update = 0;

        if (sizeof($dataImportProduct) == 0) {
            for ($i = 0; $i < count($products); $i++) {
                // $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);

                // if (!$findProduct) {
                //     $i = $i + 2;
                //     $dataImportProduct =  array('error' => true, 'message' => "Producto no existe en la base de datos. Fila: $i");
                //     break;
                // }
                // $products[$i]['idProduct'] = $findProduct['id_product'];

                $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);

                if (!$findProduct)
                    $insert = $insert + 1;
                else $update = $update + 1;

                $dataImportProduct['insert'] = $insert;
                $dataImportProduct['update'] = $update;
            }
        }
    } else
        $dataImportProduct = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProduct, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductMeasure', function (Request $request, Response $response, $args) use (
    $productsMeasuresDao,
    $lastDataDao,
    $productsDao,
    $generalPMeasuresDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */
    $dataProducts = sizeof($dataProduct);

    if ($dataProducts > 1) {
        $findProduct = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

        if (!$findProduct) {
            $resolution = $productsDao->insertProductByCompany($dataProduct, $id_company);

            if ($resolution == null) {
                $lastData = $lastDataDao->lastInsertedProductId($id_company);
                $dataProduct['idProduct'] = $lastData['id_product'];

                $resolution = $productsMeasuresDao->insertPMeasureByCompany($dataProduct, $id_company);
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Medida de producto creada correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
        } else {
            $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
        }
    } else {
        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {
            $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);
            $products[$i]['idProduct'] = $findProduct['id_product'];
            $products[$i]['weight'] = (floatval($products[$i]['grammage']) * floatval($products[$i]['usefulLength']) * floatval($products[$i]['totalWidth'])) / 10000000;

            $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);
            if (!$findProduct) {
                $resolution = $productsDao->insertProductByCompany($products[$i], $id_company);
                if (isset($resolution['info'])) break;

                $lastData = $lastDataDao->lastInsertedProductId($id_company);
                $products[$i]['idProduct'] = $lastData['id_product'];
            } else {
                $products[$i]['idProduct'] = $findProduct['id_product'];

                $resolution = $productsDao->updateProductByCompany($products[$i], $id_company);
            }
            if (isset($resolution['info'])) break;

            $findPMeasure = $generalPMeasuresDao->findProductMeasure($products[$i], $id_company);

            if (!$findPMeasure)
                $resolution = $productsMeasuresDao->insertPMeasureByCompany($products[$i], $id_company);
            else {
                $products[$i]['idProductMeasure'] = $findPMeasure['id_product_measure'];

                $resolution = $productsMeasuresDao->updatePMeasure($products[$i]);
            }
            if (isset($resolution['info'])) break;
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Medidas de productos importadas correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras importaba los datos. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProductMeasure', function (Request $request, Response $response, $args) use (
    $productsMeasuresDao,
    $productsDao,
    $generalProductsDao,
    $generalPMeasuresDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $status = true;
    $dataProduct = $request->getParsedBody();

    $products = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

    foreach ($products as $arr) {
        if ($arr['id_product'] != $dataProduct['idProduct']) {
            $status = false;
            break;
        }
    }

    if ($status == true) {
        $resolution = $productsDao->updateProductByCompany($dataProduct, $id_company);

        if ($resolution == null)
            $resolution = $productsMeasuresDao->updatePMeasure($dataProduct);

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Medidas de producto modificada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras guardaba los datos. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteProductMeasure/{id_product_measure}', function (Request $request, Response $response, $args) use ($productsMeasuresDao) {
    $resolution = $productsMeasuresDao->deletePMeasure($args['id_product_measure']);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Medidas de producto eliminada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras eliminaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
