<?php

use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProgrammingDao;
use TezlikPlaneacion\dao\DatesMachinesDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\FinalDateDao;
use TezlikPlaneacion\dao\GeneralExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\LotsProductsDao;
use TezlikPlaneacion\dao\MachinesDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\RequisitionsDao;
use TezlikPlaneacion\dao\StoreDao;

$programmingDao = new ProgrammingDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$lastDataDao = new LastDataDao();
$machinesDao = new MachinesDao();
$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$productsDao = new ProductsDao();
$datesMachinesDao = new DatesMachinesDao();
$finalDateDao = new FinalDateDao();
$economicLotDao = new LotsProductsDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$storeDao = new StoreDao();
$generalExMaterialsDao = new GeneralExplosionMaterialsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$generalRMStockDao = new GeneralRMStockDao();
$requisitionsDao = new RequisitionsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/*  
    // Consultar fecha inicio maquina
    $app->post('/dateMachine', function (Request $request, Response $response, $args) use ($datesMachinesDao) {
        session_start();
        $id_company = $_SESSION['id_company'];
        $dataProgramming = $request->getParsedBody();

        $datesMachines = $datesMachinesDao->findDatesMachine($dataProgramming, $id_company);
        if (!$datesMachines)
            $resp = array('nonExisting' => true);
        else
            $resp = array('existing' => true);

        $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Obtener información
    $app->post('/getProgrammingInfo', function (Request $request, Response $response, $args) use (
        $finalDateDao,
        $economicLotDao,
        $datesMachinesDao,
        $generalOrdersDao
    ) {
        session_start();
        $id_company = $_SESSION['id_company'];
        $dataProgramming = $request->getParsedBody();

        if (isset($dataProgramming['startDate'])) {
            // Insertar fechas maquina
            $datesMachinesDao->insertDatesMachine($dataProgramming, $id_company);

            // Calcular fecha final
            $finalDate = $finalDateDao->calcFinalDate($dataProgramming, $id_company);
            $dataProgramming['finalDate'] = $finalDate['final_date'];

            // Actualizar fecha final
            $finalDateDao->updateFinalDate($dataProgramming, $id_company);
        }

        // Calcular Lote economico
        $economicLot = $economicLotDao->calcEconomicLot($dataProgramming, $id_company);

        // Obtener fechas maquina
        $datesMachines = $datesMachinesDao->findDatesMachine($dataProgramming, $id_company);

        // Obtener información producto, pedido y cliente
        $orders = $generalOrdersDao->findOrdersByCompany($dataProgramming, $id_company);

        $data['economicLot'] = $economicLot['economic_lot'];
        $data['datesMachines'] = $datesMachines;
        $data['order'] = $orders;

        $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }); 
*/

