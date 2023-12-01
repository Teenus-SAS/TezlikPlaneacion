<?php

use TezlikPlaneacion\dao\ClassificationDao;

$classificationDao = new ClassificationDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/calcClassification', function (Request $request, Response $response, $args) use ($classificationDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataInventory = $request->getParsedBody();

    $inventory = $dataInventory['products'];

    for ($i = 0; $i < sizeof($inventory); $i++) {
        $classification = $classificationDao->calcClassificationByProduct($inventory[$i], $id_company);

        if ($classification == 1) {
            $i = $i + 2;
            break;
        }
    }

    if ($classification == null)
        $resp = array('success' => true, 'message' => 'Se calculó la clasificación correctamente');
    else if ($classification == 1)
        $resp = array('error' => true, 'message' => "Producto no tiene unidades vendidas en la base de datos. Fila: {$i}");
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras calculaba. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
