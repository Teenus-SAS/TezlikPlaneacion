<?php

use TezlikPlaneacion\dao\QuantityUsersDao;

$quantityUsersDao = new QuantityUsersDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/quantityUsers', function (Request $request, Response $response, $args) use ($quantityUsersDao) {
    session_start();
    //$dataQuantityUser = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $quantityUsersAllows = $quantityUsersDao->quantityUsersAllows($id_company);
    $quantityUsersCreated = $quantityUsersDao->quantityUsersCreated($id_company);
    $response->getBody()->write(json_encode($quantityUsersAllows, $quantityUsersCreated, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
