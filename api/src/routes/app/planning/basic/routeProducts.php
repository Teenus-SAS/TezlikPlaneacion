<?php

use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\FilesDao;
use TezlikPlaneacion\dao\GeneralCategoriesDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\InvMoldsDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;

$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();
$generalOrdersDao = new GeneralOrdersDao();
$lastDataDao = new LastDataDao();
$FilesDao = new FilesDao();
$invMoldsDao = new InvMoldsDao();
$invCategoriesDao = new GeneralCategoriesDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$generalProgrammingDao = new GeneralProgrammingDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/products', function (Request $request, Response $response, $args) use ($productsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos importados */
$app->post('/productsDataValidation', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {
            if (empty($products[$i]['referenceProduct']) || empty($products[$i]['product'])) {
                $i = $i + 1;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            }

            $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);
            if (!$findProduct) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportProduct['insert'] = $insert;
            $dataImportProduct['update'] = $update;
        }
    } else
        $dataImportProduct = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProduct, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProduct', function (Request $request, Response $response, $args) use (
    $productsDao,
    $generalProductsDao,
    $lastDataDao,
    $FilesDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $generalProgrammingDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */
    $dataProducts = sizeof($dataProduct);

    if ($dataProducts > 1) {
        $product = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);
        if (!$product) {
            //INGRESA id_company, referencia, producto. BD
            $products = $productsDao->insertProductByCompany($dataProduct, $id_company);

            //ULTIMO REGISTRO DE ID, EL MÁS ALTO
            $lastProductId = $lastDataDao->lastInsertedProductId($id_company);

            if (sizeof($_FILES) > 0) $FilesDao->imageProduct($lastProductId['id_product'], $id_company);

            if ($products == null)
                $products = $generalProductsDao->updateAccumulatedQuantity($lastProductId['id_product'], $dataProduct['quantity'], 1);

            if ($products == null)
                $resp = array('success' => true, 'message' => 'Producto creado correctamente');
            else if (isset($products['info']))
                $resp = array('info' => true, 'message' => $products['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
    } else {
        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {

            $product = $generalProductsDao->findProduct($products[$i], $id_company);

            if (!$product) {
                $resolution = $productsDao->insertProductByCompany($products[$i], $id_company);
            } else {
                $products[$i]['idProduct'] = $product['id_product'];
                $resolution = $productsDao->updateProductByCompany($products[$i], $id_company);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Productos importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras importaba los datos. Intente nuevamente');
    }

    // Cambiar estado pedidos
    $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

    for ($i = 0; $i < sizeof($orders); $i++) {
        $status = true;
        // Checkear cantidades
        $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);

        if ($order['status'] != 'En Produccion' && $order['status'] = 'Entregado') {
            if ($order['original_quantity'] > $order['accumulated_quantity']) {
                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

                if (sizeof($productsMaterials) == 0) {
                    $order = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Sin Ficha Tecnica');
                    $status = false;
                } else {
                    foreach ($productsMaterials as $arr) {
                        if ($arr['quantity_material'] <= 0) {
                            $order = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Sin Materia Prima');
                            $status = false;
                            break;
                        }
                    }
                }
            }

            if ($status == true) {
                if ($order['original_quantity'] <= $order['accumulated_quantity']) {
                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Despacho');
                    $accumulated_quantity = $order['accumulated_quantity'] - $order['original_quantity'];
                } else {
                    $accumulated_quantity = $order['accumulated_quantity'];
                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Programar');
                }

                $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
                $programming = $generalProgrammingDao->findProgrammingByOrder($orders[$i]['id_order']);
                if (sizeof($programming) > 0) {
                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Programado');
                }
            }
        }
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanProduct', function (Request $request, Response $response, $args) use (
    $productsDao,
    $generalProductsDao,
    $generalOrdersDao,
    $FilesDao,
    $productsMaterialsDao,
    $generalProgrammingDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProduct = $request->getParsedBody();

    if (empty($dataProduct['referenceProduct']) || empty($dataProduct['product']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    else {
        $product = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);
        !is_array($product) ? $data['id_product'] = 0 : $data = $product;
        if ($data['id_product'] == $dataProduct['idProduct'] || $data['id_product'] == 0) {
            // Actualizar Datos, Imagen y Calcular Precio del producto
            $products = $productsDao->updateProductByCompany($dataProduct, $id_company);

            if (sizeof($_FILES) > 0)
                $products = $FilesDao->imageProduct($dataProduct['idProduct'], $id_company);

            if ($products == null) {
                $products = $generalProductsDao->updateAccumulatedQuantityGeneral($id_company);
                $products = $generalProductsDao->updateAccumulatedQuantity($dataProduct['idProduct'], $dataProduct['quantity'], 1);
            }
            // Cambiar estado pedidos
            $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

            for ($i = 0; $i < sizeof($orders); $i++) {
                $status = true;
                // Checkear cantidades
                $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);

                if ($order['status'] != 'En Produccion' && $order['status'] = 'Entregado') {
                    if ($order['original_quantity'] > $order['accumulated_quantity']) {
                        // Ficha tecnica
                        $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

                        if (sizeof($productsMaterials) == 0) {
                            $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Sin Ficha Tecnica');
                            $status = false;
                        } else {
                            foreach ($productsMaterials as $arr) {
                                if ($arr['quantity_material'] <= 0) {
                                    $order = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Sin Materia Prima');
                                    $status = false;
                                    break;
                                }
                            }
                        }
                    }

                    if ($status == true) {
                        if ($order['original_quantity'] <= $order['accumulated_quantity']) {
                            $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Despacho');
                            $accumulated_quantity = $order['accumulated_quantity'] - $order['original_quantity'];
                        } else {
                            $accumulated_quantity = $order['accumulated_quantity'];
                            $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Programar');
                        }

                        $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);

                        $programming = $generalProgrammingDao->findProgrammingByOrder($orders[$i]['id_order']);
                        if (sizeof($programming) > 0) {
                            $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Programado');
                        }
                    }
                }
            }

            if ($products == null)
                $resp = array('success' => true, 'message' => 'Producto actualizado correctamente');
            else if (isset($products['info']))
                $resp = array('info' => true, 'message' => $products['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanProduct/{id_product}', function (Request $request, Response $response, $args) use ($productsDao) {
    $product = $productsDao->deleteProduct($args['id_product']);

    if ($product == null)
        $resp = array('success' => true, 'message' => 'Producto eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el producto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
