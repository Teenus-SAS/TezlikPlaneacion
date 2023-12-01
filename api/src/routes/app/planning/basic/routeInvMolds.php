<?php

use TezlikPlaneacion\dao\ConvertDataDao;
use TezlikPlaneacion\dao\GeneralMoldsDao;
use TezlikPlaneacion\dao\InvMoldsDao;

$invMoldsDao = new InvMoldsDao();
$generalMoldsDao = new GeneralMoldsDao();
$convertDataDao = new ConvertDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/invMolds', function (Request $request, Response $response, $args) use ($invMoldsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $invMolds = $invMoldsDao->findAllInvMold($id_company);
    $response->getBody()->write(json_encode($invMolds, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/activeOrInactiveMold', function (Request $request, Response $response, $args) use ($generalMoldsDao) {
    $dataMold = $request->getParsedBody();

    if (isset($dataMold['observationMold'])) {
        // Desactivar molde
        $mold = $generalMoldsDao->inactiveMold($dataMold);

        if ($mold == null)
            $resp = array('success' => true, 'message' => 'Molde desactivado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras desactivaba el molde. Intente nuevamente');
    } else {
        // Activar molde
        $mold = $generalMoldsDao->activeMold($dataMold);

        if ($mold == null)
            $resp = array('success' => true, 'message' => 'Molde activado correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras activaba el molde. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/invMoldDataValidation', function (Request $request, Response $response, $args) use ($invMoldsDao) {
    $dataMold = $request->getParsedBody();

    if (isset($dataMold)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $molds = $dataMold['importInvMold'];

        for ($i = 0; $i < sizeof($molds); $i++) {
            if (
                empty($molds[$i]['referenceMold']) || empty($molds[$i]['mold']) || empty($molds[$i]['assemblyTime']) ||
                empty($molds[$i]['assemblyProduction']) || empty($molds[$i]['cavity']) || empty($molds[$i]['cavityAvailable'])
            ) {
                $i = $i + 2;
                $dataImportInvMold = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            }
            if ($molds[$i]['cavity'] < $molds[$i]['cavityAvailable']) {
                $i = $i + 2;
                $dataImportInvMold = array('error' => true, 'message' => "N° de cavidades disponibles mayor a N° de cavidades. Fila: {$i}");
                break;
            } else {
                $findMold = $invMoldsDao->findInvMold($molds[$i], $id_company);
                !$findMold ? $insert = $insert + 1 : $update = $update + 1;
                $dataImportInvMold['insert'] = $insert;
                $dataImportInvMold['update'] = $update;
            }
        }
    } else
        $dataImportInvMold = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportInvMold, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addMold', function (Request $request, Response $response, $args) use ($invMoldsDao, $convertDataDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMold = $request->getParsedBody();

    $dataMolds = sizeof($dataMold);

    if ($dataMolds > 1) {
        $dataMold = $convertDataDao->strReplaceMold($dataMold);
        $invMolds = $invMoldsDao->insertInvMoldByCompany($dataMold, $id_company);

        if ($invMolds == null)
            $resp = array('success' => true, 'message' => 'Molde creado correctamente');
        else if (isset($invMolds['info']))
            $resp = array('info' => true, 'message' => $invMolds['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $molds = $dataMold['importInvMold'];

        for ($i = 0; $i < sizeof($molds); $i++) {
            $findMold = $invMoldsDao->findInvMold($molds[$i], $id_company);

            $molds[$i] = $convertDataDao->strReplaceMold($molds[$i]);

            if (!$findMold) $resolution = $invMoldsDao->insertInvMoldByCompany($molds[$i], $id_company);
            else {
                $molds[$i]['idMold'] = $findMold['id_mold'];
                $resolution = $invMoldsDao->updateInvMold($dataMold);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Moldes importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateMold', function (Request $request, Response $response, $args) use ($invMoldsDao, $convertDataDao) {
    $dataMold = $request->getParsedBody();

    if (
        empty($dataMold['referenceMold']) || empty($dataMold['mold']) || empty($dataMold['assemblyTime']) || empty($dataMold['idMold']) ||
        empty($dataMold['assemblyProduction']) || empty($dataMold['cavity'] || empty($dataMold['cavityAvailable']))
    ) {
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    } else {
        $dataMold = $convertDataDao->strReplaceMold($dataMold);
        $invMolds = $invMoldsDao->updateInvMold($dataMold);

        if ($invMolds == null)
            $resp = array('success' => true, 'message' => 'Molde modificado correctamente');
        else if (isset($invMolds['info']))
            $resp = array('info' => true, 'message' => $invMolds['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteMold/{id_mold}', function (Request $request, Response $response, $args) use ($invMoldsDao) {
    $invMolds = $invMoldsDao->deleteInvMold($args['id_mold']);
    if ($invMolds == null)
        $resp = array('success' => true, 'message' => 'Molde eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el molde, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
