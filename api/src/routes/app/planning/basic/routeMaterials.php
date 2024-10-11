<?php

use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\Dao\ConversionUnitsDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsMaterialsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\GeneralRequisitionsMaterialsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsProductsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\InventoryDaysDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\Dao\MagnitudesDao;
use TezlikPlaneacion\dao\MaterialsDao;
use TezlikPlaneacion\dao\MaterialsInventoryDao;
use TezlikPlaneacion\dao\MaterialsTypeDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\RequisitionsMaterialsDao;
use TezlikPlaneacion\dao\UnitsDao;

$materialsDao = new MaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$materialsTypeDao = new MaterialsTypeDao();
$materialsInventoryDao = new MaterialsInventoryDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalClientsDao = new GeneralClientsDao();
$inventoryDaysDao = new InventoryDaysDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalProductsMaterialsDao = new GeneralProductsMaterialsDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$productsDao = new GeneralProductsDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$filterDataDao = new FilterDataDao();
$generalProductsDao = new GeneralProductsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$minimumStockDao = new MinimumStockDao();
$lastDataDao = new LastDataDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$generalExMaterialsDao = new GeneralExplosionMaterialsDao();
$generalRMStockDao = new GeneralRMStockDao();
$generalRequisitionsMaterialsDao = new GeneralRequisitionsMaterialsDao();
$generalRequisitionsProductsDao = new GeneralRequisitionsProductsDao();
$requisitionsMaterialsDao = new RequisitionsMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/materials', function (Request $request, Response $response, $args) use (
    $materialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $materialsDao->findAllMaterialsByCompany($id_company);
    $response->getBody()->write(json_encode($materials));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/materialsType', function (Request $request, Response $response, $args) use (
    $materialsTypeDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $materialsTypeDao->findAllMaterialsTypeByCompany($id_company);
    $response->getBody()->write(json_encode($materials));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar Materias prima importada */
