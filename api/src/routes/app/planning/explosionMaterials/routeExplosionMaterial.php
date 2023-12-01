<?php

use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\RequisitionsDao;

$explosionMaterialsDao = new ExplosionMaterialsDao();
$requisitionsDao = new RequisitionsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/explosionMaterials', function (Request $request, Response $response, $args) use (
    $explosionMaterialsDao,
    $requisitionsDao,
    $generalRequisitionsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $explosionMaterialsDao->findAllMaterialsConsolidated($id_company);

    for ($i = 0; $i < sizeof($materials); $i++) {
        if ($materials[$i]['available'] < 0) {
            $data = [];
            $data['idMaterial'] = $materials[$i]['id_material'];
            $data['applicationDate'] = '';
            $data['deliveryDate'] = '';
            $data['quantity'] = abs($materials[$i]['available']);
            $data['purchaseOrder'] = '';

            $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

            if (!$requisition)
                $requisitionsDao->insertRequisitionByCompany($data, $id_company);
            else {
                $data['idRequisition'] = $requisition['id_requisition'];
                $requisitionsDao->updateRequisition($data);
            }
        }
    }

    $response->getBody()->write(json_encode($materials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
