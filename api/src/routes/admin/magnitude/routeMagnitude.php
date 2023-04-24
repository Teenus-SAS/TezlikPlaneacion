<?php

use TezlikPlaneacion\Dao\MagnitudesDao;

$magnitudesDao = new MagnitudesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/magnitudes', function (Request $request, Response $response, $args) use ($magnitudesDao) {
    $magnitudes = $magnitudesDao->findAllMagnitudes();
    $response->getBody()->write(json_encode($magnitudes));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/addMagnitudes', function (Request $request, Response $response, $args) use ($magnitudesDao) {
    $dataMagnitude = $request->getParsedBody();

    $magnitudes = $magnitudesDao->insertMagnitude($dataMagnitude);

    if ($magnitudes == null)
        $resp = array('success' => true, 'message' => 'Magnitud ingresada correctamente');
    else if (isset($magnitudes['info']))
        $resp = array('info' => true, 'message' => $magnitudes['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateMagnitude', function (Request $request, Response $response, $args) use ($magnitudesDao) {
    $dataMagnitude = $request->getParsedBody();

    $magnitudes = $magnitudesDao->updateMagnitude($dataMagnitude);

    if ($magnitudes == null)
        $resp = array('success' => true, 'message' => 'Magnitud modificada correctamente');
    else if (isset($magnitudes['info']))
        $resp = array('info' => true, 'message' => $magnitudes['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteMagnitude/{id_magnitude}', function (Request $request, Response $response, $args) use ($magnitudesDao) {
    $magnitudes = $magnitudesDao->deleteMagnitude($args['id_magnitude']);

    if ($magnitudes == null)
        $resp = array('success' => true, 'message' => 'Magnitud eliminada correctamente');
    else if (isset($magnitudes['info']))
        $resp = array('info' => true, 'message' => $magnitudes['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
