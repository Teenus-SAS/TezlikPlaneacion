<?php

use TezlikPlaneacion\dao\StoreDao;

$storeDao = new StoreDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/store', function (Request $request, Response $response, $args) use ($storeDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $store = $storeDao->findAllStore($id_company);
    $response->getBody()->write(json_encode($store, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
