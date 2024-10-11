<?php

use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\FilesDao;
use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralCategoriesDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralPMeasuresDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\GeneralRequisitionsProductsDao;
use TezlikPlaneacion\dao\InventoryDaysDao;
use TezlikPlaneacion\dao\InvMoldsDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\ProductsInventoryDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProductsTypeDao;

$productsDao = new ProductsDao();
$generalPMeasureDao = new GeneralPMeasuresDao();
$productsTypeDao = new ProductsTypeDao();
$productsInventoryDao = new ProductsInventoryDao();
$generalProductsDao = new GeneralProductsDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$lastDataDao = new LastDataDao();
$FilesDao = new FilesDao();
$invMoldsDao = new InvMoldsDao();
$invCategoriesDao = new GeneralCategoriesDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$inventoryDaysDao = new InventoryDaysDao();
$filterDataDao = new FilterDataDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$generalRequisitionsProductsDao = new GeneralRequisitionsProductsDao();
$generalClientsDao = new GeneralClientsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/products', function (Request $request, Response $response, $args) use (
    $productsDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsDao->findAllProductsByCompany($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos importados */
$app->post('/productsDataValidation', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $productsTypeDao
) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $products = $dataProduct['importProducts'];

        // Verificar duplicados
        // $duplicateTracker = [];
        $dataImportProduct = [];
        $debugg = [];

        for ($i = 0; $i < count($products); $i++) {
            if (
                empty($products[$i]['referenceProduct']) || empty($products[$i]['product']) || $products[$i]['quantity'] == ''
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
            }

            if (
                empty(trim($products[$i]['referenceProduct'])) || empty(trim($products[$i]['product'])) || trim($products[$i]['quantity']) == ''
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
            }

            $quantity = 1 * floatval($products[$i]['quantity']);

            if (is_nan($quantity) || $quantity < 0) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Ingrese una cantidad válida, fila: $row"));
            }

            $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);

            if (!$findProduct) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Producto no existe en la base de datos. Fila: $row"));
            }
            $products[$i]['idProduct'] = $findProduct['id_product'];

            // if ($_SESSION['flag_products_measure'] == '1') {
            //     if (empty($products[$i]['productType'])) {
            //         $i = $i + 2;
            //         $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
            //         break;
            //     }
            //     if (empty(trim($products[$i]['productType']))) {
            //         $i = $i + 2;
            //         $dataImportProduct = array('error' => true, 'message' => "Campos vacios, fila: $i");
            //         break;
            //     }
            // }

            // $item = $products[$i];
            // $refProduct = trim($item['referenceProduct']);
            // $nameProduct = trim($item['product']);

            // if (isset($duplicateTracker[$refProduct]) || isset($duplicateTracker[$nameProduct])) {
            //     $i = $i + 2;
            //     $dataImportProduct =  array('error' => true, 'message' => "Duplicación encontrada en la fila: $i.<br>- Referencia: $refProduct<br>- Producto: $nameProduct");
            //     break;
            // } else {
            //     $duplicateTracker[$refProduct] = true;
            //     $duplicateTracker[$nameProduct] = true;
            // }

            // $findProduct = $generalProductsDao->findProductByReferenceOrName($products[$i], $id_company);

            // if (sizeof($findProduct) > 1) {
            //     $i = $i + 2;
            //     $dataImportProduct =  array('error' => true, 'message' => "Referencia y nombre de producto ya existente, fila: $i.<br>- Referencia: $refProduct<br>- Producto: $nameProduct");
            //     break;
            // }

            // if ($findProduct) {
            //     if ($findProduct[0]['product'] != $nameProduct || $findProduct[0]['reference'] != $refProduct) {
            //         $i = $i + 2;
            //         $dataImportProduct =  array('error' => true, 'message' => "Referencia o nombre de producto ya existente, fila: $i.<br>- Referencia: $refProduct<br>- Producto: $nameProduct");
            //         break;
            //     }
            // }

            // if ($_SESSION['flag_products_measure'] == '1') {
            //     // Consultar tipo producto
            //     $productsType = $productsTypeDao->findProductsType($products[$i], $id_company);

            //     if (!$productsType) {
            //         $i = $i + 2;
            //         $dataImportProduct = array('error' => true, 'message' => "Tipo de producto no existe en la base de datos. Fila: $i");
            //         break;
            //     }
            // }
        }

        $insert = 0;
        $update = 0;

        if (sizeof($debugg) == 0) {
            for ($i = 0; $i < count($products); $i++) {
                $findProduct = $generalProductsDao->findProductInventory($products[$i]['idProduct'], $id_company);

                if (!$findProduct) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportProduct['insert'] = $insert;
                $dataImportProduct['update'] = $update;
            }
        }
    } else
        $dataImportProduct = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportProduct;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProduct', function (Request $request, Response $response, $args) use (
    $productsDao,
    $productsInventoryDao,
    $generalProductsDao,
    $productsTypeDao,
    $generalMaterialsDao,
    $lastDataDao,
    $FilesDao,
    $generalPlanCiclesMachinesDao,
    $generalRequisitionsProductsDao,
    $generalClientsDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $generalProgrammingDao,
    $inventoryDaysDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    // $flag_products_measure = $_SESSION['flag_products_measure'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */
    $dataProducts = sizeof($dataProduct);
    $resp = [];
    $resolution = null;

    if ($dataProducts > 1) {
        $product = $generalProductsDao->findProductInventory($dataProduct['idProduct'], $id_company);

        if (!$product) {
            $resolution = $productsInventoryDao->insertProductsInventory($dataProduct, $id_company);
        } else
            $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');

        if ($resolution == null && $dataProduct['origin'] == 1) {
            $product = $generalProductsDao->findProductById($dataProduct['idProduct']);

            if ($product) {
                $data = [];
                $data['refRawMaterial'] = $product['reference'];
                $data['nameRawMaterial'] = $product['product'];

                $material = $generalMaterialsDao->findMaterial($data, $id_company);

                if ($material)
                    $resolution = $generalMaterialsDao->updateQuantityMaterial($material['id_material'], $dataProduct['quantity']);
            }
        }

        if (sizeof($resp) == 0) {
            if ($resolution == null) {
                if (sizeof($_FILES) > 0) $FilesDao->imageProduct($dataProduct['idProduct'], $id_company);

                if ($resolution == null)
                    $resolution = $generalProductsDao->updateAccumulatedQuantity($dataProduct['idProduct'], $dataProduct['quantity'], 1);
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Producto creado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
        }
    } else {
        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {
            if (isset($resolution['info'])) break;
            $product = $generalProductsDao->findProduct($products[$i], $id_company);

            $products[$i]['idProduct'] = $product['id_product'];

            $productIn = $generalProductsDao->findProductInventory($products[$i]['idProduct'], $id_company);

            if (!$productIn) {
                $resolution = $productsInventoryDao->insertProductsInventory($products[$i], $id_company);
            } else {
                $resolution = $productsInventoryDao->updateProductsInventory($products[$i]);
                if (isset($resolution['info'])) break;
            }
            $product = $generalProductsDao->findProduct($products[$i], $id_company);

            if (isset($resolution['info'])) break;

            if ($resolution == null && $product['origin'] == 1) {
                $product = $generalProductsDao->findProductById($products[$i]['idProduct']);

                if ($product) {
                    $data = [];
                    $data['refRawMaterial'] = $product['reference'];
                    $data['nameRawMaterial'] = $product['product'];

                    $material = $generalMaterialsDao->findMaterial($data, $id_company);

                    if ($material)
                        $resolution = $generalMaterialsDao->updateQuantityMaterial($material['id_material'], $products[$i]['quantity']);
                }
            }

            if (isset($resolution['info'])) break;

            // Calculo dias inventario
            $inventory = $inventoryDaysDao->calcInventoryProductDays($products[$i]['idProduct']);

            !isset($inventory['days']) ? $days = 0 : $days = $inventory['days'];

            $resolution = $inventoryDaysDao->updateInventoryProductDays($products[$i]['idProduct'], $days);

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
        //         // $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);

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

        if ($resolution == null) {
            $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

            for ($i = 0; $i < sizeof($orders); $i++) {
                $status = true;
                // Checkear cantidades
                // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);

                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
                $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
                $productsFTM = array_merge($productsMaterials, $compositeProducts);

                $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

                // if ($orders[$i]['origin'] == 2) {
                if (
                    $orders[$i]['status'] != 'EN PRODUCCION' &&  $orders[$i]['status'] != 'FINALIZADO' &&
                    $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO'
                ) {
                    if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {

                        if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                            $orders[$i]['origin'] == 2 ? $status = 5 : $status = 13;

                            $resolution = $generalOrdersDao->changeStatus($orders[$i]['id_order'], $status);
                            $status = false;
                        } else {
                            foreach ($planCicles as $arr) {
                                // Verificar Maquina Disponible
                                if ($arr['status'] == 0) {
                                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 10);
                                    $status = false;
                                    break;
                                }
                                // Verificar Empleados
                                if ($arr['employees'] == 0) {
                                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 11);
                                    $status = false;
                                    break;
                                }
                            }

                            // Verificar Material
                            foreach ($productsFTM as $arr) {
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

                            // $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);

                        }
                    }

                    foreach ($productsMaterials as $arr) {
                        $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
                        !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
                        $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
                    }
                }
                // } else if ($orders[$i]['origin'] == 1) {
                //     if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                //         $resolution = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 13);

                //         $data = [];
                //         $data['idProduct'] = $orders[$i]['id_product'];

                //         $provider = $generalClientsDao->findInternalClient($id_company);

                //         $id_provider = 0;

                //         if (isset($provider['id_provider'])) $id_provider = $provider['id_provider'];

                //         $data['idProvider'] = $id_provider;
                //         $data['numOrder'] = $orders[$i]['num_order'];
                //         $data['applicationDate'] = '';
                //         $data['deliveryDate'] = '';
                //         $data['requiredQuantity'] = $orders[$i]['original_quantity'];
                //         $data['purchaseOrder'] = '';
                //         $data['requestedQuantity'] = 0;

                //         $requisition = $generalRequisitionsProductsDao->findRequisitionByApplicationDate($orders[$i]['id_product']);

                //         if (!$requisition)
                //             $generalRequisitionsProductsDao->insertRequisitionAutoByCompany($data, $id_company);
                //         else {
                //             $data['idRequisition'] = $requisition['id_requisition_product'];
                //             $generalRequisitionsProductsDao->updateRequisitionAuto($data);
                //         }
                //     }
                // }

                // Pedidos automaticos
                if ($orders[$i]['status'] == 'FABRICADO') {
                    $chOrders = $generalOrdersDao->findAllChildrenOrders($orders[$i]['num_order']);

                    foreach ($chOrders as $arr) {
                        $resolution = $generalOrdersDao->changeStatus($arr['id_order'], 12);
                    }
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
    $generalClientsDao,
    $generalRequisitionsProductsDao,
    $compositeProductsDao,
    $generalProgrammingDao,
    $inventoryDaysDao,
    $filterDataDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    // $flag_products_measure = $_SESSION['flag_products_measure'];

    $dataProduct = $request->getParsedBody();
    $resolution = null;

    // $status = true;

    // if ($flag_products_measure == '0') {
    //     $products = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

    //     foreach ($products as $arr) {
    //         if ($arr['id_product'] != $dataProduct['idProduct']) {
    //             $status = false;
    //             break;
    //         }
    //     }

    //     if ($status == true) {
    //         // Actualizar Datos, Imagen y Calcular Precio del producto
    //         $resolution = $productsDao->updateProductByCompany($dataProduct, $id_company);
    //     } else
    //         $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
    // }

    if ($resolution == null) {
        if (sizeof($_FILES) > 0)
            $resolution = $FilesDao->imageProduct($dataProduct['idProduct'], $id_company);

        if ($resolution == null) {
            $resolution = $productsInventoryDao->updateProductsInventory($dataProduct);
            $resolution = $generalProductsDao->updateAccumulatedQuantityGeneral($id_company);
            $resolution = $generalProductsDao->updateAccumulatedQuantity($dataProduct['idProduct'], $dataProduct['quantity'], 1);
        }

        if ($resolution == null && $dataProduct['origin'] == 1) {
            $product = $generalProductsDao->findProductById($dataProduct['idProduct']);

            if ($product) {
                $data = [];
                $data['refRawMaterial'] = $product['reference'];
                $data['nameRawMaterial'] = $product['product'];

                $material = $generalMaterialsDao->findMaterial($data, $id_company);

                if ($material)
                    $resolution = $generalMaterialsDao->updateQuantityMaterial($material['id_material'], $dataProduct['quantity']);
            }
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

        if ($resolution == null) {
            $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

            for ($i = 0; $i < sizeof($orders); $i++) {
                $status = true;
                // Checkear cantidades
                // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);
                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
                $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
                $productsFTM = array_merge($productsMaterials, $compositeProducts);

                $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

                // if ($orders[$i]['origin'] == 2) {
                if (
                    $orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FINALIZADO' &&
                    $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO'
                ) {
                    if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                        if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                            $orders[$i]['origin'] == 2 ? $status = 5 : $status = 13;

                            $generalOrdersDao->changeStatus($orders[$i]['id_order'], $status);
                            $status = false;
                        } else {
                            foreach ($planCicles as $arr) {
                                // Verificar Maquina Disponible
                                if ($arr['status'] == 0) {
                                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 10);
                                    $status = false;
                                    break;
                                }
                                // Verificar Empleados
                                if ($arr['employees'] == 0) {
                                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 11);
                                    $status = false;
                                    break;
                                }
                            }
                            // Verificar material
                            foreach ($productsFTM as $arr) {
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
                        }
                    }

                    foreach ($productsMaterials as $arr) {
                        $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
                        !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
                        $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
                    }
                }
                // } else if ($orders[$i]['origin'] == 1) {
                //     if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                //         $resolution = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 13);

                //         $data = [];
                //         $data['idProduct'] = $orders[$i]['id_product'];

                //         $provider = $generalClientsDao->findInternalClient($id_company);

                //         $id_provider = 0;

                //         if (isset($provider['id_provider'])) $id_provider = $provider['id_provider'];

                //         $data['idProvider'] = $id_provider;
                //         $data['numOrder'] = $orders[$i]['num_order'];
                //         $data['applicationDate'] = '';
                //         $data['deliveryDate'] = '';
                //         $data['requiredQuantity'] = $orders[$i]['original_quantity'];
                //         $data['purchaseOrder'] = '';
                //         $data['requestedQuantity'] = 0;

                //         $requisition = $generalRequisitionsProductsDao->findRequisitionByApplicationDate($orders[$i]['id_product']);

                //         if (!$requisition)
                //             $generalRequisitionsProductsDao->insertRequisitionAutoByCompany($data, $id_company);
                //         else {
                //             $data['idRequisition'] = $requisition['id_requisition_product'];
                //             $generalRequisitionsProductsDao->updateRequisitionAuto($data);
                //         }
                //     }
                // }
                // Pedidos automaticos
                if ($orders[$i]['status'] == 'FABRICADO') {
                    $chOrders = $generalOrdersDao->findAllChildrenOrders($orders[$i]['num_order']);

                    foreach ($chOrders as $arr) {
                        $resolution = $generalOrdersDao->changeStatus($arr['id_order'], 12);
                    }
                }
            }
        }

        if ($resolution == null) {
        }

        // Calcular Dias inventario
        if ($resolution == null) {
            $inventory = $inventoryDaysDao->calcInventoryProductDays($dataProduct['idProduct']);

            !isset($inventory['days']) ? $days = 0 : $days = $inventory['days'];

            $resolution = $inventoryDaysDao->updateInventoryProductDays($dataProduct['idProduct'], $days);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Producto actualizado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanProduct/{id_product_inventory}', function (Request $request, Response $response, $args) use (
    $productsDao,
    $productsInventoryDao,
    $generalPMeasureDao
) {
    session_start();
    // $flag_products_measure = $_SESSION['flag_products_measure'];

    // if ($flag_products_measure == '0') {
    // $product = $productsDao->deleteProduct($args['id_product']);
    // }

    $product = $productsInventoryDao->deleteProductInventory($args['id_product_inventory']);

    // if ($flag_products_measure == '1')
    //     $product = $generalPMeasureDao->deletePMeasure($args['id_product']);

    if ($product == null)
        $resp = array('success' => true, 'message' => 'Producto eliminado correctamente');
    else if (isset($product['info']))
        $resp = array('info' => true, 'message' => $product['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el producto, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
