<?php

use TezlikPlaneacion\dao\LastDataDao;

$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


//Obtener los ultimos login de todos los usuarios activos en orden desc

$app->get('/lastLogins', function (Request $request, Response $response, $args) use ($lastDataDao) {
    //DATOS TODAS LAS EMPRESAS
    $resp = $lastDataDao->findLastLogins();

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
