<?php

use TezlikPlaneacion\dao\RisksDao;

$risksDao = new RisksDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/risks', function (Request $request, Response $response, $args) use (
    $risksDao
) {
    $risks = $risksDao->findAllRisks();
    $response->getBody()->write(json_encode($risks));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateRisk', function (Request $request, Response $response, $args) use (
    $risksDao
) {
    $dataRisk = $request->getParsedBody();

    $risks = $risksDao->updateRisk($dataRisk);

    if ($risks == null)
        $resp = array('success' => true, 'message' => 'Nivel de riesgo modificada correctamente');
    else if (isset($risks['info']))
        $resp = array('info' => true, 'message' => $risks['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al modificar la información');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
