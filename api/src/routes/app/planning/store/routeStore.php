<?php

use TezlikPlaneacion\dao\AutenticationUserDao;
use TezlikPlaneacion\dao\StoreDao;

$storeDao = new StoreDao();
$autenticationDao = new AutenticationUserDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/store', function (Request $request, Response $response, $args) use ($storeDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $store = $storeDao->findAllStore($id_company);
    $response->getBody()->write(json_encode($store, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/deliverStore', function (Request $request, Response $response, $args) use (
    $storeDao,
    $autenticationDao
) {
    $dataStore = $request->getParsedBody();

    $user = $autenticationDao->findByEmail($dataStore['email'], 1);

    if (!$user) {
        $resp = array('error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    /* Valida el password del usuario */
    if (!password_verify($dataStore['password'], $user['password'])) {
        $resp = array('error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    // if ($user['id_rol'] != 2) {
    //     $resp = array('error' => true, 'message' => 'Usuario no autorizado, valide nuevamente');
    //     $response->getBody()->write(json_encode($resp));
    //     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    // }

    $store = $storeDao->saveDelivery($dataStore);

    if ($store == null)
        $resp = array('success' => true, 'message' => 'Materia prima entregada correctamente');
    else if (isset($store['info']))
        $resp = array('info' => true, 'message' => $store['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
