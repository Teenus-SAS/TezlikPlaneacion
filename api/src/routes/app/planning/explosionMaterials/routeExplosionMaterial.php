<?php

use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\RequisitionsDao;

$explosionMaterialsDao = new ExplosionMaterialsDao();
$requisitionsDao = new RequisitionsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$generalRMStockDao = new GeneralRMStockDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/explosionMaterials', function (Request $request, Response $response, $args) use (
    $explosionMaterialsDao,
    $generalRMStockDao,
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

            $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

            $id_provider = 0;

            if ($provider) $id_provider = $provider['id_provider'];

            $data['idProvider'] = $id_provider;

            $data['applicationDate'] = '';
            $data['deliveryDate'] = '';
            $data['requestedQuantity'] = abs($materials[$i]['available']);
            $data['purchaseOrder'] = '';
            $data['requestedQuantity'] = 0;

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
