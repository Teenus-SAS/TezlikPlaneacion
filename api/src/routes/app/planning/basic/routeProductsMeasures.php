<?php

use TezlikPlaneacion\Dao\GeneralCompositeProductsDao;
use TezlikPlaneacion\dao\GeneralPMeasuresDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\ProductsInventoryDao;
use TezlikPlaneacion\dao\ProductsMeasuresDao;
use TezlikPlaneacion\dao\ProductsTypeDao;

$productsMeasuresDao = new ProductsMeasuresDao();
$generalPMeasuresDao = new GeneralPMeasuresDao();
$productsTypeDao = new ProductsTypeDao();
$productsInventoryDao = new ProductsInventoryDao();
$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();
$lastDataDao = new LastDataDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();

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
    $productsTypeDao
) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];
        $flag_products_measure = $_SESSION['flag_products_measure'];

        $products = $dataProduct['importProducts'];

        $dataImportProduct = [];

        for ($i = 0; $i < count($products); $i++) {
            if (
                empty($products[$i]['referenceProduct']) || empty($products[$i]['product']) ||
                empty($products[$i]['origin']) || empty($products[$i]['composite'])
            ) {
                $i = $i + 2;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }

            if (
                empty(trim($products[$i]['referenceProduct'])) || empty(trim($products[$i]['product'])) ||
                empty(trim($products[$i]['origin'])) || empty(trim($products[$i]['composite']))
            ) {
                $i = $i + 2;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }

            if ($flag_products_measure == '1') {
                if (empty($products[$i]['productType'])) {
                    $i = $i + 2;
                    $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                    break;
                }

                if (empty(trim($products[$i]['productType']))) {
                    $i = $i + 2;
                    $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                    break;
                }

                $findPType = $productsTypeDao->findProductsType($products[$i], $id_company);

                if (!$findPType) {
                    $i = $i + 2;
                    $dataImportProduct =  array('error' => true, 'message' => "Tipo de producto no existe en la base de datos. Fila: $i");
                    break;
                }

                $origin = $products[$i]['origin'];

                switch ($origin) {
                    case 'MANUFACTURADO':
                        if (
                            empty($products[$i]['width']) || empty($products[$i]['inks']) || empty($products[$i]['high']) || empty($products[$i]['length']) ||
                            empty($products[$i]['usefulLength']) || empty($products[$i]['totalWidth']) || empty($products[$i]['window']) || empty($products[$i]['productType'])
                        ) {
                            $i = $i + 2;
                            $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                            break;
                        }

                        if (
                            empty(trim($products[$i]['width'])) || empty(trim($products[$i]['inks'])) || empty(trim($products[$i]['high'])) || empty(trim($products[$i]['length'])) ||
                            empty(trim($products[$i]['usefulLength'])) || empty(trim($products[$i]['totalWidth'])) || empty(trim($products[$i]['window'])) || empty(trim($products[$i]['productType']))
                        ) {
                            $i = $i + 2;
                            $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                            break;
                        }

                        $data = floatval($products[$i]['width']) * floatval($products[$i]['high']) * floatval($products[$i]['length']) * floatval($products[$i]['usefulLength']) *
                            floatval($products[$i]['totalWidth']) * floatval($products[$i]['window']) * floatval($products[$i]['inks']);

                        if (is_nan($data) || $data <= 0) {
                            $i = $i + 2;
                            $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                            break;
                        }
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
    $productsTypeDao,
    $productsDao,
    $generalPMeasuresDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag_products_measure = $_SESSION['flag_products_measure'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */
    $dataProducts = sizeof($dataProduct);

    if ($dataProducts > 1) {
        $findProduct = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

        if (!$findProduct) {
            $resolution = $productsDao->insertProductByCompany($dataProduct, $id_company);

            if ($resolution == null && $flag_products_measure == '1') {
                $lastData = $lastDataDao->lastInsertedProductId($id_company);
                $dataProduct['idProduct'] = $lastData['id_product'];

                $resolution = $productsMeasuresDao->insertPMeasureByCompany($dataProduct, $id_company);
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Producto creado correctamente');
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

            $products[$i]['origin'] == 'COMERCIALIZADO' ? $products[$i]['origin'] = 1 : $products[$i]['origin'] = 2;
            $products[$i]['composite'] == 'SI' ? $products[$i]['composite'] = 1 : $products[$i]['composite'] = 0;

            if ($flag_products_measure == '1') {
                $findPType = $productsTypeDao->findProductsType($products[$i], $id_company);
                $products[$i]['idProductType'] = $findPType['id_product_type'];
            } else $products[$i]['idProductType'] = 0;

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

            $resolution = $generalProductsDao->changeCompositeProducts($products[$i]['idProduct'], $products[$i]['composite']);

            if (isset($resolution['info'])) break;

            if ($flag_products_measure == '1') {
                $findPMeasure = $generalPMeasuresDao->findProductMeasure($products[$i], $id_company);

                if (!$findPMeasure)
                    $resolution = $productsMeasuresDao->insertPMeasureByCompany($products[$i], $id_company);
                else {
                    $products[$i]['idProductMeasure'] = $findPMeasure['id_product_measure'];

                    $resolution = $productsMeasuresDao->updatePMeasure($products[$i]);
                }
            }
            if (isset($resolution['info'])) break;
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Productos importadas correctamente');
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
    $flag_products_measure = $_SESSION['flag_products_measure'];
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

        if ($resolution == null && $flag_products_measure == '1')
            $resolution = $productsMeasuresDao->updatePMeasure($dataProduct);

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Producto actualizado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras guardaba los datos. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProductMeasure', function (Request $request, Response $response, $args) use (
    $productsDao,
    $productsInventoryDao,
    $productsMeasuresDao
) {
    session_start();
    $dataProduct = $request->getParsedBody();
    $flag_products_measure = $_SESSION['flag_products_measure'];
    $resolution = null;

    if ($flag_products_measure == '1') {
        $resolution = $productsMeasuresDao->deletePMeasure($dataProduct['idProductMeasure']);
        if ($resolution == null)
            $resolution = $productsDao->deleteProduct($dataProduct['idProduct']);
        if ($resolution == null)
            $resolution = $productsInventoryDao->deleteProductInventory($dataProduct['idProduct']);
    } else {
        $resolution = $productsDao->deleteProduct($dataProduct['idProduct']);
        if ($resolution == null)
            $resolution = $productsInventoryDao->deleteProductInventory($dataProduct['idProduct']);
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Producto eliminado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras eliminaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Cambiar Producto Compuesto */
$app->get('/changeComposite/{id_product}/{op}', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $generalCompositeProductsDao
) {
    $status = true;

    if ($args['op'] == 0) {
        $product = $generalCompositeProductsDao->findCompositeProductByChild($args['id_product']);
        if (sizeof($product) > 0)
            $status = false;
    }

    if ($status == true) {
        $product = $generalProductsDao->changeCompositeProducts($args['id_product'], $args['op']);

        if ($product == null)
            $resp = array('success' => true, 'message' => 'Producto modificado correctamente');
        else if (isset($product['info']))
            $resp = array('info' => true, 'message' => $product['message']);
        else
            $resp = array('error' => true, 'message' => 'No se pudo modificar la información. Intente de nuevo');
    } else
        $resp = array(
            'error' => true,
            'message' => 'No se pudo desactivar el producto. Tiene datos relacionados a él'
        );

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
