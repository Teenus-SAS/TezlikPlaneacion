<?php

use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProgrammingDao;
use TezlikPlaneacion\dao\DatesMachinesDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\FinalDateDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\LotsProductsDao;
use TezlikPlaneacion\dao\MachinesDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\StoreDao;

$programmingDao = new ProgrammingDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$productsMaterialsDao = new ProductsMaterialsDao();
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
        $dataProgramming['order'] = $programming[$i]['id_order'];
        $order = $generalProgrammingDao->checkAccumulatedQuantityOrder($dataProgramming['order']);

        if ($order['quantity_programming'] < $order['original_quantity'])
            $dataProgramming['accumulatedQuantity'] = $order['original_quantity'] - $order['quantity_programming'];
        else
            $dataProgramming['accumulatedQuantity'] = 0;

        $generalOrdersDao->updateAccumulatedOrder($dataProgramming);
    }

    // $programming1 = $generalProgrammingDao->findAllOrdersByCompany($id_company);

    // for ($i = 0; $i < ($programming1); $i++) {
    //     $materials = $productsMaterialsDao->findAllProductsmaterials($programming1[$i]['id_product'], $id_company);

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

$app->get('/programmingByMachine/{id_machine}/{id_product}', function (Request $request, Response $response, $args) use (
    $generalProgrammingDao,
    $generalPlanCiclesMachinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $programming = $generalProgrammingDao->findAllProgrammingByMachine($args['id_machine'], $id_company);

    if ($args['id_product'] != 0) {
        $planCiclesMachines = $generalPlanCiclesMachinesDao->findPlanCiclesMachineByProductAndMachine($args['id_product'], $args['id_machine'], $id_company);

        if (!$planCiclesMachines) $programming = 1;
    }

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

$app->post('/addProgramming', function (Request $request, Response $response, $args) use (
    $programmingDao,
    $generalProgrammingDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $generalMaterialsDao,
    $ordersDao
) {
    session_start();
    $dataProgramming = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $result = $programmingDao->insertProgrammingByCompany($dataProgramming, $id_company);

    if ($result == null) {
        $order = $generalProgrammingDao->checkAccumulatedQuantityOrder($dataProgramming['order']);

        if ($order['quantity_programming'] < $order['original_quantity'])
            $dataProgramming['accumulatedQuantity'] = $order['original_quantity'] - $order['quantity_programming'];
        else
            $dataProgramming['accumulatedQuantity'] = 0;

        $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);
    }

    if ($result == null)
        $result = $generalOrdersDao->changeStatus($dataProgramming['order'], 'Programado');

    if ($result == null) {
        $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($dataProgramming['idProduct'], $id_company);

        if (sizeof($productsMaterials) == 0) {
            $generalOrdersDao->changeStatus($dataProgramming['order'], 'Sin Ficha Tecnica');
        } else {
            foreach ($productsMaterials as $k) {
                if (isset($result['info'])) break;

                $j = $generalMaterialsDao->findReservedMaterial($k['id_material']);

                !isset($j['reserved']) ? $j['reserved'] = 0 : $j;

                $result = $generalMaterialsDao->updateReservedMaterial($k['id_material'], $j['reserved']);
            }
        }
    }

    if ($result == null) {
        $orders = $ordersDao->findAllOrdersByCompany($id_company);

        foreach ($orders as $arr) {
            if ($arr['status'] != 'En Produccion' && $arr['status'] != 'Entregado' && $arr['status'] != 'Programado' && $arr['id_product'] == $dataProgramming['idProduct']) {
                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($arr['id_product'], $id_company);

                if (sizeof($productsMaterials) == 0) {
                    $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Ficha Tecnica');
                } else {
                    foreach ($productsMaterials as $k) {
                        if (($k['quantity_material'] - $k['reserved']) <= 0) {
                            $result = $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Materia Prima');
                            break;
                        }
                    }
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

$app->post('/updateProgramming', function (Request $request, Response $response, $args) use (
    $programmingDao,
    $generalProgrammingDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $generalMaterialsDao,
    $ordersDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProgramming = $request->getParsedBody();

    $result = $programmingDao->updateProgramming($dataProgramming);

    if ($result == null) {
        $order = $generalProgrammingDao->checkAccumulatedQuantityOrder($dataProgramming['order']);

        if ($order['quantity_programming'] < $order['original_quantity'])
            $dataProgramming['accumulatedQuantity'] = $order['original_quantity'] - $order['quantity_programming'];
        else
            $dataProgramming['accumulatedQuantity'] = 0;

        $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);
    }

    if ($result == null) {
        $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($dataProgramming['idProduct'], $id_company);

        if (sizeof($productsMaterials) == 0) {
            $generalOrdersDao->changeStatus($dataProgramming['order'], 'Sin Ficha Tecnica');
        } else {
            foreach ($productsMaterials as $k) {
                if (isset($result['info'])) break;

                $j = $generalMaterialsDao->findReservedMaterial($k['id_material']);

                !isset($j['reserved']) ? $j['reserved'] = 0 : $j;

                $result = $generalMaterialsDao->updateReservedMaterial($k['id_material'], $j['reserved']);
            }
        }
    }

    if ($result == null) {
        $orders = $ordersDao->findAllOrdersByCompany($id_company);

        foreach ($orders as $arr) {
            if ($arr['status'] != 'En Produccion' && $arr['status'] != 'Entregado' && $arr['status'] != 'Programado' && $arr['id_product'] == $dataProgramming['idProduct']) {
                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($arr['id_product'], $id_company);

                if (sizeof($productsMaterials) == 0) {
                    $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Ficha Tecnica');
                } else {
                    foreach ($productsMaterials as $k) {
                        if (($k['quantity_material'] - $k['reserved']) <= 0) {
                            $result = $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Materia Prima');
                            break;
                        }
                    }
                }
            }
        }
    }

    if ($result == null)
        $resp = array('success' => true, 'message' => 'Programa de producción actualizado correctamente');
    else if (isset($result['info']))
        $resp = array('info' => true, 'message' => $result['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProgramming', function (Request $request, Response $response, $args) use (
    $programmingDao,
    $generalOrdersDao,
    $generalProgrammingDao,
    $productsMaterialsDao,
    $generalMaterialsDao,
    $ordersDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProgramming = $request->getParsedBody();
    $result = $programmingDao->deleteProgramming($dataProgramming['idProgramming']);

    if ($result == null) {
        $programming = $programmingDao->findAllProgrammingByCompany($id_company);

        if (sizeof($programming) == 0) {
            $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);
        } else {
            $order = $generalProgrammingDao->checkAccumulatedQuantityOrder($dataProgramming['order']);

            if ($order['quantity_programming'] < $order['original_quantity'])
                $dataProgramming['accumulatedQuantity'] = $order['original_quantity'] - $order['quantity_programming'];
            else
                $dataProgramming['accumulatedQuantity'] = 0;

            $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);
        }
    }

    if ($result == null)
        $result = $generalOrdersDao->changeStatus($dataProgramming['order'], 'Programar');

    if ($result == null) {
        $orders = $ordersDao->findAllOrdersByCompany($id_company);

        foreach ($orders as $arr) {
            if ($arr['status'] != 'En Produccion' && $arr['status'] != 'Entregado' && $arr['status'] != 'Programado') {
                $result = $generalOrdersDao->changeStatus($arr['id_order'], 'Programar');

                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($arr['id_product'], $id_company);

                if (sizeof($productsMaterials) == 0) {
                    $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Ficha Tecnica');
                } else {
                    foreach ($productsMaterials as $k) {
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
        $resp = array('success' => true, 'message' => 'Orden de Producción eliminada correctamente');
    else if (isset($programming['info']))
        $resp = array('info' => true, 'message' => $programming['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/changeStatusProgramming', function (Request $request, Response $response, $args) use ($generalProgrammingDao, $generalOrdersDao) {
    $dataProgramming = $request->getParsedBody();
    if (isset($dataProgramming['idProgramming'])) {
        $result = $generalProgrammingDao->changeStatusProgramming($dataProgramming['idProgramming'], 1);

        $orders = $generalProgrammingDao->findProgrammingByOrder($dataProgramming['idOrder']);

        if (sizeof($orders) == 1) {
            $generalOrdersDao->changeStatus($dataProgramming['idOrder'], 'En Produccion');
        }
    } else {
        $programming = $dataProgramming['data'];

        for ($i = 0; $i < sizeof($programming); $i++) {
            $result = $generalProgrammingDao->changeStatusProgramming($programming[$i]['idProgramming'], 1);

            if (isset($result['info'])) break;
        }
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
