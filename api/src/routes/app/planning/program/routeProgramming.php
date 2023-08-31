<?php

use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProgrammingDao;
use TezlikPlaneacion\dao\DatesMachinesDao;
use TezlikPlaneacion\dao\FinalDateDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\LotsProductsDao;
use TezlikPlaneacion\dao\MachinesDao;
use TezlikPlaneacion\dao\ProductsDao;

$programmingDao = new ProgrammingDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$lastDataDao = new LastDataDao();
$machinesDao = new MachinesDao();
$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$productsDao = new ProductsDao();
$datesMachinesDao = new DatesMachinesDao();
$finalDateDao = new FinalDateDao();
$economicLotDao = new LotsProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* 
    $app->get('/generalData', function (Request $request, Response $response, $args) use ($machinesDao, $ordersDao, $productsDao) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $machines = $machinesDao->findAllMachinesByCompany($id_company);
        $orders = $ordersDao->findAllOrdersByCompany($id_company);
        $products = $productsDao->findAllProductsByCompany($id_company);

        $data['machines'] = $machines;
        $data['orders'] = $orders;
        $data['products'] = $products;

        $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }); 

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

$app->get('/programming', function (Request $request, Response $response, $args) use ($programmingDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $programming = $programmingDao->findAllProgrammingByCompany($id_company);
    $response->getBody()->write(json_encode($programming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/programming/{num_order}', function (Request $request, Response $response, $args) use ($programmingDao) {
    $programming = $programmingDao->findProductsByOrders($args['num_order']);

    $response->getBody()->write(json_encode($programming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/programmingByMachine/{id_machine}', function (Request $request, Response $response, $args) use ($generalProgrammingDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $programming = $generalProgrammingDao->findAllProgrammingByMachine($args['id_machine'], $id_company);
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
    $lastDataDao
) {
    session_start();
    $dataProgramming = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $programming = $programmingDao->findAllProgrammingByCompany($id_company);

    if (sizeof($programming) == 0) {
        $minDate = $dataProgramming['minDate'];
    } else {
        $minDate = $programming[0]['min_date'];
    }

    $result = $programmingDao->insertProgrammingByCompany($dataProgramming, $id_company);

    if ($result == null) {
        $programming = $lastDataDao->findLastInsertedProgramming($id_company);
        $result = $generalProgrammingDao->setMinDateProgramming($programming['id_programming'], $minDate);
    }

    if ($result == null)
        $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);

    if ($result == null)
        $resp = array('success' => true, 'message' => 'Programa de producción creado correctamente');
    else if (isset($result['info']))
        $resp = array('info' => true, 'message' => $result['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProgramming', function (Request $request, Response $response, $args) use ($programmingDao) {
    $dataProgramming = $request->getParsedBody();

    $programming = $programmingDao->updateProgramming($dataProgramming);

    if ($programming == null)
        $resp = array('success' => true, 'message' => 'Programa de producción actualizado correctamente');
    else if (isset($programming['info']))
        $resp = array('info' => true, 'message' => $programming['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProgramming', function (Request $request, Response $response, $args) use (
    $programmingDao,
    $generalOrdersDao
) {
    $dataProgramming = $request->getParsedBody();

    $result = $generalOrdersDao->updateAccumulatedOrder($dataProgramming);

    if ($result == null)
        $result = $programmingDao->deleteProgramming($dataProgramming['idProgramming']);

    if ($result == null)
        $resp = array('success' => true, 'message' => 'Programa de producción eliminado correctamente');
    else if (isset($programming['info']))
        $resp = array('info' => true, 'message' => $programming['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
