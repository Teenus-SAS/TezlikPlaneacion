<?php

use TezlikPlaneacion\dao\DashboardGeneralDao;

$dashboardGeneralDao = new DashboardGeneralDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/dashboardGeneral', function (Request $request, Response $response, $args) use (
    $dashboardGeneralDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $classification = $dashboardGeneralDao->findClassificationByCompany($id_company);

    $data = array();
    $data['classification'] = $classification;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
