<?php

use TezlikPlaneacion\dao\CiclesMachineDao;
use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\dao\GeneralMachinesDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProcessDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\LotsProductsDao;
use TezlikPlaneacion\dao\PlanCiclesMachineDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;

$planCiclesMachineDao = new PlanCiclesMachineDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$lastDataDao = new LastDataDao();
$ciclesMachinesDao = new CiclesMachineDao();
$processDao = new GeneralProcessDao();
$machinesDao = new GeneralMachinesDao();
$productsDao = new GeneralProductsDao();
$economicLotDao = new LotsProductsDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/planCiclesMachine', function (Request $request, Response $response, $args) use (
    $planCiclesMachineDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $planCiclesMachine = $planCiclesMachineDao->findAllPlanCiclesMachine($id_company);
    $response->getBody()->write(json_encode($planCiclesMachine, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/planCiclesMachine/{id_product}', function (Request $request, Response $response, $args) use (
    $generalPlanCiclesMachinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $planCiclesMachine = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($args['id_product'], $id_company);

    $response->getBody()->write(json_encode($planCiclesMachine, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/routesCiclesMachine/{id_product}', function (Request $request, Response $response, $args) use (
    $generalPlanCiclesMachinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $planCiclesMachine = $generalPlanCiclesMachinesDao->findAllRoutes($args['id_product'], $id_company);

    $response->getBody()->write(json_encode($planCiclesMachine, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// $app->get('/planCiclesMachine/{id_product}/{id_machine}', function (Request $request, Response $response, $args) use (
//     $generalPlanCiclesMachinesDao
// ) {
//     session_start();
//     $id_company = $_SESSION['id_company'];

//     $planCiclesMachine = $generalPlanCiclesMachinesDao->findPlanCiclesMachineByProductAndMachine($args['id_product'], $args['id_machine'], $id_company);

//     $response->getBody()->write(json_encode($planCiclesMachine, JSON_NUMERIC_CHECK));
//     return $response->withHeader('Content-Type', 'application/json');
// });

$app->post('/planCiclesMachineDataValidation', function (Request $request, Response $response, $args) use (
    $generalPlanCiclesMachinesDao,
    $processDao,
    $machinesDao,
    $productsDao
) {
    $dataPlanCiclesMachine = $request->getParsedBody();

    if (isset($dataPlanCiclesMachine['importPlanCiclesMachine'])) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $planCiclesMachine = $dataPlanCiclesMachine['importPlanCiclesMachine'];

        for ($i = 0; $i < sizeof($planCiclesMachine); $i++) {
            if (
                empty($planCiclesMachine[$i]['referenceProduct']) || empty($planCiclesMachine[$i]['product']) || empty($planCiclesMachine[$i]['process']) ||
                empty($planCiclesMachine[$i]['machine']) || empty($planCiclesMachine[$i]['ciclesHour'])
            ) {
                $i = $i + 2;
                $dataImportPlanCiclesMachine = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            if (
                empty(trim($planCiclesMachine[$i]['referenceProduct'])) || empty(trim($planCiclesMachine[$i]['product'])) || empty(trim($planCiclesMachine[$i]['process'])) ||
                empty(trim($planCiclesMachine[$i]['machine'])) || empty(trim($planCiclesMachine[$i]['ciclesHour']))
            ) {
                $i = $i + 2;
                $dataImportPlanCiclesMachine = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($planCiclesMachine[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportPlanCiclesMachine = array('error' => true, 'message' => "No existe el producto en la base de datos<br>Fila: {$i}");
                break;
            } else $planCiclesMachine[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id proceso
            $findProcess = $processDao->findProcess($planCiclesMachine[$i], $id_company);
            if (!$findProcess) {
                $i = $i + 2;
                $dataImportPlanCiclesMachine = array('error' => true, 'message' => "No existe el proceso en la base de datos<br>Fila: {$i}");
                break;
            } else $planCiclesMachine[$i]['idProcess'] = $findProcess['id_process'];

            // Obtener id maquina 
            // Si no está definida agrega 0 a 'idMachine'
            if (!isset($planCiclesMachine[$i]['machine']) || strtoupper(trim($planCiclesMachine[$i]['machine'])) == 'PROCESO MANUAL') {
                $planCiclesMachine[$i]['idMachine'] = 0;
            } else {
                $findMachine = $machinesDao->findMachine($planCiclesMachine[$i], $id_company);
                if (!$findMachine) {
                    $i = $i + 2;
                    $dataImportPlanCiclesMachine = array('error' => true, 'message' => "Maquina no existe en la base de datos <br>Fila: {$i}");
                    break;
                } else $planCiclesMachine[$i]['idMachine'] = $findMachine['id_machine'];
            }

            $findPlanCiclesMachine = $generalPlanCiclesMachinesDao->findPlansCiclesMachine($planCiclesMachine[$i], $id_company);

            if (!$findPlanCiclesMachine) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportPlanCiclesMachine['insert'] = $insert;
            $dataImportPlanCiclesMachine['update'] = $update;
        }
    } else $dataImportPlanCiclesMachine = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportPlanCiclesMachine, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPlanCiclesMachine', function (Request $request, Response $response, $args) use (
    $planCiclesMachineDao,
    $generalPlanCiclesMachinesDao,
    $ciclesMachinesDao,
    $generalProgrammingDao,
    $generalMaterialsDao,
    $lastDataDao,
    $productsDao,
    $processDao,
    $machinesDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPlanCiclesMachine = $request->getParsedBody();

    $dataPlanCiclesMachines = sizeof($dataPlanCiclesMachine);

    if ($dataPlanCiclesMachines > 1) {
        $findPlanCiclesMachine = $generalPlanCiclesMachinesDao->findPlanCiclesMachineByProductAndMachine($dataPlanCiclesMachine['idProduct'], $dataPlanCiclesMachine['idMachine'], $dataPlanCiclesMachine['idProcess']);

        if (!$findPlanCiclesMachine) {
            $planCiclesMachine = $planCiclesMachineDao->addPlanCiclesMachines($dataPlanCiclesMachine, $id_company);

            if ($planCiclesMachine == null) {
                $data = [];
                $machine = $lastDataDao->findLastInsertedCiclesMachine($id_company);
                $data['idCiclesMachine'] = $machine['id_cicles_machine'];

                // Agregar ruta siguiente
                $arr = $generalPlanCiclesMachinesDao->findNextRouteByProduct($dataPlanCiclesMachine['idProduct']);
                $data['route'] = $arr['route'];
                $planCiclesMachine = $generalPlanCiclesMachinesDao->changeRouteById($data);

                if ($planCiclesMachine == null) {
                    // Calcular unidades
                    $arr = $ciclesMachinesDao->calcUnitsTurn($machine['id_cicles_machine']);
                    $data['units_turn'] = $arr['units_turn'];

                    $arr = $ciclesMachinesDao->calcUnitsMonth($data, 2);
                    $data['units_month'] = $arr['units_month'];

                    $planCiclesMachine = $generalPlanCiclesMachinesDao->updateUnits($data);
                }
            }

            if ($planCiclesMachine == null) {
                $ciclesMachine = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($dataPlanCiclesMachine['idProduct'], $id_company);

                if (sizeof($ciclesMachine) == 1) {
                    $arr = $generalProgrammingRoutesDao->findProgrammingRoutesByProduct($dataPlanCiclesMachine['idProduct']);

                    if (!$arr) {
                        $data = [];
                        $data['idProduct'] = $dataPlanCiclesMachine['idProduct'];
                        $data['idOrder'] = 0;
                        $data['route'] = 1;

                        $planCiclesMachine = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                    }
                }
            }

            if ($planCiclesMachine == null)
                $resp = array('success' => true, 'message' => 'Ciclo de maquina agregado correctamente');
            else if (isset($planCiclesMachine['info']))
                $resp = array('info' => true, 'message' => $planCiclesMachine['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error al agregar el ciclo de maquina. Intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Ciclo de maquina existente. Ingrese uno nuevo');
    } else {
        $planCiclesMachine = $dataPlanCiclesMachine['importPlanCiclesMachine'];

        $resolution = 1;

        for ($i = 0; $i < sizeof($planCiclesMachine); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($planCiclesMachine[$i], $id_company);
            $planCiclesMachine[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id proceso
            $findProcess = $processDao->findProcess($planCiclesMachine[$i], $id_company);
            $planCiclesMachine[$i]['idProcess'] = $findProcess['id_process'];

            // Obtener id maquina
            if (!isset($planCiclesMachine[$i]['machine']) || strtoupper(trim($planCiclesMachine[$i]['machine'])) == 'PROCESO MANUAL') {
                $planCiclesMachine[$i]['idMachine'] = 0;
            } else {
                $findMachine = $machinesDao->findMachine($planCiclesMachine[$i], $id_company);
                $planCiclesMachine[$i]['idMachine'] = $findMachine['id_machine'];
            }

            $findPlanCiclesMachine = $generalPlanCiclesMachinesDao->findPlansCiclesMachine($planCiclesMachine[$i], $id_company);
            if (!$findPlanCiclesMachine) {
                $resolution = $planCiclesMachineDao->addPlanCiclesMachines($planCiclesMachine[$i], $id_company);

                if (isset($resolution['info'])) break;

                $ciclesMachine = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($planCiclesMachine[$i]['idProduct'], $id_company);

                if (sizeof($ciclesMachine) == 1) {
                    $arr = $generalProgrammingRoutesDao->findProgrammingRoutesByProduct($planCiclesMachine[$i]['idProduct']);

                    if (!$arr) {
                        $data = [];
                        $data['idProduct'] = $planCiclesMachine[$i]['idProduct'];
                        $data['idOrder'] = 0;
                        $data['route'] = 1;

                        $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                    }
                }

                $data = [];

                if (!$findPlanCiclesMachine) {
                    $machine = $lastDataDao->findLastInsertedCiclesMachine($id_company);
                    $data['idCiclesMachine'] = $machine['id_cicles_machine'];
                } else
                    $data['idCiclesMachine'] = $findPlanCiclesMachine['id_cicles_machine'];

                $planCiclesMachine[$i]['idCiclesMachine'] = $data['idCiclesMachine'];

                // Añadir siguiente ruta
                $arr = $generalPlanCiclesMachinesDao->findNextRouteByProduct($planCiclesMachine[$i]['idProduct']);
                $data['route'] = $arr['route'];
                $resolution = $generalPlanCiclesMachinesDao->changeRouteById($data);
            } else {
                $planCiclesMachine[$i]['idCiclesMachine'] = $findPlanCiclesMachine['id_cicles_machine'];
                $resolution = $planCiclesMachineDao->updatePlanCiclesMachine($planCiclesMachine[$i]);
            }

            if (isset($resolution['info'])) break;

            $data = [];
            $data['idCiclesMachine'] = $planCiclesMachine[$i]['idCiclesMachine'];

            if (isset($resolution)) break;
            // Calcular unidades
            $arr = $ciclesMachinesDao->calcUnitsTurn($planCiclesMachine[$i]['idCiclesMachine']);
            $data['units_turn'] = $arr['units_turn'];

            $arr = $ciclesMachinesDao->calcUnitsMonth($data, 2);
            $data['units_month'] = $arr['units_month'];

            $resolution = $generalPlanCiclesMachinesDao->updateUnits($data);
        }

        if ($resolution == null) $resp = array('success' => true, 'message' => 'Plan ciclos de maquina importado correctamente');
        else $resp = array('error' => true, 'message' => 'Ocurrio un error al importar los ciclos de maquina. Intente nuevamente');
    }

    $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

    for ($i = 0; $i < sizeof($orders); $i++) {
        $status = true;
        // Ficha tecnica
        $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
        $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
        $productsFTM = array_merge($productsMaterials, $compositeProducts);

        $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

        if ($orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'PROGRAMADO' && $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO') {
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

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanCiclesMachine', function (Request $request, Response $response, $args) use (
    $planCiclesMachineDao,
    $ciclesMachinesDao,
    $generalPlanCiclesMachinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPlanCiclesMachine = $request->getParsedBody();

    $machine = $generalPlanCiclesMachinesDao->findPlanCiclesMachineByProductAndMachine($dataPlanCiclesMachine['idProduct'], $dataPlanCiclesMachine['idMachine'], $dataPlanCiclesMachine['idProcess']);
    !is_array($machine) ? $data['id_cicles_machine'] = 0 : $data = $machine;

    if ($data['id_cicles_machine'] == $dataPlanCiclesMachine['idCiclesMachine'] || $data['id_cicles_machine'] == 0) {
        $planCiclesMachine = $planCiclesMachineDao->updatePlanCiclesMachine($dataPlanCiclesMachine);

        $data = [];

        $data['idCiclesMachine'] = $dataPlanCiclesMachine['idCiclesMachine'];

        $arr = $ciclesMachinesDao->calcUnitsTurn($data['idCiclesMachine']);
        $data['units_turn'] = $arr['units_turn'];

        $arr = $ciclesMachinesDao->calcUnitsMonth($data, 2);
        $data['units_month'] = $arr['units_month'];

        $planCiclesMachine = $generalPlanCiclesMachinesDao->updateUnits($data);

        if ($planCiclesMachine == null) $resp = array('success' => true, 'message' => 'Ciclo de maquina modificada correctamente');
        else $resp = array('error' => true, 'message' => 'Ocurrio un error al modificar ciclo de maquina. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Ciclo de maquina existente. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanCiclesMachine/{id_cicles_machine}', function (Request $request, Response $response, $args) use (
    $planCiclesMachineDao,
    $generalPlanCiclesMachinesDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $productsDao,
    $generalMaterialsDao,
    $generalProgrammingDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    // $planCiclesMachine = $generalPlanCiclesMachinesDao->findPlanCiclesMachine($args['id_cicles_machine']);

    $resolution = $planCiclesMachineDao->deletePlanCiclesMachine($args['id_cicles_machine']);

    if ($resolution == null) {
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Ficha tecnica
            $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
            $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
            $productsFTM = array_merge($productsMaterials, $compositeProducts);

            $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

            if ($orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'PROGRAMADO' && $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO') {
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
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Ciclo de maquina eliminado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar ciclo de maquina, existe informacion asociada a él');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/saveRoute', function (Request $request, Response $response, $args) use ($generalPlanCiclesMachinesDao) {
    $dataRoute = $request->getParsedBody();

    $routes = $dataRoute['data'];

    $resolution = null;

    foreach ($routes as $arr) {
        $resolution = $generalPlanCiclesMachinesDao->changeRouteById($arr);

        if (isset($resolution['info'])) break;
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Ciclo de maquina modificado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo modificar la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
