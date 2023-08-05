<?php

use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\requisitionsDao;

$requisitionsDao = new requisitionsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$generalProductsDao = new GeneralProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/requisitions', function (Request $request, Response $response, $args) use ($requisitionsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $requisitions = $requisitionsDao->findAllRequisitionByCompany($id_company);
    $response->getBody()->write(json_encode($requisitions, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/requisitionDataValidation', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $generalRequisitionsDao
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
                empty($requisition[$i]['referenceProduct']) || empty($requisition[$i]['product']) || empty($requisition[$i]['applicationDate']) ||
                empty($requisition[$i]['deliveryDate']) || empty($requisition[$i]['quantity'])
            ) {
                $i = $i + 1;
                $dataImportRequisition = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $generalProductsDao->findProduct($requisition[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 1;
                $dataImportRequisition = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $requisition[$i]['idProduct'] = $findProduct['id_product'];


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
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataRequisition = $request->getParsedBody();

    $count = sizeof($dataRequisition);

    if ($count > 1) {
        $findRequisition = $generalRequisitionsDao->findRequisition($dataRequisition, $id_company);
        if (!$findRequisition) {
            $requisition = $requisitionsDao->insertRequisitionByCompany($dataRequisition, $id_company);

            if ($requisition == null)
                $resp = array('success' => true, 'message' => 'Requisicion creada correctamente');
            else if (isset($requisition['info']))
                $resp = array('info' => true, 'message' => $requisition['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Producto ya existente en la requisicion. Ingrese nuevo producto');
    } else {
        $requisition = $dataRequisition['importRequisition'];

        for ($i = 0; $i < sizeof($requisition); $i++) {
            $findProduct = $generalProductsDao->findProduct($requisition[$i], $id_company);
            $requisition[$i]['idProduct'] = $findProduct['id_product'];

            !isset($requisition[$i]['purchaseOrder']) ? $requisition[$i]['purchaseOrder'] = '' : $requisition[$i]['purchaseOrder'];

            $findRequisition = $generalRequisitionsDao->findRequisition($requisition[$i], $id_company);

            if (!$findRequisition) $resolution = $requisitionsDao->insertRequisitionByCompany($requisition[$i], $id_company);
            else {
                $requisition[$i]['idRequisition'] = $findRequisition['id_requisition'];
                $resolution = $requisitionsDao->updateRequisition($dataRequisition);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Requisicions importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateRequisition', function (Request $request, Response $response, $args) use (
    $requisitionsDao,
    $generalRequisitionsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataRequisition = $request->getParsedBody();

    $requisition = $generalRequisitionsDao->findRequisition($dataRequisition, $id_company);
    !is_array($requisition) ? $data['id_requisition'] = 0 : $data = $requisition;

    if ($data['id_requisition'] == $dataRequisition['idRequisition'] || $data['id_requisition'] == 0) {
        $requisition = $requisitionsDao->updateRequisition($dataRequisition);

        if ($requisition == null)
            $resp = array('success' => true, 'message' => 'Requisicion modificada correctamente');
        else if (isset($requisition['info']))
            $resp = array('info' => true, 'message' => $requisition['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Producto ya existente en la requisicion. Ingrese nuevo producto');
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
