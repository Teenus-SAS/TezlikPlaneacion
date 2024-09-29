<?php

use TezlikPlaneacion\dao\DashboardGeneralDao;

$dashboardGeneralDao = new DashboardGeneralDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/dashboardGeneral', function (Request $request, Response $response, $args) use ($dashboardGeneralDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $classification = $dashboardGeneralDao->findClassificationInvByCompany($id_company);

    $data = array();
    $data['classification'] = $classification;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/dashboardIndicators', function (Request $request, Response $response, $args) use ($dashboardGeneralDao) {
    session_start();
    //obtener Id Company
    $id_company = $_SESSION['id_company'];


    $prodOutStock = $dashboardGeneralDao->findProductsOutOfStock($id_company); //Obtener porcentaje productos sin stock
    $mpOutOfStock = $dashboardGeneralDao->findMPOutOfStock($id_company); //Obtener porcentaje MP sin stock
    $ordersActive = $dashboardGeneralDao->findAllActiveOrders($id_company);    //Obtener porcentaje pedidos sin programar
    $ordersNoProgram = $dashboardGeneralDao->findOrdersNoProgramm($id_company);    //Obtener porcentaje pedidos sin programar
    $OrdersNoMP = $dashboardGeneralDao->findOrdersNoMP($id_company); //Obtener porcentaje pedidos sin MP
    $ordersDelivered = $dashboardGeneralDao->findOrdersDelivered($id_company); //Obtener porcentaje pedidos en Despacho
    $indicators = array_merge($prodOutStock, $mpOutOfStock, $ordersActive, $ordersNoProgram, $OrdersNoMP, $ordersDelivered); //Consolidar

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

$app->get('/dashboardOrdersPerDay', function (Request $request, Response $response, $args) use ($dashboardGeneralDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    //obtener cantidad de OC sin procesar
    $OrdersPerDay = $dashboardGeneralDao->findOrderxDay($id_company);

    //Enviar respuesta
    $response->getBody()->write(json_encode($OrdersPerDay, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/dashboardQuantityOrdersByClients', function (Request $request, Response $response, $args) use ($dashboardGeneralDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    //obtener cantidad de Ordenes por Cliente
    $QuantityOrdersByClients = $dashboardGeneralDao->findQuantityOrdersByClients($id_company);

    //Enviar respuesta
    $response->getBody()->write(json_encode($QuantityOrdersByClients, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
