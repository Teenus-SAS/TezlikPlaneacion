<?php

use TezlikPlaneacion\dao\ExplosionMaterialsDao;

$explosionMaterialsDao = new ExplosionMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/explosionMaterials', function (Request $request, Response $response, $args) use ($explosionMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $explosionMaterialsDao->findAllMaterialsConsolidated($id_company);
    $response->getBody()->write(json_encode($materials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
