<?php

use TezlikPlaneacion\dao\ConsolidatedDao;
use TezlikPlaneacion\dao\OrderTypesDao;

$consolidatedDao = new ConsolidatedDao();
$orderTypesDao = new OrderTypesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/consolidated', function (Request $request, Response $response, $args) use ($orderTypesDao, $consolidatedDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $orderTypes = $orderTypesDao->findAllOrderTypes();

    $consolidated = $consolidatedDao->findConsolidated($orderTypes, $id_company);

    $response->getBody()->write(json_encode($consolidated, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/calcConsolidated/{week}', function (Request $request, Response $response, $args) use ($orderTypesDao, $consolidatedDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $orderTypes = $orderTypesDao->findAllOrderTypes();

    $consolidated = $consolidatedDao->calcConsolidated($args['week'], $id_company, $orderTypes);

    if (!$consolidated)
        $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');
    else
        $resp = array('success' => true, 'message' => 'Semana agregada correctamente', 'dataConsolidated' => $consolidated);

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
