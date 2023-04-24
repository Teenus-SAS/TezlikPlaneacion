<?php

use TezlikPlaneacion\dao\ActiveUsersDao;
use TezlikPlaneacion\dao\LastLoginUsersDao;

$activeUsersDao = new ActiveUsersDao();
$lastLogUsersDao = new LastLoginUsersDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Usuarios activos general
// $app->get('/quantityAllUsers', function (Request $request, Response $response, $args) use ($activeUsersDao) {

//     //NÚMERO DE USUARIOS ACTIVOS GENERAL
//     $resp = $activeUsersDao->usersStatus();

//     $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
//     return $response->withHeader('Content-Type', 'application/json');
// });

$app->get('/lastLoginUsers', function (Request $request, Response $response, $args) use ($lastLogUsersDao) {

    //NÚMERO DE USUARIOS ACTIVOS GENERAL
    $resp = $lastLogUsersDao->loginUsers();

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
