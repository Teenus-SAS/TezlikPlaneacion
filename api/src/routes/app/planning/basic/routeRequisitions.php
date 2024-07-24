<?php

use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\requisitionsDao;

$requisitionsDao = new requisitionsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$generalClientsDao = new GeneralClientsDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$generalRMStockDao = new GeneralRMStockDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/requisitions', function (Request $request, Response $response, $args) use ($generalRequisitionsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $requisitions = $generalRequisitionsDao->findAllActualRequisitionByCompany($id_company);
    $response->getBody()->write(json_encode($requisitions, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/requisitions/{min_date}/{max_date}', function (Request $request, Response $response, $args) use ($generalRequisitionsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $requisitions = $generalRequisitionsDao->findAllMinAndMaxRequisitionByCompany($args['min_date'], $args['max_date'], $id_company);
    $response->getBody()->write(json_encode($requisitions, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/requisitionDataValidation', function (Request $request, Response $response, $args) use (
    $generalMaterialsDao,
    $generalRequisitionsDao,
    $generalClientsDao
) {
    $dataRequisition = $request->getParsedBody();

    if (isset($dataRequisition)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $requisition = $dataRequisition['importRequisition'];

        for ($i = 0; $i < sizeof($requisition); $i++) {
            if (
                empty($requisition[$i]['refRawMaterial']) || empty($requisition[$i]['nameRawMaterial']) || empty($requisition[$i]['applicationDate']) ||
                empty($requisition[$i]['deliveryDate']) || empty($requisition[$i]['quantity'])
            ) {
                $i = $i + 2;
                $dataImportRequisition = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            }
            if (
                trim(empty($requisition[$i]['refRawMaterial'])) || trim(empty($requisition[$i]['nameRawMaterial'])) || trim(empty($requisition[$i]['applicationDate'])) ||
                trim(empty($requisition[$i]['deliveryDate'])) || trim(empty($requisition[$i]['quantity']))
            ) {
                $i = $i + 2;
                $dataImportRequisition = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            }

            // Obtener id material
            $findMaterial = $generalMaterialsDao->findMaterial($requisition[$i], $id_company);
            if (!$findMaterial) {
                $i = $i + 2;
                $dataImportRequisition = array('error' => true, 'message' => "Material no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $requisition[$i]['idMaterial'] = $findMaterial['id_material'];

            // Obtener id proveedor
            $findClient = $generalClientsDao->findClientByName($requisition[$i], $id_company, 2);
            if (!$findClient) {
                $i = $i + 2;
                $dataImportRequisition = array('error' => true, 'message' => "Cliente no existe en la base de datos o es tipo cliente.<br>Fila: {$i}");
                break;
            } else $requisition[$i]['idProvider'] = $findClient['id_client'];

            $findRequisition = $generalRequisitionsDao->findRequisition($requisition[$i], $id_company);
            !$findRequisition ? $insert = $insert + 1 : $update = $update + 1;
            $dataImportRequisition['insert'] = $insert;
            $dataImportRequisition['update'] = $update;
        }
    } else
        $dataImportRequisition = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportRequisition, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addRequisition', function (Request $request, Response $response, $args) use (
    $requisitionsDao,
    $generalRequisitionsDao,
    $generalMaterialsDao,
    $generalClientsDao,
    $generalRMStockDao,
    $explosionMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataRequisition = $request->getParsedBody();

    $count = sizeof($dataRequisition);

    if ($count > 1) {
        // $findRequisition = $generalRequisitionsDao->findRequisition($dataRequisition, $id_company);
        // if (!$findRequisition) {
        $requisition = $requisitionsDao->insertRequisitionByCompany($dataRequisition, $id_company);

        if ($requisition == null)
            $resp = array('success' => true, 'message' => 'Requisicion creada correctamente');
        else if (isset($requisition['info']))
            $resp = array('info' => true, 'message' => $requisition['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        // } else
        // $resp = array('error' => true, 'message' => 'Material ya existente en la requisicion. Ingrese nuevo material');
    } else {
        $requisition = $dataRequisition['importRequisition'];

        for ($i = 0; $i < sizeof($requisition); $i++) {
            $findMaterial = $generalMaterialsDao->findMaterial($requisition[$i], $id_company);
            $requisition[$i]['idMaterial'] = $findMaterial['id_material'];
            $findClient = $generalClientsDao->findClientByName($requisition[$i], $id_company, 2);
            $requisition[$i]['idProvider'] = $findClient['id_client'];

            !isset($requisition[$i]['purchaseOrder']) ? $requisition[$i]['purchaseOrder'] = '' : $requisition[$i]['purchaseOrder'];

            // $findRequisition = $generalRequisitionsDao->findRequisition($requisition[$i], $id_company);

            // if (!$findRequisition) 
            $resolution = $requisitionsDao->insertRequisitionByCompany($requisition[$i], $id_company);
            // else {
            //     $requisition[$i]['idRequisition'] = $findRequisition['id_requisition'];
            //     $resolution = $requisitionsDao->updateRequisition($dataRequisition);
            // }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Requisicions importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $materials = $explosionMaterialsDao->findAllMaterialsConsolidated($id_company);

    for ($i = 0; $i < sizeof($materials); $i++) {
        if ($materials[$i]['available'] < 0) {
            $data = [];
            $data['idMaterial'] = $materials[$i]['id_material'];

            $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

            $id_provider = 0;

            if ($provider) $id_provider = $provider['id_provider'];

            $data['idProvider'] = $id_provider;
            $data['applicationDate'] = '';
            $data['deliveryDate'] = '';
            $data['requestedQuantity'] = abs($materials[$i]['available']);
            $data['purchaseOrder'] = '';
            $data['requiredQuantity'] = 0;

            $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

            if (!$requisition)
                $requisitionsDao->insertRequisitionByCompany($data, $id_company);
            else {
                $data['idRequisition'] = $requisition['id_requisition'];
                $requisitionsDao->updateRequisition($data);
            }
        }
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateRequisition', function (Request $request, Response $response, $args) use (
    $requisitionsDao,
    $generalRequisitionsDao
) {
    // session_start();
    // $id_company = $_SESSION['id_company'];
    $dataRequisition = $request->getParsedBody();

    // $requisition = $generalRequisitionsDao->findRequisition($dataRequisition, $id_company);
    // !is_array($requisition) ? $data['id_requisition'] = 0 : $data = $requisition;

    // if ($data['id_requisition'] == $dataRequisition['idRequisition'] || $data['id_requisition'] == 0) {
    $requisition = $requisitionsDao->updateRequisition($dataRequisition);

    if ($requisition == null)
        $resp = array('success' => true, 'message' => 'Requisicion modificada correctamente');
    else if (isset($requisition['info']))
        $resp = array('info' => true, 'message' => $requisition['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    // } else
    // $resp = array('error' => true, 'message' => 'Material ya existente en la requisicion. Ingrese nuevo material');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/saveAdmissionDate', function (Request $request, Response $response, $args) use (
    $generalRequisitionsDao,
    $generalMaterialsDao
) {
    $dataRequisition = $request->getParsedBody();

    $requisition = $generalRequisitionsDao->updateDateRequisition($dataRequisition);

    if ($requisition == null) {
        $material = $generalMaterialsDao->calcMaterialRecieved($dataRequisition['idMaterial']);

        $requisition = $generalMaterialsDao->updateQuantityMaterial($dataRequisition['idMaterial'], $material['quantity']);
    }

    if ($requisition == null)
        $resp = array('success' => true, 'message' => 'Fecha guardada correctamente');
    else if (isset($requisition['info']))
        $resp = array('info' => true, 'message' => $requisition['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteRequisition/{id_requisition}', function (Request $request, Response $response, $args) use ($requisitionsDao) {
    $requisitions = $requisitionsDao->deleteRequisition($args['id_requisition']);

    if ($requisitions == null)
        $resp = array('success' => true, 'message' => 'Requisicion eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la Requisicion, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
