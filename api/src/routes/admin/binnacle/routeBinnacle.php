<?php

use TezlikPlaneacion\Dao\BinnacleDao;

$binnacleDao = new BinnacleDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/binnacle', function (Request $request, Response $response, $args) use ($binnacleDao) {
    $binnacle = $binnacleDao->findAllBinnacle();
    $response->getBody()->write(json_encode($binnacle));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
