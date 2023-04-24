<?php

use TezlikPlaneacion\dao\PlansDao;

$plansDao = new PlansDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/plans', function (Request $request, Response $response, $args) use ($plansDao) {
    $plans = $plansDao->findAllPlans();

    $response->getBody()->write(json_encode($plans));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
