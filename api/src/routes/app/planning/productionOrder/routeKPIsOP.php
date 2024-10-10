<?php

use TezlikPlaneacion\dao\ProductionOrderPartialDao;

$productionOrderPartialDao = new ProductionOrderPartialDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/kpisOP', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $kpi = $productionOrderPartialDao->findAllOPPartialByCompany($id_company);
    $response->getBody()->write(json_encode($kpi));
    return $response->withHeader('Content-Type', 'application/json');
});