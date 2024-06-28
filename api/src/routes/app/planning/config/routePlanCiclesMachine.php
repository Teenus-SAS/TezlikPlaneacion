<?php

use TezlikPlaneacion\dao\CiclesMachineDao;
use TezlikPlaneacion\dao\GeneralMachinesDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProcessDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\LotsProductsDao;
use TezlikPlaneacion\dao\PlanCiclesMachineDao;
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
$generalOrdersDao = new GeneralOrdersDao();

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
    $lastDataDao,
    $productsDao,
    $processDao,
    $machinesDao,
    $generalOrdersDao,
    $programmingRoutesDao
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
                    $arr = $programmingRoutesDao->findProgrammingRoutes($dataPlanCiclesMachine['idProduct']);

                    if (!$arr) {
                        $data = [];
                        $data['idProduct'] = $dataPlanCiclesMachine['idProduct'];
                        // $data['idOrder'] = $arr['id_order'];
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
                    $arr = $programmingRoutesDao->findProgrammingRoutes($planCiclesMachine[$i]['idProduct']);

                    if (!$arr) {
                        $data = [];
                        $data['idProduct'] = $planCiclesMachine[$i]['idProduct'];
                        // $data['idOrder'] = $arr['id_order'];
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
    $generalPlanCiclesMachinesDao
) {

    // $planCiclesMachine = $generalPlanCiclesMachinesDao->findPlanCiclesMachine($args['id_cicles_machine']);

    $resolution = $planCiclesMachineDao->deletePlanCiclesMachine($args['id_cicles_machine']);

    // if ($resolution == null){

    // }

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
