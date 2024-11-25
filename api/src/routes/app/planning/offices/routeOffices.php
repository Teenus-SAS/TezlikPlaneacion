<?php

use TezlikPlaneacion\dao\ClientsDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOfficesDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\GeneralRequisitionsMaterialsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\GeneralSellersDao;
use TezlikPlaneacion\dao\InventoryDaysDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\LicenseCompanyDao;
use TezlikPlaneacion\dao\OfficesDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\UsersOfficesDao;

$officesDao = new OfficesDao();
$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalClientsDao = new GeneralClientsDao();
$generalSellersDao = new GeneralSellersDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$generalExMaterialsDao = new GeneralExplosionMaterialsDao();
$generalOfficesDao = new GeneralOfficesDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$generalProductsDao = new GeneralProductsDao();
$inventoryDaysDao = new InventoryDaysDao();
$usersOfficesDao = new UsersOfficesDao();
$licenseDao = new LicenseCompanyDao();
$clientsDao = new ClientsDao();
$lastDataDao = new LastDataDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalRMStockDao = new GeneralRMStockDao();
$generalRequisitionsMaterialsDao = new GeneralRequisitionsMaterialsDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/offices', function (Request $request, Response $response, $args) use ($officesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $offices = $officesDao->findAllOfficesByCompany($id_company);
    $response->getBody()->write(json_encode($offices, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/offices/{min_date}/{max_date}', function (Request $request, Response $response, $args) use (
    $generalOfficesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $orders = $generalOfficesDao->findAllFilterOrders($args['min_date'], $args['max_date'], $id_company);

    $response->getBody()->write(json_encode($orders, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/actualOffices', function (Request $request, Response $response, $args) use ($generalOfficesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $offices = $generalOfficesDao->findAllActualsOfficesByCompany($id_company);
    $response->getBody()->write(json_encode($offices, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// $app->post('/cancelOffice', function (Request $request, Response $response, $args) use (
//     $generalProductsDao,
//     $officesDao,
//     $generalOrdersDao,
//     $usersOfficesDao
// ) {
//     $dataOrder = $request->getParsedBody();

//     // $order = $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], $dataOrder['quantity'] + $dataOrder['originalQuantity'], 1);
//     $dataOrder['date'] = '0000-00-00';
//     $order = $officesDao->updateDeliveryDate($dataOrder);

//     if ($order == null)
//         $order = $generalOrdersDao->changeStatus($dataOrder['idOrder'], 9);

//     if ($order == null) {
//         $arr = $generalProductsDao->findProductReserved($dataOrder['idProduct']);
//         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
//         $generalProductsDao->updateReservedByProduct($dataOrder['idProduct'], $arr['reserved']);
//     }

//     if ($order == null)
//         $resp = array('success' => true, 'message' => 'Despacho cancelado correctamente');
//     else if ($order['info'])
//         $resp = array('info' => true, 'message' => $order['message']);
//     else
//         $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });

$app->post('/changeOffices', function (Request $request, Response $response, $args) use (
    $officesDao,
    $generalProductsDao,
    $ordersDao,
    $generalOrdersDao,
    $generalClientsDao,
    $licenseDao,
    $generalMaterialsDao,
    $clientsDao,
    $lastDataDao,
    $generalRMStockDao,
    $generalExMaterialsDao,
    $explosionMaterialsDao,
    $generalRequisitionsMaterialsDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $generalSellersDao,
    $productsMaterialsDao,
    $usersOfficesDao,
    $inventoryDaysDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];
    $dataOrder = $request->getParsedBody();

    $resolution = $officesDao->updateDeliveryDate($dataOrder);

    if ($resolution == null) {
        $resolution = $usersOfficesDao->saveUserDeliverOffices($id_company, $dataOrder['idOrder'], $id_user);
    }

    if ($resolution == null) {
        $generalOrdersDao->changeStatus($dataOrder['idOrder'], 3);
        $arr = $generalProductsDao->findProductReserved($dataOrder['idProduct']);
        !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
        $generalProductsDao->updateReservedByProduct($dataOrder['idProduct'], $arr['reserved']);

        if ($dataOrder['stock'] > ($dataOrder['quantity'] - $dataOrder['originalQuantity'])) {
            $data = [];
            $arr2 = $generalOrdersDao->findLastNumOrderByCompany($id_company);

            $seller = $generalSellersDao->findInternalSeller($id_company);

            if ($seller) {
                $client = $generalClientsDao->findInternalClient($id_company);

                if (!$client) {
                    $company = $licenseDao->findLicenseCompany($id_company);
                    $dataClient = [];

                    $dataClient['nit'] = $company['nit'];
                    $dataClient['client'] = $company['company'];
                    $dataClient['address'] = $company['address'];
                    $dataClient['phone'] = $company['telephone'];
                    $dataClient['city'] = $company['city'];
                    $dataClient['type'] = 1;

                    $resolution = $clientsDao->insertClient($dataClient, $id_company);

                    $client = $lastDataDao->findLastInsertedClient();

                    $resolution = $generalClientsDao->changeStatusClient($client['id_client'], 1);
                }

                $data['order'] = $arr2['num_order'];
                $data['dateOrder'] = date('Y-m-d');
                $data['minDate'] = '';
                $data['maxDate'] = '';
                $data['idProduct'] = $dataOrder['idProduct'];
                $data['idClient'] = $client['id_client'];
                $data['idSeller'] = $seller['id_seller'];
                $data['route'] = 1;
                $data['originalQuantity'] = $dataOrder['stock'] - ($dataOrder['quantity'] - $dataOrder['originalQuantity']);
                $data['typeOrder'] = 2;

                $findOrder = $generalOrdersDao->findLastSameOrder($data);

                if (!$findOrder) {
                    $resolution = $ordersDao->insertOrderByCompany($data, $id_company);

                    $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);
                    $data['idOrder'] = $lastOrder['id_order'];
                    $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($dataOrder['idProduct'], $lastOrder['id_order']);

                    if (!$programmingRoutes) {
                        $data['route'] = 1;

                        $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                    }
                } else {
                    $data['idOrder'] = $findOrder['id_order'];
                    $data['originalQuantity'] = $findOrder['original_quantity'] + $data['originalQuantity'];

                    $resolution = $ordersDao->updateOrder($data);
                }

                if ($dataOrder['origin'] == 1) {
                    $resolution = $generalOrdersDao->changeStatus($data['idOrder'], 12);
                }
            }
        }

        $resolution = $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], $dataOrder['quantity'] - $dataOrder['originalQuantity'], 2);
    }

    if ($resolution == null && $dataOrder['origin'] == 1) {
        $product = $generalProductsDao->findProductById($dataOrder['idProduct']);

        if ($product) {
            $data = [];
            $data['refRawMaterial'] = $product['reference'];
            $data['nameRawMaterial'] = $product['product'];

            $material = $generalMaterialsDao->findMaterial($data, $id_company);

            if ($material)
                $resolution = $generalMaterialsDao->updateQuantityMaterial($material['id_material'], $dataOrder['quantity'] - $dataOrder['originalQuantity']);
        }
    }

    if ($resolution == null) {
        // Materiales
        $arr = $generalExMaterialsDao->findAllMaterialsConsolidated($id_company);
        $materials = $generalExMaterialsDao->setDataEXMaterials($arr);

        for ($i = 0; $i < sizeof($materials); $i++) {
            $findEX = $generalExMaterialsDao->findEXMaterial($materials[$i]['id_material']);

            if (!$findEX)
                $resolution = $explosionMaterialsDao->insertNewEXMByCompany($materials[$i], $id_company);
            else {
                $materials[$i]['id_explosion_material'] = $findEX['id_explosion_material'];
                $resolution = $explosionMaterialsDao->updateEXMaterials($materials[$i]);
            }

            if (intval($materials[$i]['available']) < 0) {
                $data = [];
                $data['idMaterial'] = $materials[$i]['id_material'];

                $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                $id_provider = 0;

                if (isset($provider['id_provider'])) $id_provider = $provider['id_provider'];

                $data['idProvider'] = $id_provider;
                $data['numOrder'] = $materials[$i]['num_order'];
                $data['applicationDate'] = '';
                $data['deliveryDate'] = '';
                $data['requiredQuantity'] = abs($materials[$i]['available']);
                $data['purchaseOrder'] = '';
                $data['requestedQuantity'] = 0;

                $requisition = $generalRequisitionsMaterialsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                if (!$requisition)
                    $generalRequisitionsMaterialsDao->insertRequisitionAutoByCompany($data, $id_company);
                else {
                    $data['idRequisition'] = $requisition['id_requisition_material'];
                    $generalRequisitionsMaterialsDao->updateRequisitionAuto($data);
                }
            }
        }
    }

    if ($resolution == null) {
        // Calcular Dias inventario 
        $inventory = $inventoryDaysDao->calcInventoryProductDays($dataOrder['idProduct']);

        !isset($inventory['days']) ? $days = 0 : $days = $inventory['days'];

        $resolution = $inventoryDaysDao->updateInventoryProductDays($dataOrder['idProduct'], $days);
    }
    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Pedido modificado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
