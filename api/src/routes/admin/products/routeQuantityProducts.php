<?php

use TezlikPlaneacion\dao\ProductsQuantityDao;

$productsQuantityDao = new ProductsQuantityDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


//Cantidad productos general
$app->get('/quantityProductsGeneral', function (Request $request, Response $response, $args) use ($productsQuantityDao) {

    //NÚMERO DE PRODUCTOS GENERALES
    $resp = $productsQuantityDao->totalProducts();

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

/*
//CANTIDAD DE PRODUCTOS POR EMPRESA
$app->post('/quantityProducts', function (Request $request, Response $response, $args) use ($productsQuantityDao) {
    $dataProducts = $request->getParsedBody();

    //NÚMERO DE PRODUCTOS POR EMPRESA
    $resp = $productsQuantityDao->totalProductsByCompany();

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
}); */
