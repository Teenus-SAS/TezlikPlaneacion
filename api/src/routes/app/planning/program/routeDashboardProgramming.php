<?php

use TezlikPlaneacion\dao\DashboardProgrammingDao;

$dashboardProgrammingDao = new DashboardProgrammingDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/staffAvailable', function (Request $request, Response $response, $args) use ($dashboardProgrammingDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $staffAvailable = $dashboardProgrammingDao->findStaffAvailableByCompany($id_company);

    $response->getBody()->write(json_encode($staffAvailable, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});