<?php

use TezlikPlaneacion\dao\SendEmailDao;

//$sendCodeDao = new SendCodeDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Verificar codigo */

$app->post('/checkCode', function (Request $request, Response $response, $args) {
    session_start();
    $code = $_SESSION['code'];
    $dataCheck = $request->getParsedBody();

    if ($dataCheck['code'] == $code)
        $resp = array('success' => true, 'message' => 'Inicio de sesión completado');
    else
        $resp = array('error' => true, 'message' => 'Codigo incorrecto, ingrese el código nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
