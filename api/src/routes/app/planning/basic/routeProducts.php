<?php

use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\FilesDao;
use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralCategoriesDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\InventoryDaysDao;
use TezlikPlaneacion\dao\InvMoldsDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\ProductsInventoryDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;

$productsDao = new ProductsDao();
$productsInventoryDao = new ProductsInventoryDao();
$generalProductsDao = new GeneralProductsDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$lastDataDao = new LastDataDao();
$FilesDao = new FilesDao();
$invMoldsDao = new InvMoldsDao();
$invCategoriesDao = new GeneralCategoriesDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$inventoryDaysDao = new InventoryDaysDao();
$filterDataDao = new FilterDataDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/products', function (Request $request, Response $response, $args) use ($productsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products));
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

        $products = $dataProduct['importProducts'];

        // Verificar duplicados
        $duplicateTracker = [];
        $dataImportProduct = [];

        for ($i = 0; $i < count($products); $i++) {
            if (
                empty($products[$i]['referenceProduct']) || empty($products[$i]['product'])
            ) {
                $i = $i + 2;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }
            if (
                empty(trim($products[$i]['referenceProduct'])) || empty(trim($products[$i]['product']))
            ) {
                $i = $i + 2;
                $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
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
                if (!$findProduct) $insert = $insert + 1;
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

$app->post('/addProduct', function (Request $request, Response $response, $args) use (
    $productsDao,
    $productsInventoryDao,
    $generalProductsDao,
    $generalMaterialsDao,
    $lastDataDao,
    $FilesDao,
    $generalPlanCiclesMachinesDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $generalProgrammingDao,
    $inventoryDaysDao
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
                $products = $productsInventoryDao->insertProductsInventory($lastProductId['id_product'], $dataProduct['quantity'], $id_company);

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
            if (isset($resolution['info'])) break;

            $product = $generalProductsDao->findProduct($products[$i], $id_company);

            if (!$product) {
                $resolution = $productsDao->insertProductByCompany($products[$i], $id_company);

                $lastProductId = $lastDataDao->lastInsertedProductId($id_company);
                $products[$i]['idProduct'] = $lastProductId['id_product'];

                if (isset($resolution['info'])) break;

                $resolution = $productsInventoryDao->insertProductsInventory($lastProductId['id_product'], $products[$i]['quantity'], $id_company);
            } else {
                $products[$i]['idProduct'] = $product['id_product'];
                $resolution = $productsDao->updateProductByCompany($products[$i], $id_company);

                $resolution = $productsInventoryDao->updateProductsInventory($products[$i]['idProduct'], $products[$i]['quantity']);
                // if ($products == null) {
                //     $products = $generalProductsDao->updateAccumulatedQuantity($dataProduct['idProduct'], $$products[$i]['quantity'], 1);
                // }

                if (isset($resolution['info'])) break;

                $inventory = $inventoryDaysDao->calcInventoryProductDays($products[$i]['idProduct']);

                !$inventory['days'] ? $days = 0 : $days = $inventory['days'];

                $resolution = $inventoryDaysDao->updateInventoryProductDays($products[$i]['idProduct'], $days);
            }

            if (isset($resolution['info'])) break;

            $resolution = $generalProductsDao->updateAccumulatedQuantity($products[$i]['idProduct'], $products[$i]['quantity'], 1);
        }

        $products = $generalProductsDao->updateAccumulatedQuantityGeneral($id_company);

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Productos importados correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras importaba los datos. Intente nuevamente');

        // Cambiar estado pedidos
        // $allOrders = $generalOrdersDao->findAllOrdersWithMaterialsByCompany($id_company);

        // foreach ($allOrders as $arr) {
        //     $status = true;
        //     if ($arr['original_quantity'] > $arr['accumulated_quantity']) {
        //         if ($arr['status_ds'] == 0) {
        //             $generalOrdersDao->changeStatus($arr['id_order'], 5);
        //             $status = false;
        //             // break;
        //         } else if ($arr['quantity_material'] <= 0) {
        //             $generalOrdersDao->changeStatus($arr['id_order'], 6);
        //             $status = false;
        //             // break;
        //         }
        //     }

        //     foreach ($allOrders as &$order) {
        //         if ((!isset($arr['status_mp']) || $arr['status_mp'] === false) && $order['id_order'] == $arr['id_order']) {
        //             // if ($order['id_order'] == $arr['id_order']) {
        //             $order['status_mp'] = $status;
        //         }
        //     }
        //     unset($order);

        //     if ($status == true && $arr['programming'] != 0) {
        //         $generalOrdersDao->changeStatus($arr['id_order'], 4);

        //         $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
        //         !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
        //         $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
        //         // $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

        //         // foreach ($productsMaterials as $arr) {
        //         //     
        //         // }

        //     }
        // }

        // $orders = $filterDataDao->filterDuplicateArray($allOrders, 'id_order');

        // for ($i = 0; $i < sizeof($orders); $i++) {
        //     if ($orders[$i]['status_mp'] == true) {
        //         if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
        //             $generalOrdersDao->changeStatus($orders[$i]['id_order'], 2);
        //             $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
        //         } else {
        //             $accumulated_quantity = $orders[$i]['accumulated_quantity'];
        //         }

        //         if ($orders[$i]['status'] != 2) {
        //             $date = date('Y-m-d');

        //             $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
        //         }

        //         $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
        //         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
        //         $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

        //         $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
        //     }
        // }

        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Checkear cantidades
            // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);

            // Ficha tecnica
            $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);
            $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

            if ($orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'PROGRAMADO') {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {

                    if (sizeof($productsMaterials) == 0 || sizeof($planCicles) == 0) {
                        $resolution = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 5);
                        $status = false;
                    } else {
                        foreach ($productsMaterials as $arr) {
                            if ($arr['quantity_material'] <= 0) {
                                $resolution = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 6);
                                $status = false;
                                break;
                            }
                        }
                    }
                }

                if ($status == true) {
                    if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 2);
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
                    } else {
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'];
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 1);
                    }

                    if ($orders[$i]['status'] != 'DESPACHO') {
                        $date = date('Y-m-d');

                        $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
                    }

                    $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
                    $programming = $generalProgrammingDao->findProgrammingByOrder($orders[$i]['id_order']);
                    if (sizeof($programming) > 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 4);

                        // $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

                    }
                }

                foreach ($productsMaterials as $arr) {
                    $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
                    !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
                    $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
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
    $generalPlanCiclesMachinesDao,
    $generalMaterialsDao,
    $generalOrdersDao,
    $productsInventoryDao,
    $FilesDao,
    $productsMaterialsDao,
    $generalProgrammingDao,
    $inventoryDaysDao,
    $filterDataDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProduct = $request->getParsedBody();

    $status = true;

    $products = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

    foreach ($products as $arr) {
        if ($arr['id_product'] != $dataProduct['idProduct']) {
            $status = false;
            break;
        }
    }

    if ($status == true) {
        // Actualizar Datos, Imagen y Calcular Precio del producto
        $products = $productsDao->updateProductByCompany($dataProduct, $id_company);

        if (sizeof($_FILES) > 0)
            $products = $FilesDao->imageProduct($dataProduct['idProduct'], $id_company);

        if ($products == null) {
            $products = $productsInventoryDao->updateProductsInventory($dataProduct['idProduct'], $dataProduct['quantity']);
            $products = $generalProductsDao->updateAccumulatedQuantityGeneral($id_company);
            $products = $generalProductsDao->updateAccumulatedQuantity($dataProduct['idProduct'], $dataProduct['quantity'], 1);
        }
        // Cambiar estado pedidos
        // $allOrders = $generalOrdersDao->findAllOrdersWithMaterialsByCompany($id_company);

        // foreach ($allOrders as $arr) {
        //     $status = true;
        //     if ($arr['original_quantity'] > $arr['accumulated_quantity']) {
        //         if ($arr['status_ds'] == 0) {
        //             $generalOrdersDao->changeStatus($arr['id_order'], 5);
        //             $status = false;
        //             // break;
        //         } else if ($arr['quantity_material'] <= 0) {
        //             $generalOrdersDao->changeStatus($arr['id_order'], 6);
        //             $status = false;
        //             // break;
        //         }
        //     }

        //     foreach ($allOrders as &$order) {
        //         if ((!isset($arr['status_mp']) || $arr['status_mp'] === false) && $order['id_order'] == $arr['id_order']) {
        //             // if ($order['id_order'] == $arr['id_order']) {
        //             $order['status_mp'] = $status;
        //         }
        //     }
        //     unset($order);

        //     if ($status == true && $arr['programming'] != 0) {
        //         $generalOrdersDao->changeStatus($arr['id_order'], 4);

        //         $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
        //         !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
        //         $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
        //     }
        // }

        // $orders = $filterDataDao->filterDuplicateArray($allOrders, 'id_order');

        // for ($i = 0; $i < sizeof($orders); $i++) {
        //     if ($orders[$i]['status_mp'] == true) {
        //         if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
        //             $generalOrdersDao->changeStatus(
        //                 $orders[$i]['id_order'],
        //                 2
        //             );
        //             $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
        //         } else {
        //             $accumulated_quantity = $orders[$i]['accumulated_quantity'];
        //         }

        //         if ($orders[$i]['status'] != 2) {
        //             $date = date('Y-m-d');

        //             $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
        //         }

        //         $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
        //         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
        //         $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

        //         $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
        //     }
        // }
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Checkear cantidades
            // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);
            // Ficha tecnica
            $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);
            $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

            if ($orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'PROGRAMADO') {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                    if (sizeof($productsMaterials) == 0 || sizeof($planCicles) == 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 5);
                        $status = false;
                    } else {
                        foreach ($productsMaterials as $arr) {
                            if ($arr['quantity_material'] <= 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 6);
                                $status = false;
                                break;
                            }
                        }
                    }
                }

                if ($status == true) {
                    if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 2);
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
                    } else {
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'];
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 1);
                    }

                    if ($orders[$i]['status'] != 'DESPACHO') {
                        $date = date('Y-m-d');

                        $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
                    }

                    $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
                    $programming = $generalProgrammingDao->findProgrammingByOrder($orders[$i]['id_order']);
                    if (sizeof($programming) > 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 4);

                        // $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

                    }
                }

                foreach ($productsMaterials as $arr) {
                    $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
                    !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
                    $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
                }
            }
        }

        // Calcular Dias inventario
        if ($products == null) {
            $inventory = $inventoryDaysDao->calcInventoryProductDays($dataProduct['idProduct']);

            !$inventory['days'] ? $days = 0 : $days = $inventory['days'];

            $products = $inventoryDaysDao->updateInventoryProductDays($dataProduct['idProduct'], $days);
        }

        if ($products == null)
            $resp = array('success' => true, 'message' => 'Producto actualizado correctamente');
        else if (isset($products['info']))
            $resp = array('info' => true, 'message' => $products['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanProduct/{id_product}', function (Request $request, Response $response, $args) use ($productsDao) {
    $product = $productsDao->deleteProduct($args['id_product']);

    if ($product == null)
        $resp = array('success' => true, 'message' => 'Producto eliminado correctamente');
    else if (isset($product['info']))
        $resp = array('info' => true, 'message' => $product['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el producto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
