<?php

use TezlikPlaneacion\Dao\ConversionUnitsDao;
use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\Dao\MagnitudesDao;
use TezlikPlaneacion\dao\MaterialsDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\UnitsDao;

$materialsDao = new MaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$generalOrdersDao = new GeneralOrdersDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$productsDao = new GeneralProductsDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$filterDataDao = new FilterDataDao();
$generalProductsDao = new GeneralProductsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$minimumStockDao = new MinimumStockDao();
$lastDataDao = new LastDataDao();

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

/* Consultar Materias prima importada */
$app->post('/materialsDataValidation', function (Request $request, Response $response, $args) use (
    $generalMaterialsDao,
    $magnitudesDao,
    $unitsDao
) {
    $dataMaterial = $request->getParsedBody();

    if (isset($dataMaterial)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $materials = $dataMaterial['importMaterials'];

        // Verificar duplicados
        $duplicateTracker = [];
        $dataImportMaterial = [];

        for ($i = 0; $i < count($materials); $i++) {
            if (
                empty($materials[$i]['refRawMaterial']) || empty($materials[$i]['nameRawMaterial']) ||
                empty($materials[$i]['magnitude']) || empty($materials[$i]['unit'])
            ) {
                $i = $i + 2;
                $dataImportMaterial = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }
            if (
                empty(trim($materials[$i]['refRawMaterial'])) || empty(trim($materials[$i]['nameRawMaterial'])) ||
                empty(trim($materials[$i]['magnitude'])) || empty(trim($materials[$i]['unit']))
            ) {
                $i = $i + 2;
                $dataImportMaterial = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }

            $item = $materials[$i];
            $refRawMaterial = trim($item['refRawMaterial']);
            $nameRawMaterial = trim($item['nameRawMaterial']);

            if (isset($duplicateTracker[$refRawMaterial]) || isset($duplicateTracker[$nameRawMaterial])) {
                $i = $i + 2;
                $dataImportMaterial =  array('error' => true, 'message' => "Duplicación encontrada en la fila: $i.<br>- Referencia: $refRawMaterial<br>- Material: $nameRawMaterial");
                break;
            } else {
                $duplicateTracker[$refRawMaterial] = true;
                $duplicateTracker[$nameRawMaterial] = true;
            }

            $findMaterial = $generalMaterialsDao->findMaterialByReferenceOrName($materials[$i], $id_company);

            if (sizeof($findMaterial) > 1) {
                $i = $i + 2;
                $dataImportMaterial =  array('error' => true, 'message' => "Referencia o nombre de material ya existente, fila: $i.<br>- Referencia: $refRawMaterial<br>- Material: $nameRawMaterial");
                break;
            }

            if ($findMaterial) {
                if ($findMaterial[0]['material'] != $nameRawMaterial || $findMaterial[0]['reference'] != $refRawMaterial) {
                    $i = $i + 2;
                    $dataImportMaterial =  array('error' => true, 'message' => "Referencia o nombre de material ya existente, fila: $i.<br>- Referencia: $refRawMaterial<br>- Material: $nameRawMaterial");
                    break;
                }
            }

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($materials[$i]);

            if (!$magnitude) {
                $i = $i + 2;
                $dataImportMaterial = array('error' => true, 'message' => "Magnitud no existe en la base de datos. Fila: $i");
                break;
            }

            $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($materials[$i]);

            if (!$unit) {
                $i = $i + 2;
                $dataImportMaterial = array('error' => true, 'message' => "Unidad no existe en la base de datos. Fila: $i");
                break;
            }
        }

        if (sizeof($dataImportMaterial) == 0) {
            // if (
            //     empty($materials[$i]['refRawMaterial']) || empty($materials[$i]['nameRawMaterial']) ||
            //     empty($materials[$i]['magnitude']) || empty($materials[$i]['unit'])
            // ) {
            //     $i = $i + 2;
            //     $dataImportMaterial = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
            //     break;
            // }

            // // Consultar magnitud
            // $magnitude = $magnitudesDao->findMagnitude($materials[$i]);

            // if (!$magnitude) {
            //     $i = $i + 2;
            //     $dataImportMaterial = array('error' => true, 'message' => "Magnitud no existe en la base de datos. Fila: $i");
            //     break;
            // }

            // $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // // Consultar unidad
            // $unit = $unitsDao->findUnit($materials[$i]);

            // if (!$unit) {
            //     $i = $i + 2;
            //     $dataImportMaterial = array('error' => true, 'message' => "Unidad no existe en la base de datos. Fila: $i");
            //     break;
            // }
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

    $response->getBody()->write(json_encode($dataImportMaterial, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $productsDao,
    $generalProgrammingDao,
    $generalMaterialsDao,
    $magnitudesDao,
    $unitsDao,
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

                $dataProducts = $generalProductsDao->findProductByMaterial($lastData['id_material'], $id_company);

                foreach ($dataProducts as $j) {
                    if ($j['id_product'] != 0) {
                        if (isset($materials['info'])) break;

                        // Calcular precio total materias
                        // Consultar todos los datos del producto
                        $productsMaterial = $productsMaterialsDao->findAllProductsmaterials($j['id_product'], $id_company);

                        foreach ($productsMaterial as $k) {
                            // Obtener materia prima
                            $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                            // Convertir unidades
                            $quantity = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                            $arr = $minimumStockDao->calcStockByMaterial($lastData['id_material'], $quantity);

                            if (isset($arr['stock']))
                                $materials = $generalMaterialsDao->updateStockMaterial($lastData['id_material'], $arr['stock']);
                        }
                    }
                }
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

        for ($i = 0; $i < sizeof($materials); $i++) {

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($materials[$i]);
            $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($materials[$i]);
            $materials[$i]['unit'] = $unit['id_unit'];

            $material = $generalMaterialsDao->findMaterial($materials[$i], $id_company);

            if (!$material) {
                $resolution = $materialsDao->insertMaterialsByCompany($materials[$i], $id_company);

                $lastData = $lastDataDao->lastInsertedMaterialId($id_company);
                $materials[$i]['idMaterial'] = $lastData['id_material'];
            } else {
                $materials[$i]['idMaterial'] = $material['id_material'];
                $resolution = $materialsDao->updateMaterialsByCompany($materials[$i]);
            }

            if (isset($resolution['info'])) break;
            $dataProducts = $generalProductsDao->findProductByMaterial($materials[$i]['idMaterial'], $id_company);

            foreach ($dataProducts as $j) {
                if ($j['id_product'] != 0) {
                    if (isset($resolution['info'])) break;

                    // Calcular precio total materias
                    // Consultar todos los datos del producto
                    $productsMaterial = $productsMaterialsDao->findAllProductsmaterials($j['id_product'], $id_company);

                    foreach ($productsMaterial as $k) {
                        // Obtener materia prima
                        $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                        // Convertir unidades
                        $quantity = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                        $arr = $minimumStockDao->calcStockByMaterial($materials[$i]['idMaterial'], $quantity);

                        if (isset($arr['stock']))
                            $resolution = $generalMaterialsDao->updateStockMaterial($materials[$i]['idMaterial'], $arr['stock']);
                    }
                }
            }
        }

        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Checkear cantidades
            // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);

            if ($orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FABRICADO') {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                    // Ficha tecnica
                    $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

                    if (sizeof($productsMaterials) == 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 5);
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

                    $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $productsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
                    $programming = $generalProgrammingDao->findProgrammingByOrder($orders[$i]['id_order']);

                    if (sizeof($programming) > 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 4);

                        // $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

                        foreach ($productsMaterials as $arr) {
                            $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
                            !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
                            $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
                        }
                    }
                }
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia Prima Importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao,
    $generalMaterialsDao,
    $generalOrdersDao,
    $generalProductsDao,
    $productsDao,
    $productsMaterialsDao,
    $generalProgrammingDao,
    $conversionUnitsDao,
    $minimumStockDao,
    $explosionMaterialsDao,
    $generalRMStockDao,
    $generalRequisitionsDao,
    $requisitionsDao
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
            $dataProducts = $generalProductsDao->findProductByMaterial($dataMaterial['idMaterial'], $id_company);

            foreach ($dataProducts as $j) {
                if ($j['id_product'] != 0) {
                    if (isset($resolution['info'])) break;

                    // Calcular precio total materias
                    // Consultar todos los datos del producto
                    $productsMaterial = $productsMaterialsDao->findAllProductsmaterials($j['id_product'], $id_company);

                    foreach ($productsMaterial as $k) {
                        // Obtener materia prima
                        $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                        // Convertir unidades
                        $quantity = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                        $arr = $minimumStockDao->calcStockByMaterial($dataMaterial['idMaterial'], $quantity);

                        if (isset($arr['stock']))
                            $resolution = $generalMaterialsDao->updateStockMaterial($dataMaterial['idMaterial'], $arr['stock']);
                    }
                }
            }
        }

        if ($resolution == null) {
            $arr = $explosionMaterialsDao->findAllMaterialsConsolidated($id_company);

            $materials = $explosionMaterialsDao->setDataEXMaterials($arr);

            for ($i = 0; $i < sizeof($materials); $i++) {
                if ($materials[$i]['available'] < 0) {
                    $data = [];
                    $data['idMaterial'] = $materials[$i]['id_material'];

                    $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                    $id_provider = 0;

                    if ($provider) $id_provider = $provider['id_provider'];

                    $data['idProvider'] = $id_provider;

                    $data['applicationDate'] = '';
                    $data['deliveryDate'] = '';
                    $data['requestedQuantity'] = abs($materials[$i]['available']);
                    $data['purchaseOrder'] = '';
                    $data['requestedQuantity'] = 0;

                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                    if (!$requisition)
                        $requisitionsDao->insertRequisitionByCompany($data, $id_company);
                    else {
                        $data['idRequisition'] = $requisition['id_requisition'];
                        $requisitionsDao->updateRequisition($data);
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

            if ($orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FABRICADO') {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                    // Ficha tecnica
                    $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

                    if (sizeof($productsMaterials) == 0) {
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

                    $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $productsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
                    $programming = $generalProgrammingDao->findProgrammingByOrder($orders[$i]['id_order']);
                    if (sizeof($programming) > 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 4);

                        // $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

                        foreach ($productsMaterials as $arr) {
                            $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
                            !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
                            $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
                        }
                    }
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

    if ($materials != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar el material, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
