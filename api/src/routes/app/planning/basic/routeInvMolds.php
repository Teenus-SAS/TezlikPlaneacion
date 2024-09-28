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
        $dataImportInvMold = [];
        $debugg = [];

        for ($i = 0; $i < sizeof($molds); $i++) {
            if (
                empty($molds[$i]['referenceMold']) || empty($molds[$i]['mold']) || empty($molds[$i]['cavityTotal']) || empty($molds[$i]['cavityAvailable']) ||
                empty($molds[$i]['blowsTotal']) || empty($molds[$i]['available']) || empty($molds[$i]['cicleHour'])
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios. Fila: {$row}"));
                // break;
            }

            if (
                empty(trim($molds[$i]['referenceMold'])) || empty(trim($molds[$i]['mold'])) || empty(trim($molds[$i]['cavityTotal'])) || empty(trim($molds[$i]['cavityAvailable'])) ||
                empty(trim($molds[$i]['blowsTotal'])) || empty(trim($molds[$i]['available'])) || empty(trim($molds[$i]['cicleHour']))
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios. Fila: {$row}"));
                // break;
            }

            if ($molds[$i]['cavityTotal'] < $molds[$i]['cavityAvailable']) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "N° de cavidades disponibles mayor a N° de cavidades. Fila: {$row}"));
            }   // break;

            if (sizeof($debugg) == 0) {
                $findMold = $invMoldsDao->findInvMold($molds[$i], $id_company);
                !$findMold ? $insert = $insert + 1 : $update = $update + 1;
                $dataImportInvMold['insert'] = $insert;
                $dataImportInvMold['update'] = $update;
            }
        }
    } else
        $dataImportInvMold = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportInvMold;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addMold', function (Request $request, Response $response, $args) use ($invMoldsDao, $convertDataDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMold = $request->getParsedBody();

    $dataMolds = sizeof($dataMold);

    if ($dataMolds > 1) {
        // $dataMold = $convertDataDao->strReplaceMold($dataMold);
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

            // $molds[$i] = $convertDataDao->strReplaceMold($molds[$i]);

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

    $invMolds = $invMoldsDao->updateInvMold($dataMold);

    if ($invMolds == null)
        $resp = array('success' => true, 'message' => 'Molde modificado correctamente');
    else if (isset($invMolds['info']))
        $resp = array('info' => true, 'message' => $invMolds['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

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