$app->post('/materialsDataValidation', function (Request $request, Response $response, $args) use (
    $generalMaterialsDao,
    $magnitudesDao,
    $unitsDao,
    $materialsTypeDao
) {
    $dataMaterial = $request->getParsedBody();

    if (isset($dataMaterial)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $materials = $dataMaterial['importMaterials'];
        $debugg = [];

        // Verificar duplicados
        $duplicateTracker = [];
        $dataImportMaterial = [];

        for ($i = 0; $i < count($materials); $i++) {
            if (
                empty($materials[$i]['refRawMaterial']) || empty($materials[$i]['nameRawMaterial']) ||
                empty($materials[$i]['magnitude']) || empty($materials[$i]['unit'])
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
            }
            if (
                empty(trim($materials[$i]['refRawMaterial'])) || empty(trim($materials[$i]['nameRawMaterial'])) ||
                empty(trim($materials[$i]['magnitude'])) || empty(trim($materials[$i]['unit']))
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
            }

            if ($_SESSION['flag_products_measure'] == '1') {
                if (empty($materials[$i]['materialType'])) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
                }
                if (empty(trim($materials[$i]['materialType']))) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
                }
            }

            $item = $materials[$i];
            $refRawMaterial = trim($item['refRawMaterial']);
            $nameRawMaterial = strtoupper(trim($item['nameRawMaterial']));

            if (isset($duplicateTracker[$refRawMaterial]) || isset($duplicateTracker[$nameRawMaterial])) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Duplicidad encontrada: $refRawMaterial, $nameRawMaterial"));
            } else {
                $duplicateTracker[$refRawMaterial] = true;
                $duplicateTracker[$nameRawMaterial] = true;
            }

            $findMaterial = $generalMaterialsDao->findMaterialByReferenceOrName($materials[$i], $id_company);

            if (sizeof($findMaterial) > 1) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Referencia o nombre de materia prima ya existente: $refRawMaterial, $nameRawMaterial"));
            }

            if ($findMaterial) {
                if ($findMaterial[0]['material'] != $nameRawMaterial || $findMaterial[0]['reference'] != $refRawMaterial) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "fila-$row: Referencia o nombre de materia prima ya existe: $refRawMaterial, $nameRawMaterial"));
                }
            }

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($materials[$i]);

            if (!$magnitude) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Magnitud no existe en la base de datos. Fila: $row"));
            } else {
                $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

                // Consultar unidad
                $unit = $unitsDao->findUnit($materials[$i]);

                if (!$unit) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Unidad no existe en la base de datos. Fila: $row"));
                }
            }

            if ($_SESSION['flag_products_measure'] == '1') {
                // Consultar tipo material
                $materialType = $materialsTypeDao->findMaterialsType($materials[$i], $id_company);

                if (!$materialType) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Fila-$row: Tipo de material no existe."));
                }
            }
        }

        if (sizeof($debugg) == 0) {
            for ($i = 0; $i < count($materials); $i++) {
                $findMaterial = $generalMaterialsDao->findMaterial($materials[$i], $id_company);
                if (!$findMaterial) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportMaterial['insert'] = $insert;
                $dataImportMaterial['update'] = $update;
            }
        }
    } else
        $dataImportMaterial = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportMaterial;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao,
    $materialsTypeDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $inventoryDaysDao,
    $generalProductsMaterialsDao,
    $productsDao,
    $generalClientsDao,
    $generalProgrammingDao,
    $generalPlanCiclesMachinesDao,
    $generalMaterialsDao,
    $materialsInventoryDao,
    $magnitudesDao,
    $unitsDao,
    $generalRequisitionsProductsDao,
    $generalProductsDao,
    $conversionUnitsDao,
    $minimumStockDao,
    $lastDataDao
) {
    session_start();
    $dataMaterial = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $dataMaterials = sizeof($dataMaterial);

    if ($dataMaterials > 1) {
        $material = $generalMaterialsDao->findMaterialByReferenceOrName($dataMaterial, $id_company);

        if (!$material) {
            $materials = $materialsDao->insertMaterialsByCompany($dataMaterial, $id_company);

            if ($materials == null) {
                $lastData = $lastDataDao->lastInsertedMaterialId($id_company);
                $dataMaterial['idMaterial'] = $lastData['id_material'];

                $materials = $materialsInventoryDao->insertMaterialInventory($dataMaterial, $id_company);
            }

            // Calcular Dias Inventario Material
            if ($materials == null) {
                if ($_SESSION['flag_products_measure'] == '1') {
                    $materials = $generalMaterialsDao->updateGrammageMaterial($dataMaterial['idMaterial'], $dataMaterial['grammage']);
                }

                $inventory = $inventoryDaysDao->calcInventoryMaterialDays($dataMaterial['idMaterial']);
                if (isset($inventory['days']))
                    $materials = $inventoryDaysDao->updateInventoryMaterialDays($dataMaterial['idMaterial'], $inventory['days']);
            }

            if ($materials == null)
                $resp = array('success' => true, 'message' => 'Materia Prima creada correctamente');
            else if (isset($materials['info']))
                $resp = array('info' => true, 'message' => $materials['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'La materia prima ya existe. Ingrese una nueva');
    } else {
        $materials = $dataMaterial['importMaterials'];
        $resolution = null;

        for ($i = 0; $i < sizeof($materials); $i++) {

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($materials[$i]);
            $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($materials[$i]);
            $materials[$i]['unit'] = $unit['id_unit'];

            if ($_SESSION['flag_products_measure'] == '1') {
                // Obtener tipo material
                $materialType = $materialsTypeDao->findMaterialsType($materials[$i], $id_company);

                $materials[$i]['idMaterialType'] = $materialType['id_material_type'];
            } else
                $materials[$i]['idMaterialType'] = 0;

            $material = $generalMaterialsDao->findMaterial($materials[$i], $id_company);

            if (!$material) {
                $resolution = $materialsDao->insertMaterialsByCompany($materials[$i], $id_company);

                $lastData = $lastDataDao->lastInsertedMaterialId($id_company);
                $materials[$i]['idMaterial'] = $lastData['id_material'];
            } else {
                $materials[$i]['idMaterial'] = $material['id_material'];
                $resolution = $materialsDao->updateMaterialsByCompany($materials[$i]);

                if ($material['unit'] != $materials[$i]['unit']) {
                    $dataProducts = $generalProductsDao->findProductByMaterial($materials[$i]['idMaterial'], $id_company);

                    foreach ($dataProducts as $j) {
                        if ($j['id_product'] != 0) {
                            if (isset($resolution['info'])) break;

                            // Calcular precio total materias
                            // Consultar todos los datos del producto
                            $productsMaterial = $productsMaterialsDao->findAllProductsMaterials($j['id_product'], $id_company);

                            foreach ($productsMaterial as $k) {
                                // Obtener materia prima
                                $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                                // Convertir unidades
                                $quantity = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                                // Guardar Unidad convertida
                                $generalProductsMaterialsDao->saveQuantityConverted($k['id_product_material'], $quantity);

                                $arr = $minimumStockDao->calcStockByMaterial($k['id_material']);

                                if (isset($arr['stock']))
                                    $resolution = $generalMaterialsDao->updateStockMaterial($k['id_material'], $arr['stock']);
                            }

                            if (isset($resolution['info'])) break;

                            // $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($j['id_product'], $id_company);

                            // foreach ($compositeProducts as $k) {
                            //     $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

                            //     $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

                            //     if (isset($arr['stock']) && isset($product['stock'])) {
                            //         $stock = $product['stock'] + $arr['stock'];

                            //         $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
                            //     }
                            // }
                        }
                    }
                }
            }

            if (isset($resolution['info'])) break;

            if ($_SESSION['flag_products_measure'] == '1') {
                !isset($materials[$i]['grammage']) ? $grammage = 0 : $grammage = $materials[$i]['grammage'];

                $resolution = $generalMaterialsDao->updateGrammageMaterial($materials[$i]['idMaterial'], $grammage);
            }

            if (isset($resolution['info'])) break;

            $inventory = $materialsInventoryDao->findMaterialInventory($materials[$i]['idMaterial']);

            if (!$inventory) {
                $resolution = $materialsInventoryDao->insertMaterialInventory($materials[$i], $id_company);
            } else {
                $resolution = $materialsInventoryDao->updateMaterialInventory($materials[$i]);
            }

            if (isset($resolution['info'])) break;

            // Calcular Dias Inventario Material 
            $inventory = $inventoryDaysDao->calcInventoryMaterialDays($materials[$i]['idMaterial']);
            if (isset($inventory['days']))
                $resolution = $inventoryDaysDao->updateInventoryMaterialDays($materials[$i]['idMaterial'], $inventory['days']);
        }

        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Checkear cantidades
            // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);

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
                    // Ficha tecnica

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

                    $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $productsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
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
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia Prima Importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao,
    $generalMaterialsDao,
    $materialsInventoryDao,
    $generalOrdersDao,
    $generalProductsDao,
    $inventoryDaysDao,
    $generalPlanCiclesMachinesDao,
    $productsDao,
    $productsMaterialsDao,
    $generalClientsDao,
    $compositeProductsDao,
    $generalProductsMaterialsDao,
    $generalProgrammingDao,
    $conversionUnitsDao,
    $minimumStockDao,
    $explosionMaterialsDao,
    $generalExMaterialsDao,
    $generalRMStockDao,
    $generalRequisitionsMaterialsDao,
    $generalRequisitionsProductsDao,
    $requisitionsMaterialsDao
) {
    session_start();
    $dataMaterial = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $status = true;
    $materials = $generalMaterialsDao->findMaterialByReferenceOrName($dataMaterial, $id_company);

    foreach ($materials as $arr) {
        if ($arr['id_material'] != $dataMaterial['idMaterial']) {
            $status = false;
            break;
        }
    }

    if ($status == true) {
        $resolution = $materialsDao->updateMaterialsByCompany($dataMaterial);

        if ($resolution == null) {
            $inventory = $materialsInventoryDao->findMaterialInventory($dataMaterial['idMaterial']);

            if (!$inventory) {
                $resolution = $materialsInventoryDao->insertMaterialInventory($dataMaterial, $id_company);
            } else {
                $resolution = $materialsInventoryDao->updateMaterialInventory($dataMaterial);
            }
        }

        if ($resolution == null) {
            if ($_SESSION['flag_products_measure'] == '1') {
                $resolution = $generalMaterialsDao->updateGrammageMaterial($dataMaterial['idMaterial'], $dataMaterial['grammage']);
            }
        }
        // Calcular Dias Inventario Material
        if ($resolution == null) {
            $inventory = $inventoryDaysDao->calcInventoryMaterialDays($dataMaterial['idMaterial']);
            if (isset($inventory['days']))
                $resolution = $inventoryDaysDao->updateInventoryMaterialDays($dataMaterial['idMaterial'], $inventory['days']);
        }

        if ($resolution == null) {
            $data = [];
            $data['referenceProduct'] = $dataMaterial['refRawMaterial'];
            $data['product'] = $dataMaterial['nameRawMaterial'];

            $product = $generalProductsDao->findProduct($data, $id_company);

            if ($product) {
                $resolution = $generalProductsDao->updateAccumulatedQuantity($product['id_product'], $dataMaterial['quantity'], 2);
            }
        }

        if ($resolution == null && $materials[0]['unit'] != $dataMaterial['unit']) {
            $dataProducts = $generalProductsDao->findProductByMaterial($dataMaterial['idMaterial'], $id_company);

            foreach ($dataProducts as $j) {
                if ($j['id_product'] != 0) {
                    if (isset($resolution['info'])) break;

                    // Calcular precio total materias
                    // Consultar todos los datos del producto
                    $productsMaterial = $productsMaterialsDao->findAllProductsMaterials($j['id_product'], $id_company);

                    foreach ($productsMaterial as $k) {
                        // Obtener materia prima
                        $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                        // Convertir unidades
                        $quantity = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                        // Guardar Unidad convertida
                        $generalProductsMaterialsDao->saveQuantityConverted($k['id_product_material'], $quantity);

                        $arr = $minimumStockDao->calcStockByMaterial($k['id_material']);

                        if (isset($arr['stock']))
                            $resolution = $generalMaterialsDao->updateStockMaterial($k['id_material'], $arr['stock']);
                    }

                    if (isset($resolution['info'])) break;

                    $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($j['id_product'], $id_company);

                    // foreach ($compositeProducts as $k) {
                    //     $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

                    //     $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

                    //     if (isset($arr['stock']) && isset($product['stock'])) {
                    //         $stock = $product['stock'] + $arr['stock'];

                    //         $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
                    //     }
                    // }
                }
            }
        }

        if ($resolution == null) {
            $arr = $generalExMaterialsDao->findAllMaterialsConsolidatedByMaterial($dataMaterial['idMaterial']);

            $materials = $generalExMaterialsDao->setDataEXMaterials($arr);

            for ($i = 0; $i < sizeof($materials); $i++) {
                $findEX = $generalExMaterialsDao->findEXMaterial($materials[$i]['id_material']);

                if (!$findEX)
                    $resolution = $explosionMaterialsDao->insertNewEXMByCompany($materials[$i], $id_company);
                else {
                    $materials[$i]['id_explosion_material'] = $findEX['id_explosion_material'];
                    $resolution = $explosionMaterialsDao->updateEXMaterials($materials[$i]);
                }

                if (intval($materials[$i]['available']) < 0) {
                    $data = [];
                    $data['idMaterial'] = $materials[$i]['id_material'];

                    $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                    $id_provider = 0;

                    if (isset($provider['id_provider'])) $id_provider = $provider['id_provider'];

                    $data['idProvider'] = $id_provider;

                    $data['numOrder'] = $materials[$i]['num_order'];
                    $data['applicationDate'] = '';
                    $data['deliveryDate'] = '';
                    $data['requiredQuantity'] = abs($materials[$i]['available']);
                    $data['purchaseOrder'] = '';
                    $data['requestedQuantity'] = 0;

                    $requisition = $generalRequisitionsMaterialsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                    if (!$requisition)
                        $generalRequisitionsMaterialsDao->insertRequisitionAutoByCompany($data, $id_company);
                    else {
                        $data['idRequisition'] = $requisition['id_requisition_material'];
                        $generalRequisitionsMaterialsDao->updateRequisitionAuto($data);
                    }
                }
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

        //         $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
        //         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
        //         $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

        //         $arr = $generalMaterialsDao->findReservedMaterial($orders[$i]['id_product']);
        //         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
        //         $generalMaterialsDao->updateReservedMaterial($orders[$i]['id_product'], $arr['reserved']);

        //         $generalMaterialsDao->updateQuantityMaterial($orders[$i]['id_product'], $accumulated_quantity, 1);
        //     }
        // }
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
                        // Verificar Material
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

                        // $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);

                    }

                    foreach ($productsMaterials as $arr) {
                        $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
                        !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
                        $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
                    }
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

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia Prima actualizada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'La materia prima ya existe. Ingrese una nueva');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteMaterial/{id_material}', function (Request $request, Response $response, $args) use (
    $materialsDao
) {
    $materials = $materialsDao->deleteMaterial($args['id_material']);
    if ($materials == null)
        $resp = array('success' => true, 'message' => 'Material eliminado correctamente');
    else if (isset($materials['info']))
        $resp = array('info' => true, 'message' => $materials['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el material, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
