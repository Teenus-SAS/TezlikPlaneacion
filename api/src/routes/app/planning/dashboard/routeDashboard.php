<?php

use TezlikPlaneacion\dao\DashboardGeneralDao;

$dashboardGeneralDao = new DashboardGeneralDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/dashboardGeneral', function (Request $request, Response $response, $args) use ($dashboardGeneralDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $classification = $dashboardGeneralDao->findClassificationByCompany($id_company);

    $data = array();
    $data['classification'] = $classification;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/dashboardIndicators', function (Request $request, Response $response, $args) use ($dashboardGeneralDao) {
    session_start();
    //obtener Id Company
    $id_company = $_SESSION['id_company'];

    //Obtener porcentaje productos sin stock
    $prodOutStock = $dashboardGeneralDao->findProductsOutOfStock($id_company);

    //Obtener porcentaje pedidos sin programar
    $ordersNoProgram = $dashboardGeneralDao->findOrdersNoProgramm($id_company);

    //Obtener porcentaje pedidos sin MP
    $OrdersNoMP = $dashboardGeneralDao->findOrdersNoMP($id_company);

    //Obtener porcentaje pedidos en Despacho
    $ordersDelivered = $dashboardGeneralDao->findOrdersDelivered($id_company);

    //Consolidar
    $indicators = array_merge($prodOutStock, $ordersNoProgram, $OrdersNoMP, $ordersDelivered);

    //Enviar respuesta
    $response->getBody()->write(json_encode($indicators, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/dashboardDeliveredOnTime', function (Request $request, Response $response, $args) use ($dashboardGeneralDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    //obtener % de ordenes entregadas a tiempo
    $percent = $dashboardGeneralDao->findOrdersDeliveredOnTime($id_company);

    //Enviar respuesta
    $response->getBody()->write(json_encode($percent, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/dashboardPendingOC', function (Request $request, Response $response, $args) use ($dashboardGeneralDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    //obtener cantidad de OC sin procesar
    $quantityOC = $dashboardGeneralDao->findPendignOC($id_company);

    //Enviar respuesta
    $response->getBody()->write(json_encode($quantityOC, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
