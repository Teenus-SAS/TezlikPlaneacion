<?php

use TezlikPlaneacion\dao\AreaDao;
use TezlikPlaneacion\dao\GeneralAreaDao;

$areaDao = new AreaDao();
$generalAreaDao = new GeneralAreaDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/planAreas', function (Request $request, Response $response, $args) use ($areaDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $areas = $areaDao->findAllAreasByCompany($id_company);
    $response->getBody()->write(json_encode($areas));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos importados */
$app->post('/areasDataValidation', function (Request $request, Response $response, $args) use (
    $generalAreaDao,
) {
    $dataArea = $request->getParsedBody();

    if (isset($dataArea)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $areas = $dataArea['importAreas'];

        $dataImportArea = [];

        for ($i = 0; $i < count($areas); $i++) {
            if (
                empty($areas[$i]['area'])
            ) {
                $i = $i + 2;
                $dataImportArea = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }
            if (
                empty(trim($areas[$i]['area']))
            ) {
                $i = $i + 2;
                $dataImportArea = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }
        }

        $insert = 0;
        $update = 0;

        if (sizeof($dataImportArea) == 0) {
            for ($i = 0; $i < count($areas); $i++) {
                $findArea = $generalAreaDao->findArea($areas[$i], $id_company);
                if (!$findArea)
                    $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportArea['insert'] = $insert;
                $dataImportArea['update'] = $update;
            }
        }
    } else
        $dataImportArea = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportArea, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPlanArea', function (Request $request, Response $response, $args) use (
    $areaDao,
    $generalAreaDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataArea = $request->getParsedBody();

    if (isset($dataArea['area'])) {
        $findArea = $generalAreaDao->findArea($dataArea, $id_company);

        if (!$findArea) {
            $resolution = $areaDao->insertAreaByCompany($dataArea, $id_company);

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Area creada correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
        } else {
            $resp = array('info' => true, 'message' => 'Area ya existe en la base de datos. Ingrese uno nuevo');
        }
    } else {
        $areas = $dataArea['importAreas'];

        for ($i = 0; $i < sizeof($areas); $i++) {
            $findArea = $generalAreaDao->findArea($areas[$i], $id_company);

            if (!$findArea) {
                $resolution = $areaDao->insertAreaByCompany($areas[$i], $id_company);
            } else {
                $areas[$i]['idArea'] = $findArea['id_plan_area'];
                $resolution = $areaDao->updateArea($areas[$i]);
            }

            if (isset($resolution['info'])) break;
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Areas importadas correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras importaba los datos. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateArea', function (Request $request, Response $response, $args) use (
    $areaDao,
    $generalAreaDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataArea = $request->getParsedBody();

    $area = $generalAreaDao->findArea($dataArea, $id_company);

    !is_array($area) ? $data['id_plan_area'] = 0 : $data = $area;

    if ($data['id_plan_area'] == $dataArea['idArea'] || $data['id_plan_area'] == 0) {
        $resolution = $areaDao->updateArea($dataArea);

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Area modificada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras guardaba los datos. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Area ya existe. Ingrese una nueva');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteArea/{id_plan_area}', function (Request $request, Response $response, $args) use ($areaDao) {
    $resolution = $areaDao->deleteArea($args['id_plan_area']);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Area eliminada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras eliminaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
