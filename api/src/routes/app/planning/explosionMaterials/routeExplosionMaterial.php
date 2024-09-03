<?php

use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\GeneralSellersDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\RequisitionsDao;

$explosionMaterialsDao = new ExplosionMaterialsDao();
$requisitionsDao = new RequisitionsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$generalRMStockDao = new GeneralRMStockDao();
$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalClientsDao = new GeneralClientsDao();
$generalSellersDao = new GeneralSellersDao();
$generalProductsDao = new GeneralProductsDao();
$lastDataDao = new LastDataDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/explosionMaterials', function (Request $request, Response $response, $args) use (
    $explosionMaterialsDao,
    $generalRMStockDao,
    $requisitionsDao,
    $generalOrdersDao,
    $generalProductsDao,
    $lastDataDao,
    $ordersDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $generalClientsDao,
    $generalSellersDao,
    $generalRequisitionsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $arr = $explosionMaterialsDao->findAllMaterialsConsolidated($id_company);

    $materials = $explosionMaterialsDao->setDataEXMaterials($arr);

    for ($i = 0; $i < sizeof($materials); $i++) {
        if (intval($materials[$i]['available']) < 0) {
            $data = [];
            $data['idMaterial'] = $materials[$i]['id_material'];

            $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

            $id_provider = 0;

            if ($provider) $id_provider = $provider['id_provider'];

            $data['idProvider'] = $id_provider;

            $data['applicationDate'] = '';
            $data['deliveryDate'] = '';
            $data['requiredQuantity'] = abs($materials[$i]['available']);
            $data['purchaseOrder'] = '';
            $data['requestedQuantity'] = 0;

            $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

            if (!$requisition)
                $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
            else {
                $data['idRequisition'] = $requisition['id_requisition'];
                $generalRequisitionsDao->updateRequisitionAuto($data);
            }
        }
    }

    $arr = $explosionMaterialsDao->findAllCompositeConsolidated($id_company);
    $products = $explosionMaterialsDao->setDataEXComposite($arr);

    for ($i = 0; $i < sizeof($products); $i++) {
        if (intval($products[$i]['available']) < 0) {
            $data = [];
            $arr2 = $generalOrdersDao->findLastNumOrder($id_company);

            $client = $generalClientsDao->findInternalClient($id_company);
            $seller = $generalSellersDao->findInternalSeller($id_company);

            if ($client && $seller) {
                $data['order'] = $arr2['num_order'];
                $data['dateOrder'] = date('Y-m-d');
                $data['minDate'] = '';
                $data['maxDate'] = '';
                $data['idProduct'] = $products[$i]['id_child_product'];
                $data['idClient'] = $client['id_client'];
                $data['idSeller'] = $seller['id_seller'];
                $data['route'] = 1;
                $data['originalQuantity'] = abs($products[$i]['available']);

                $findOrder = $generalOrdersDao->findLastSameOrder($data);
                if (!$findOrder) {
                    $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                    // $generalProductsDao->updateAccumulatedQuantity($products[$i]['id_child_product'], abs($products[$i]['available']), 2);

                    if (isset($resolution['info'])) break;
                    $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                    $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($products[$i]['id_child_product'], $lastOrder['id_order']);

                    if (!$programmingRoutes) {
                        $data['idOrder'] = $lastOrder['id_order'];
                        $data['route'] = 1;

                        $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                    }
                } else {
                    $data['idOrder'] = $findOrder['id_order'];
                    $resolution = $ordersDao->updateOrder($data);
                }
                if (isset($resolution['info'])) break;
            }
        }
    }

    $explosion = array_merge($materials, $products);

    $response->getBody()->write(json_encode($explosion, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
