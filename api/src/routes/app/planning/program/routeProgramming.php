<?php

use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProgrammingDao;
use TezlikPlaneacion\dao\DatesMachinesDao;
use TezlikPlaneacion\dao\FinalDateDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\LotsProductsDao;
use TezlikPlaneacion\dao\MachinesDao;
use TezlikPlaneacion\dao\ProductsDao;

$machinesDao = new MachinesDao();
$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$productsDao = new ProductsDao();
$programmingDao = new ProgrammingDao();
$datesMachinesDao = new DatesMachinesDao();
$finalDateDao = new FinalDateDao();
$economicLotDao = new LotsProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

$app->post('/programming', function (Request $request, Response $response, $args) use ($programmingDao) {
    $dataProgramming = $request->getParsedBody();

    if (isset($dataProgramming['idMachine']))
        $programming = $programmingDao->findProductsAndOrdersByMachine($dataProgramming);
    if (isset($dataProgramming['idProduct']))
        $programming = $programmingDao->findMachinesAndOrdersByProducts($dataProgramming);
    if (isset($dataProgramming['idOrder']))
        $programming = $programmingDao->findProductsByOrders($dataProgramming);

    $response->getBody()->write(json_encode($programming, JSON_NUMERIC_CHECK));
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
