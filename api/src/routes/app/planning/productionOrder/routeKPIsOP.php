<?php

use TezlikPlaneacion\dao\ProductionOrderPartialDao;

$productionOrderPartialDao = new ProductionOrderPartialDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/kpisOP', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $data = $productionOrderPartialDao->findAllOPPartialByCompany($id_company);

    // Inicializar variables para las sumas
    $totalWaste = 0;
    $totalPartialQuantity = 0;

    // Iterar sobre cada elemento del array para realizar la suma
    foreach ($data as $item) {
        // Sumar los valores de waste y partial_quantity
        $totalWaste += $item['waste'];
        $totalPartialQuantity += $item['partial_quantity'];
    }

    $quality = (1 - ($totalWaste / $totalPartialQuantity)) * 100;

    $response->getBody()->write(json_encode($quality));
    return $response->withHeader('Content-Type', 'application/json');
});