$app->get('/programming', function (Request $request, Response $response, $args) use (
    $programmingDao,
    $generalProgrammingDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $storeDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $programming = $programmingDao->findAllProgrammingByCompany($id_company);

    for ($i = 0; $i < sizeof($programming); $i++) {
        $dataProgramming['id_order'] = $programming[$i]['id_order'];
        $order = $generalProgrammingDao->checkAccumulatedQuantityOrder($dataProgramming['id_order']);

        if ($order['quantity_programming'] < $order['original_quantity'])
            $dataProgramming['accumulated_quantity_order'] = $order['original_quantity'] - $order['quantity_programming'];
        else
            $dataProgramming['accumulated_quantity_order'] = 0;

        $generalOrdersDao->updateAccumulatedOrder($dataProgramming);
    }

    $programming = $programmingDao->findAllProgrammingByCompany($id_company);

    // $programming1 = $generalProgrammingDao->findAllOrdersByCompany($id_company);

    // for ($i = 0; $i < ($programming1); $i++) {
    //     $materials = $productsMaterialsDao->findAllProductsMaterials($programming1[$i]['id_product'], $id_company);

    //     $status = true;

    //     for ($j = 0; $j < sizeof($materials); $j++) {
    //         if ($materials[$j]['status'] == 0) {
    //             $status = false;
    //             break;
    //         }
    //     }

    //     if ($status == true) {
    //         $data = [];
    //         $data['idMaterial'] = $programming1[$i]['id_material'];
    //         $storeDao->saveDelivery($data, 0);
    //     }
    // }

    $response->getBody()->write(json_encode($programming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/programming/{num_order}', function (Request $request, Response $response, $args) use ($programmingDao) {
    $programming = $programmingDao->findProductsByOrders($args['num_order']);

    $response->getBody()->write(json_encode($programming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/processProgramming', function (Request $request, Response $response, $args) use ($programmingRoutesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $programming = $programmingRoutesDao->findAllProgrammingRoutes($id_company);
    $response->getBody()->write(json_encode($programming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ordersProgramming', function (Request $request, Response $response, $args) use (
    $generalProgrammingDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $orders = $generalProgrammingDao->findAllOrdersByCompany($id_company);
    $response->getBody()->write(json_encode($orders, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/statusOrder/{id_order}/{status}', function (Request $request, Response $response, $args) use (
    $generalOrdersDao
) {
    $orders = $generalOrdersDao->changeStatus($args['id_order'], $args['status']);

    if ($orders == null)
        $resp = array('success' => true);
    else if (isset($orders['info']))
        $resp = array('info' => true, 'message' => $orders['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error cambiando la informacion. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/saveProgramming', function (Request $request, Response $response, $args) use (
    $programmingDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $generalProgrammingDao,
    $generalOrdersDao,
    $generalPlanCiclesMachinesDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $generalMaterialsDao,
    $ordersDao,
    $lastDataDao,
    $generalRMStockDao,
    $explosionMaterialsDao,
    $generalExMaterialsDao,
    $requisitionsDao,
    $generalRequisitionsDao
) {
    session_start();
    $dataProgramming = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $programmings = $dataProgramming['data'];

    for ($i = 0; $i < sizeof($programmings); $i++) {
        $findProgramming = $generalProgrammingDao->findProgrammingByOrderAndProduct($programmings[$i]['id_order'], $programmings[$i]['id_product']);

        !$findProgramming ? $new = 1 : $new = 0;
        $programmings[$i]['new_programming'] = $new;

        $find = $generalProgrammingDao->findProgramming($programmings[$i]['id_programming'], $id_company);

        if (!$find || $programmings[$i]['bd_status'] == 0) {
            $result = $programmingDao->insertProgrammingByCompany($programmings[$i], $id_company);
        } else
            $result = $programmingDao->updateProgramming($programmings[$i]);

        if ($result == null) {
            $arr = $generalProgrammingRoutesDao->findProgrammingRoutes($programmings[$i]['id_product'], $programmings[$i]['id_order']);

            if ($arr) {
                $programmings[$i]['idProgrammingRoutes'] = $arr['id_programming_routes'];
                $result = $programmingRoutesDao->updateProgrammingRoutes($programmings[$i]);
            }
        }

        // if ($result == null) {
        //     $arr = $lastDataDao->findLastInsertedProgramming($id_company);
        //     $result = $generalProgrammingDao->addMinutesProgramming($arr['id_programming'], $dataProgramming['minutes']);
        // }

        if ($result == null) {
            // $order = $generalProgrammingDao->checkAccumulatedQuantityOrder($programming[$i]['order']);

            // if ($programmings[$i]['quantity_programming'] < $programmings[$i]['original_quantity'])
            //     $dataProgramming['accumulatedQuantity'] = $programmings[$i]['original_quantity'] - $programmings[$i]['quantity_programming'];
            // else
            //     $dataProgramming['accumulatedQuantity'] = 0;

            $result = $generalOrdersDao->updateAccumulatedOrder($programmings[$i]);
        }

        if ($result != null) break;
        $result = $generalOrdersDao->changeStatus($programmings[$i]['id_order'], 4);

        if ($result != null) break;
        $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($programmings[$i]['id_product'], $id_company);
        $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($programmings[$i]['id_product'], $id_company);
        $productsFTM = array_merge($productsMaterials, $compositeProducts);

        $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($programmings[$i]['id_product'], $id_company);

        if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
            $generalOrdersDao->changeStatus($programmings[$i]['id_order'], 5);
        } else {
            foreach ($productsMaterials as $k) {
                if (isset($result['info'])) break;

                $j = $generalMaterialsDao->findReservedMaterial($k['id_material']);

                !isset($j['reserved']) ? $j['reserved'] = 0 : $j;

                $result = $generalMaterialsDao->updateReservedMaterial($k['id_material'], $j['reserved']);
            }
        }

        if ($result != null) break;

        $orders = $ordersDao->findAllOrdersByCompany($id_company);

        foreach ($orders as $arr) {
            if ($arr['status'] != 'EN PRODUCCION' && $arr['status'] != 'ENTREGADO' && $arr['status'] != 'PROGRAMADO' && $arr['id_product'] == $programmings[$i]['id_product']) {
                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($arr['id_product'], $id_company);
                $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($arr['id_product'], $id_company);
                $productsFTM = array_merge($productsMaterials, $compositeProducts);

                $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($arr['id_product'], $id_company);

                if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                    $generalOrdersDao->changeStatus($arr['id_order'], 5);
                } else {
                    foreach ($productsFTM as $k) {
                        if (($k['quantity_material'] - $k['reserved']) <= 0) {
                            $result = $generalOrdersDao->changeStatus($arr['id_order'], 6);
                            break;
                        }
                    }
                }
            }
        }
    }

    if ($result == null) {
        $arr = $generalExMaterialsDao->findAllMaterialsConsolidated($id_company);
        $materials = $generalExMaterialsDao->setDataEXMaterials($arr);

        for ($i = 0; $i < sizeof($materials); $i++) {
            $findEX = $generalExMaterialsDao->findEXMaterial($materials[$i]['id_material']);

            if (!$findEX)
                $result = $explosionMaterialsDao->insertNewEXMByCompany($materials[$i], $id_company);
            else {
                $materials[$i]['id_explosion_material'] = $findEX['id_explosion_material'];
                $result = $explosionMaterialsDao->updateEXMaterials($materials[$i]);
            }

            if (intval($materials[$i]['available']) < 0) {
                $data = [];
                $data['idMaterial'] = $materials[$i]['id_material'];

                $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                $id_provider = 0;

                if ($provider) $id_provider = $provider['id_provider'];

                $data['idProvider'] = $id_provider;

                $data['applicationDate'] = '';
                $data['numOrder'] = $materials[$i]['num_order'];
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
            }
        }
    }

    if ($result == null)
        $resp = array('success' => true, 'message' => 'Programa de producción creado correctamente');
    else if (isset($result['info']))
        $resp = array('info' => true, 'message' => $result['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

// $app->post('/updateProgramming', function (Request $request, Response $response, $args) use (
//     $programmingDao,
//     $generalProgrammingDao,
//     $generalOrdersDao,
//     $productsMaterialsDao,
//     $generalMaterialsDao,
//     $ordersDao
// ) {
//     session_start();
//     $id_company = $_SESSION['id_company'];
//     $dataProgramming = $request->getParsedBody();

//     $result = $programmingDao->updateProgramming($dataProgramming);

//     if ($result == null) {
//         $order = $generalProgrammingDao->checkAccumulatedQuantityOrder($dataProgramming['order']);

//         if ($order['quantity_programming'] < $order['original_quantity'])
//             $dataProgramming['accumulatedQuantity'] = $order['original_quantity'] - $order['quantity_programming'];
//         else
//             $dataProgramming['accumulatedQuantity'] = 0;

//         $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);
//     }
//     if ($result == null)
//         $result = $generalProgrammingDao->addMinutesProgramming($dataProgramming['idProgramming'], $dataProgramming['minutes']);

//     if ($result == null) {
//         $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($dataProgramming['idProduct'], $id_company);

//         if (sizeof($productsMaterials) == 0) {
//             $generalOrdersDao->changeStatus($dataProgramming['order'], 'Sin Ficha Tecnica');
//         } else {
//             foreach ($productsMaterials as $k) {
//                 if (isset($result['info'])) break;

//                 $j = $generalMaterialsDao->findReservedMaterial($k['id_material']);

//                 !isset($j['reserved']) ? $j['reserved'] = 0 : $j;

//                 $result = $generalMaterialsDao->updateReservedMaterial($k['id_material'], $j['reserved']);
//             }
//         }
//     }

//     if ($result == null) {
//         $orders = $ordersDao->findAllOrdersByCompany($id_company);

//         foreach ($orders as $arr) {
//             if ($arr['status'] != 'En Produccion' && $arr['status'] != 'Entregado' && $arr['status'] != 'Programado' && $arr['id_product'] == $dataProgramming['idProduct']) {
//                 // Ficha tecnica
//                 $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($arr['id_product'], $id_company);

//                 if (sizeof($productsMaterials) == 0) {
//                     $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Ficha Tecnica');
//                 } else {
//                     foreach ($productsMaterials as $k) {
//                         if (($k['quantity_material'] - $k['reserved']) <= 0) {
//                             $result = $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Materia Prima');
//                             break;
//                         }
//                     }
//                 }
//             }
//         }
//     }

//     if ($result == null)
//         $resp = array('success' => true, 'message' => 'Programa de producción actualizado correctamente');
//     else if (isset($result['info']))
//         $resp = array('info' => true, 'message' => $result['message']);
//     else
//         $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });

$app->post('/deleteProgramming', function (Request $request, Response $response, $args) use (
    $programmingDao,
    $generalOrdersDao,
    $generalProgrammingDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $generalPlanCiclesMachinesDao,
    $generalMaterialsDao,
    $ordersDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    // $id_company = 1;

    $dataProgramming = $request->getParsedBody();
    $result = $programmingDao->deleteProgramming($dataProgramming['idProgramming']);

    if ($result == null)
        $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);

    if ($result == null) {
        $programming = $programmingDao->findAllProgrammingByCompany($id_company);

        if (sizeof($programming) == 0) {
            $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);
        } else {
            $order = $generalProgrammingDao->checkAccumulatedQuantityOrder($dataProgramming['order']);

            if ($order['quantity_programming'] < $order['original_quantity'])
                $dataProgramming['accumulated_quantity_order'] = $order['original_quantity'] - $order['quantity_programming'];
            else
                $dataProgramming['accumulated_quantity_order'] = '';

            $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);
        }
    }

    if ($result == null)
        $result = $generalOrdersDao->changeStatus($dataProgramming['order'], 1);

    if ($result == null) {
        $orders = $ordersDao->findAllOrdersByCompany($id_company);

        foreach ($orders as $arr) {
            if ($arr['status'] != 'EN PRODUCCION' && $arr['status'] != 'ENTREGADO' && $arr['status'] != 'PROGRAMADO') {
                // $result = $generalOrdersDao->changeStatus($arr['id_order'], 'Programar');

                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($arr['id_product'], $id_company);
                $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($arr['id_product'], $id_company);
                $productsFTM = array_merge($productsMaterials, $compositeProducts);

                $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($arr['id_product'], $id_company);

                if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                    $generalOrdersDao->changeStatus($arr['id_order'], 5);
                } else {
                    foreach ($productsFTM as $k) {
                        if (isset($result['info'])) break;

                        $j = $generalMaterialsDao->findReservedMaterial($k['id_material']);

                        !isset($j['reserved']) ? $j['reserved'] = 0 : $j;

                        $result = $generalMaterialsDao->updateReservedMaterial($k['id_material'], $j['reserved']);
                    }
                }
            }
        }
    }

    if ($result == null)
        $resp = array('success' => true, 'message' => 'Programa de produccion eliminado correctamente');
    else if (isset($programming['info']))
        $resp = array('info' => true, 'message' => $programming['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/changeStatusProgramming', function (Request $request, Response $response, $args) use ($generalProgrammingDao, $generalOrdersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProgramming = $request->getParsedBody();

    $programming = $dataProgramming['data'];

    for ($i = 0; $i < sizeof($programming); $i++) {
        $last = $generalProgrammingDao->findLastNumOPByCompany($id_company);
        $programming[$i]['numOP'] = $last['op'];
        $programming[$i]['status'] = 1;

        $result = $generalProgrammingDao->changeStatusProgramming($programming[$i]);

        if (isset($result['info'])) break;
        $result = $generalOrdersDao->changeStatus($programming[$i]['id_order'], 7);
    }

    if ($result == null)
        $resp = array('success' => true, 'message' => 'Programa de producción eliminado correctamente');
    else if (isset($result['info']))
        $resp = array('info' => true, 'message' => $result['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
