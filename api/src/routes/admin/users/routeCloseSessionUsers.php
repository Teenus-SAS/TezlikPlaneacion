<?php

use TezlikPlaneacion\dao\CloseSessionUsersDao;

$closeSessionUser = new CloseSessionUsersDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Cerra Sesión usuarios
$app->post('/closeSessionUser/{id}', function (Request $request, Response $response, $args) use ($closeSessionUser) {
    // $dataUser = $request->getParsedBody();
    $session = $closeSessionUser->closeSessionUsers($args);

    if ($session == null) {
        $resp = array('success' => true, 'message' => 'Sesión terminada');
    } else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
