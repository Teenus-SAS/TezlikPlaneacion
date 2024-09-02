<?php

use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\Dao\ConversionUnitsDao;
use TezlikPlaneacion\dao\ConvertDataDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\Dao\GeneralCompositeProductsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsMaterialsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\GeneralSellersDao;
use TezlikPlaneacion\dao\InventoryDaysDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\Dao\MagnitudesDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\RequisitionsDao;
use TezlikPlaneacion\dao\UnitsDao;

$productsMaterialsDao = new ProductsMaterialsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$generalProductsMaterialsDao = new GeneralProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$generalProductsDao = new GeneralProductsDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new GeneralProductsDao();
$materialsDao = new GeneralMaterialsDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$inventoryDaysDao = new InventoryDaysDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$minimumStockDao = new MinimumStockDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$filterDataDao = new FilterDataDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$generalRMStockDao = new GeneralRMStockDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$requisitionsDao = new RequisitionsDao();
$generalClientsDao = new GeneralClientsDao();
$generalSellersDao = new GeneralSellersDao();
$ordersDao = new OrdersDao();
$lastDataDao = new LastDataDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productsMaterials/{idProduct}', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productMaterials = $productsMaterialsDao->findAllProductsMaterials($args['idProduct'], $id_company);

    $response->getBody()->write(json_encode($productMaterials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/allProductsMaterials', function (Request $request, Response $response, $args) use ($generalProductsMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productMaterials = $generalProductsMaterialsDao->findAllProductsMaterials($id_company);

    $response->getBody()->write(json_encode($productMaterials));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/productsMaterialsDataValidation', function (Request $request, Response $response, $args) use (
    $generalProductsMaterialsDao,
    $productsDao,
    $generalCompositeProductsDao,
    $materialsDao,
    $unitsDao,
    $magnitudesDao
) {
    $dataProductMaterial = $request->getParsedBody();

    if (isset($dataProductMaterial)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $productMaterials = $dataProductMaterial['importProducts'];

        for ($i = 0; $i < sizeof($productMaterials); $i++) {
            if (
                empty($productMaterials[$i]['referenceProduct']) || empty($productMaterials[$i]['product']) || empty($productMaterials[$i]['refRawMaterial']) ||
                empty($productMaterials[$i]['nameRawMaterial']) || $productMaterials[$i]['quantity'] == '' || empty($productMaterials[$i]['type'])
            ) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }
            if (
                empty(trim($productMaterials[$i]['referenceProduct'])) || empty(trim($productMaterials[$i]['product'])) || empty(trim($productMaterials[$i]['refRawMaterial'])) ||
                empty(trim($productMaterials[$i]['nameRawMaterial'])) || trim($productMaterials[$i]['quantity']) == '' || empty($productMaterials[$i]['type'])
            ) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            $quantity = str_replace(',', '.', $productMaterials[$i]['quantity']);

            $quantity = 1 * $quantity;

            if ($quantity <= 0 || is_nan($quantity)) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "La cantidad debe ser mayor a cero (0)<br>Fila: {$i}");
                break;
            }

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($productMaterials[$i]);

            if (!$magnitude) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Magnitud no existe en la base de datos. Fila: $i");
                break;
            }

            $productMaterials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($productMaterials[$i]);

            if (!$unit) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Unidad no existe en la base de datos. Fila: $i");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportProductsMaterials = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            $type = $productMaterials[$i]['type'];

            switch ($type) {
                case 'MATERIAL':
                    // Obtener id materia prima
                    $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
                    if (!$findMaterial) {
                        $i = $i + 2;
                        $dataImportProductsMaterials = array('error' => true, 'message' => "Materia prima no existe en la base de datos<br>Fila: {$i}");
                        break;
                    } else $productMaterials[$i]['material'] = $findMaterial['id_material'];

                    $findProductsMaterials = $generalProductsMaterialsDao->findProductMaterial($productMaterials[$i], $id_company);
                    if (!$findProductsMaterials) $insert = $insert + 1;
                    else $update = $update + 1;
                    break;
                case 'PRODUCTO':
                    // Obtener id productos compuestos
                    $arr = [];
                    $arr['referenceProduct'] = $productMaterials[$i]['refRawMaterial'];
                    $arr['product'] = $productMaterials[$i]['nameRawMaterial'];

                    $findProduct = $productsDao->findProduct($arr, $id_company);
                    if (!$findProduct) {
                        $i = $i + 2;
                        $dataImportProductsMaterials = array('error' => true, 'message' => "Producto no existe en la base de datos.<br>Fila: {$i}");
                        break;
                    }

                    if ($findProduct['composite'] == 0) {
                        $i = $i + 2;
                        $dataImportProductsMaterials = array('error' => true, 'message' => "Producto no está definido como compuesto.<br>Fila: {$i}");
                        break;
                    }

                    $productMaterials[$i]['compositeProduct'] = $findProduct['id_product'];

                    $findComposite = $generalCompositeProductsDao->findCompositeProduct($productMaterials[$i]);
                    if (!$findComposite) $insert += 1;
                    else $update += 1;
                    break;
            }

            $dataImportProductsMaterials['insert'] = $insert;
            $dataImportProductsMaterials['update'] = $update;
        }
    } else
        $dataImportProductsMaterials = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProductsMaterials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductsMaterials', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao,
    $generalProductsMaterialsDao,
    $compositeProductsDao,
    $generalCompositeProductsDao,
    $generalProductsDao,
    $generalPlanCiclesMachinesDao,
    $conversionUnitsDao,
    $explosionMaterialsDao,
    $inventoryDaysDao,
    $generalRMStockDao,
    $generalRequisitionsDao,
    $requisitionsDao,
    $productsDao,
    $materialsDao,
    $generalClientsDao,
    $generalSellersDao,
    $ordersDao,
    $lastDataDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $magnitudesDao,
    $unitsDao,
    $generalOrdersDao,
    $generalProgrammingDao,
    $minimumStockDao,
    $generalMaterialsDao,
    $filterDataDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();
    $resolution = null;
    $dataProductMaterials = sizeof($dataProductMaterial);

    if ($dataProductMaterials > 1) {

        $productMaterials = $generalProductsMaterialsDao->findProductMaterial($dataProductMaterial, $id_company);
        if (!$productMaterials) {
            $resolution = $productsMaterialsDao->insertProductsMaterialsByCompany($dataProductMaterial, $id_company);

            // Estado producto
            if ($resolution == null)
                $resolution = $generalProductsDao->updateStatusByProduct($dataProductMaterial['idProduct'], 1);

            if ($resolution == null) {
                // Consultar todos los datos del producto
                $products = $productsMaterialsDao->findAllProductsMaterials($dataProductMaterial['idProduct'], $id_company);

                foreach ($products as $arr) {
                    // Calculo Dias Inventario Materiales  
                    $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);
                    if (isset($inventory['days']))
                        $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $inventory['days']);

                    if (isset($resolution['info'])) break;

                    // Obtener materia prima
                    $material = $generalMaterialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                    // Convertir unidades
                    $quantity = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                    // Guardar Unidad convertida
                    $generalProductsMaterialsDao->saveQuantityConverted($arr['id_product_material'], $quantity);

                    $arr = $minimumStockDao->calcStockByMaterial($dataProductMaterial['material']);

                    if (isset($arr['stock']))
                        $resolution = $generalMaterialsDao->updateStockMaterial($dataProductMaterial['material'], $arr['stock']);
                }

                if ($resolution == null) {
                    $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataProductMaterial['idProduct'], $id_company);

                    foreach ($compositeProducts as $k) {
                        $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

                        if (isset($arr['stock']))
                            $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $arr['stock']);
                    }
                }
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Materia prima asignada correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras asignaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'El material ya existe. Ingrese nuevo material');
    } else {
        $productMaterials = $dataProductMaterial['importProducts'];

        $resolution = 1;

        for ($i = 0; $i < sizeof($productMaterials); $i++) {
            if (isset($resolution['info'])) break;
            // Obtener id producto
            $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($productMaterials[$i]);
            $productMaterials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($productMaterials[$i]);
            $productMaterials[$i]['unit'] = $unit['id_unit'];

            $type = $productMaterials[$i]['type'];

            switch ($type) {
                case 'MATERIAL':
                    // Obtener id materia prima
                    $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
                    $productMaterials[$i]['material'] = $findMaterial['id_material'];

                    $findProductsMaterials = $generalProductsMaterialsDao->findProductMaterial($productMaterials[$i]);

                    if (!$findProductsMaterials) $resolution = $productsMaterialsDao->insertProductsMaterialsByCompany($productMaterials[$i], $id_company);
                    else {
                        $productMaterials[$i]['idProductMaterial'] = $findProductsMaterials['id_product_material'];
                        $resolution = $productsMaterialsDao->updateProductsMaterials($productMaterials[$i]);
                    }
                    break;

                case 'PRODUCTO':
                    // Obtener id productos compuestos
                    $arr = [];
                    $arr['referenceProduct'] = $productMaterials[$i]['refRawMaterial'];
                    $arr['product'] = $productMaterials[$i]['nameRawMaterial'];

                    $findProduct = $productsDao->findProduct($arr, $id_company);
                    $productMaterials[$i]['compositeProduct'] = $findProduct['id_product'];

                    $findComposite = $generalCompositeProductsDao->findCompositeProduct($productMaterials[$i]);

                    if ($findComposite) {
                        $resolution = $compositeProductsDao->insertCompositeProductByCompany($productMaterials[$i], $id_company);
                    } else {
                        $productMaterials[$i]['idCompositeProduct'] = $findComposite['id_composite_product'];
                        $resolution = $compositeProductsDao->updateCompositeProduct($productMaterials[$i]);
                    }
                    break;
            }

            if (isset($resolution['info'])) break;

            // Consultar todos los datos del producto
            $products = $productsMaterialsDao->findAllProductsMaterials($productMaterials[$i]['idProduct'], $id_company);

            foreach ($products as $arr) {
                // Calculo Dias Inventario Materiales  
                $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);
                if (isset($inventory['days']))
                    $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $inventory['days']);

                if (isset($resolution['info'])) break;
                // Obtener materia prima
                $material = $generalMaterialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                // Convertir unidades
                $quantity = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                // Guardar Unidad convertida
                $generalProductsMaterialsDao->saveQuantityConverted($arr['id_product_material'], $quantity);

                $arr = $minimumStockDao->calcStockByMaterial($productMaterials[$i]['material']);

                if (isset($arr['stock']))
                    $resolution = $generalMaterialsDao->updateStockMaterial($productMaterials[$i]['material'], $arr['stock']);
            }
            if (isset($resolution['info'])) break;

            $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($productMaterials[$i]['idProduct'], $id_company);

            foreach ($compositeProducts as $k) {
                $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

                if (isset($arr['stock']))
                    $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $arr['stock']);
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia prima importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);

        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importada la información. Intente nuevamente');
    }

    // $products = $productsDao->updateAccumulatedQuantityGeneral($id_company);

    // // Cambiar estado pedidos 
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

    //         $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
    //         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
    //         $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

    //         $arr = $generalMaterialsDao->findReservedMaterial($orders[$i]['id_product']);
    //         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
    //         $generalMaterialsDao->updateReservedMaterial($orders[$i]['id_product'], $arr['reserved']);

    //         $generalMaterialsDao->updateQuantityMaterial($orders[$i]['id_product'], $accumulated_quantity, 1);
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

            if ($orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FABRICADO') {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {

                    if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 5);
                        $status = false;
                    } else {
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

                    $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $productsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
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
        }

        $arr = $explosionMaterialsDao->findAllMaterialsConsolidated($id_company);

        $materials = $explosionMaterialsDao->setDataEXMaterials($arr);

        for ($i = 0; $i < sizeof($materials); $i++) {
            if (intval($materials[$i]['available']) < 0) {
                $data = [];
                $data['idMaterial'] = $materials[$i]['id_material'];

                $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                $id_provider = 0;

                if ($provider) $id_provider = $provider['id_provider'];

                $data['idProvider'] = $id_provider;
                $data['applicationDate'] = '';
                $data['deliveryDate'] = '';
                $data['requiredQuantity'] = abs($materials[$i]['available']);
                $data['purchaseOrder'] = '';

                $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                if (!$requisition)
                    $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
                else {
                    $data['idRequisition'] = $requisition['id_requisition'];
                    $generalRequisitionsDao->updateRequisitionAuto($data);
                }
            } else {
                $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);
                if ($requisition) {
                    $requisitionsDao->deleteRequisition($requisition['id_requisition']);
                }
            }
        }

        $arr = $explosionMaterialsDao->findAllCompositeConsolidated($id_company);
        $products = $explosionMaterialsDao->setDataEXComposite($arr);

        for ($i = 0; $i < sizeof($products); $i++) {
            if (intval($products[$i]['available']) < 0) {
                $data = [];
                $arr2 = $generalOrdersDao->findLastNumOrder($id_company);

                $client = $generalClientsDao->findInternalClient($id_company);
                $seller = $generalSellersDao->findInternalSeller($id_company);

                if ($client && $seller) {
                    $data['order'] = $arr2['num_order'];
                    $data['dateOrder'] = date('Y-m-d');
                    $data['minDate'] = '';
                    $data['maxDate'] = '';
                    $data['idProduct'] = $products[$i]['id_child_product'];
                    $data['idClient'] = $client['id_client'];
                    $data['idSeller'] = $seller['id_seller'];
                    $data['route'] = 1;
                    $data['originalQuantity'] = abs($products[$i]['available']);

                    $findOrder = $generalOrdersDao->findLastSameOrder($data);
                    if (!$findOrder) {
                        $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                        // $generalProductsDao->updateAccumulatedQuantity($products[$i]['id_child_product'], abs($products[$i]['available']), 2);

                        if (isset($resolution['info'])) break;
                        $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                        $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($products[$i]['id_child_product'], $lastOrder['id_order']);

                        if (!$programmingRoutes) {
                            $data['idOrder'] = $lastOrder['id_order'];
                            $data['route'] = 1;

                            $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                        }
                        if (isset($resolution['info'])) break;
                    }
                }
            }
        }
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanProductsMaterials', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao,
    $conversionUnitsDao,
    $explosionMaterialsDao,
    $inventoryDaysDao,
    $generalRMStockDao,
    $compositeProductsDao,
    $generalRequisitionsDao,
    $requisitionsDao,
    $generalClientsDao,
    $generalSellersDao,
    $ordersDao,
    $generalOrdersDao,
    $generalProductsDao,
    $lastDataDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $generalProductsMaterialsDao,
    $generalMaterialsDao,
    $minimumStockDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();

    $productMaterials = $generalProductsMaterialsDao->findProductMaterial($dataProductMaterial, $id_company);
    !is_array($productMaterials) ? $data['id_productMaterial'] = 0 : $data = $productMaterials;

    if ($data['id_product_material'] == $dataProductMaterial['idProductMaterial'] || $data['id_productMaterial'] == 0) {
        // $dataProductMaterial = $convertDataDao->strReplaceProductsMaterials($dataProductMaterial);
        $resolution = $productsMaterialsDao->updateProductsMaterials($dataProductMaterial);

        if ($resolution == null) {
            // Consultar todos los datos del producto
            $products = $productsMaterialsDao->findAllProductsMaterials($dataProductMaterial['idProduct'], $id_company);

            foreach ($products as $arr) {
                // Calculo Dias Inventario Materiales  
                $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);
                if (isset($inventory['days']))
                    $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $inventory['days']);

                if (isset($resolution['info'])) break;
                // Obtener materia prima
                $material = $generalMaterialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                // Convertir unidades
                $quantity = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                // Guardar Unidad convertida
                $generalProductsMaterialsDao->saveQuantityConverted($arr['id_product_material'], $quantity);

                $arr = $minimumStockDao->calcStockByMaterial($dataProductMaterial['material']);

                if (isset($arr['stock']))
                    $resolution = $generalMaterialsDao->updateStockMaterial($dataProductMaterial['material'], $arr['stock']);
            }

            if ($resolution == null) {
                $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataProductMaterial['idProduct'], $id_company);

                foreach ($compositeProducts as $k) {
                    $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

                    if (isset($arr['stock']))
                        $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $arr['stock']);
                }
            }
        }

        if ($resolution == null) {
            $arr = $explosionMaterialsDao->findAllMaterialsConsolidated($id_company);

            $materials = $explosionMaterialsDao->setDataEXMaterials($arr);

            for ($i = 0; $i < sizeof($materials); $i++) {
                if (intval($materials[$i]['available']) < 0) {
                    $data = [];
                    $data['idMaterial'] = $materials[$i]['id_material'];

                    $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                    $id_provider = 0;

                    if ($provider) $id_provider = $provider['id_provider'];

                    $data['idProvider'] = $id_provider;
                    $data['applicationDate'] = '';
                    $data['deliveryDate'] = '';
                    $data['requiredQuantity'] = abs($materials[$i]['available']);
                    $data['purchaseOrder'] = '';
                    $data['requestedQuantity'] = 0;

                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                    if (!$requisition)
                        $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
                    else {
                        $data['idRequisition'] = $requisition['id_requisition'];
                        $generalRequisitionsDao->updateRequisitionAuto($data);
                    }
                } else {
                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);
                    if ($requisition) {
                        $requisitionsDao->deleteRequisition($requisition['id_requisition']);
                    }
                }
            }

            $arr = $explosionMaterialsDao->findAllCompositeConsolidated($id_company);
            $products = $explosionMaterialsDao->setDataEXComposite($arr);

            for ($i = 0; $i < sizeof($products); $i++) {
                if (intval($products[$i]['available']) < 0) {
                    $data = [];
                    $arr2 = $generalOrdersDao->findLastNumOrder($id_company);

                    $client = $generalClientsDao->findInternalClient($id_company);
                    $seller = $generalSellersDao->findInternalSeller($id_company);

                    if ($client && $seller) {
                        $data['order'] = $arr2['num_order'];
                        $data['dateOrder'] = date('Y-m-d');
                        $data['minDate'] = '';
                        $data['maxDate'] = '';
                        $data['idProduct'] = $products[$i]['id_child_product'];
                        $data['idClient'] = $client['id_client'];
                        $data['idSeller'] = $seller['id_seller'];
                        $data['route'] = 1;
                        $data['originalQuantity'] = abs($products[$i]['available']);

                        $findOrder = $generalOrdersDao->findLastSameOrder($data);
                        if (!$findOrder) {
                            $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                            // $generalProductsDao->updateAccumulatedQuantity($products[$i]['id_child_product'], abs($products[$i]['available']), 2);

                            if (isset($resolution['info'])) break;
                            $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                            $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($products[$i]['id_child_product'], $lastOrder['id_order']);

                            if (!$programmingRoutes) {
                                $data['idOrder'] = $lastOrder['id_order'];
                                $data['route'] = 1;

                                $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                            }
                            if (isset($resolution['info'])) break;
                        }
                    }
                }
            }
        }
        // if ($resolution == null) {
        //     $products = $generalProductsMaterialsDao->findAllProductByMaterial($dataProductMaterial['material']);

        //     foreach ($products as $arr) {
        //         $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
        //         if (isset($product['stock']))
        //             $resolution = $productsDao->updateStockByProduct($arr['id_product'], $product['stock']);

        //         if (isset($resolution['info'])) break;
        //     }
        // }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia prima actualizada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El material ya existe. Ingrese nuevo material');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deletePlanProductMaterial', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao,
    $generalProductsMaterialsDao,
    $inventoryDaysDao,
    $productsDao,
    $explosionMaterialsDao,
    $generalRMStockDao,
    $generalRequisitionsDao,
    $requisitionsDao,
    $generalClientsDao,
    $generalSellersDao,
    $ordersDao,
    $generalOrdersDao,
    $generalProductsDao,
    $lastDataDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $generalMaterialsDao,
    $minimumStockDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();
    $resolution = $productsMaterialsDao->deleteProductMaterial($dataProductMaterial['idProductMaterial']);

    if ($resolution == null) {
        $product = $generalProductsDao->findProductStatus($dataProductMaterial['idProduct']);

        $resolution = $generalProductsDao->updateStatusByProduct($dataProductMaterial['idProduct'], $product['status']);
    }

    if ($resolution == null) {
        $arr = $minimumStockDao->calcStockByMaterial($dataProductMaterial['idMaterial']);

        if (isset($arr['stock']))
            $resolution = $generalMaterialsDao->updateStockMaterial($dataProductMaterial['idMaterial'], $arr['stock']);
    }

    if ($resolution == null) {
        $arr = $explosionMaterialsDao->findAllMaterialsConsolidated($id_company);

        $materials = $explosionMaterialsDao->setDataEXMaterials($arr);

        for ($i = 0; $i < sizeof($materials); $i++) {
            if (intval($materials[$i]['available']) < 0) {
                $data = [];
                $data['idMaterial'] = $materials[$i]['id_material'];

                $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                $id_provider = 0;

                if ($provider) $id_provider = $provider['id_provider'];

                $data['idProvider'] = $id_provider;
                $data['applicationDate'] = '';
                $data['deliveryDate'] = '';
                $data['requiredQuantity'] = abs($materials[$i]['available']);
                $data['purchaseOrder'] = '';
                $data['requestedQuantity'] = 0;

                $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                if (!$requisition)
                    $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
                else {
                    $data['idRequisition'] = $requisition['id_requisition'];
                    $generalRequisitionsDao->updateRequisitionAuto($data);
                }
            } else {
                $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);
                if ($requisition) {
                    $requisitionsDao->deleteRequisition($requisition['id_requisition']);
                }
            }
        }

        $arr = $explosionMaterialsDao->findAllCompositeConsolidated($id_company);
        $products = $explosionMaterialsDao->setDataEXComposite($arr);

        for ($i = 0; $i < sizeof($products); $i++) {
            if (intval($products[$i]['available']) < 0) {
                $data = [];
                $arr2 = $generalOrdersDao->findLastNumOrder($id_company);

                $client = $generalClientsDao->findInternalClient($id_company);
                $seller = $generalSellersDao->findInternalSeller($id_company);

                if ($client && $seller) {
                    $data['order'] = $arr2['num_order'];
                    $data['dateOrder'] = date('Y-m-d');
                    $data['minDate'] = '';
                    $data['maxDate'] = '';
                    $data['idProduct'] = $products[$i]['id_child_product'];
                    $data['idClient'] = $client['id_client'];
                    $data['idSeller'] = $seller['id_seller'];
                    $data['route'] = 1;
                    $data['originalQuantity'] = abs($products[$i]['available']);

                    $findOrder = $generalOrdersDao->findLastSameOrder($data);
                    if (!$findOrder) {
                        $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                        // $generalProductsDao->updateAccumulatedQuantity($products[$i]['id_child_product'], abs($products[$i]['available']), 2);

                        if (isset($resolution['info'])) break;
                        $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                        $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($products[$i]['id_child_product'], $lastOrder['id_order']);

                        if (!$programmingRoutes) {
                            $data['idOrder'] = $lastOrder['id_order'];
                            $data['route'] = 1;

                            $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                        }
                        if (isset($resolution['info'])) break;
                    }
                }
            }
        }
    }

    // Calculo Dias Inventario Materiales  
    if ($resolution == null) {
        $products = $productsMaterialsDao->findAllProductsMaterials($dataProductMaterial['idProduct'], $id_company);

        foreach ($products as $arr) {
            $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);
            if (isset($inventory['days']))
                $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $inventory['days']);

            if (isset($resolution['info'])) break;
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Materia prima eliminada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la materia prima asignada, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/calcQuantityFTM', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $generalMaterialsDao,
    $generalProductsMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $arr = $request->getParsedBody();

    $type = $arr['type'];
    $dataMaterial = $generalMaterialsDao->findMaterialById($arr['idMaterial']);
    $dataProduct = $generalProductsDao->findProductById($arr['idProduct']);
    $weight = 0;

    switch ($type) {
        case '1': // Papel
            $weight = (floatval($dataMaterial['grammage']) * floatval($dataProduct['length']) * floatval($dataProduct['total_width'])) / 10000000;

            break;

        default:
            $quantity = floatval($arr['quantityCalc']);
            $quantityFTM = 0;

            $dataFTM = $generalProductsMaterialsDao->findProductsMaterialsByCompany($arr, $id_company);

            if ($dataFTM) $quantityFTM = $dataFTM['quantity'];

            $weight = $quantity * $quantityFTM;
            break;
    }

    $resp = ['weight' => $weight];

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
