<?php

use TezlikPlaneacion\dao\ClientsDao;
use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\dao\ConvertDataDao;
use TezlikPlaneacion\dao\DeliveryDateDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\ExplosionProductsdao;
use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralExplosionProductsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralOrderTypesDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\GeneralRequisitionsMaterialsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsProductsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\GeneralSellersDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\LicenseCompanyDao;
use TezlikPlaneacion\dao\MallasDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\RequisitionsMaterialsDao;

$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProductsDao = new GeneralProductsDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new GeneralProductsDao();
$generalRMStockDao = new GeneralRMStockDao();
$clientsDao = new ClientsDao();
$generalSellersDao = new GeneralSellersDao();
$generalClientsDao = new GeneralClientsDao();
$orderTypesDao = new GeneralOrderTypesDao();
$licenseDao = new LicenseCompanyDao();
$clientsDao = new ClientsDao();
$mallasDao = new MallasDao();
$deliveryDateDao = new DeliveryDateDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$lastDataDao = new LastDataDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$explosionProductsDao = new ExplosionProductsdao();
$generalExProductsDao = new GeneralExplosionProductsDao();
$generalExMaterialsDao = new GeneralExplosionMaterialsDao();
$generalRequisitionsMaterialsDao = new GeneralRequisitionsMaterialsDao();
$generalRequisitionsProductsDao = new GeneralRequisitionsProductsDao();
$RequisitionsMaterialsDao = new RequisitionsMaterialsDao();
$filterDataDao = new FilterDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/orders', function (Request $request, Response $response, $args) use (
    $ordersDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $orders = $ordersDao->findAllOrdersByCompany($id_company);

    $response->getBody()->write(json_encode($orders));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/orders/{id_order}', function (Request $request, Response $response, $args) use ($generalOrdersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $data['order'] = $args['id_order'];
    $order = $generalOrdersDao->findOrdersByCompany($data, $id_company);
    $response->getBody()->write(json_encode($order));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/orderDataValidation', function (Request $request, Response $response, $args) use (
    $generalOrdersDao,
    $productsDao,
    $generalSellersDao,
    $generalClientsDao,
    $orderTypesDao
) {
    $dataOrder = $request->getParsedBody();

    if (isset($dataOrder)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;
        $order = $dataOrder['importOrder'];

        $dataImportOrder = [];

        for ($i = 0; $i < sizeof($order); $i++) {
            if (
                empty($order[$i]['referenceProduct'])  || empty($order[$i]['product']) || empty($order[$i]['client']) ||
                empty($order[$i]['email'])  || empty($order[$i]['dateOrder']) ||
                empty($order[$i]['minDate']) || empty($order[$i]['maxDate']) || empty($order[$i]['originalQuantity'])
            ) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            if (
                trim($order[$i]['referenceProduct']) == '' || trim($order[$i]['product']) == '' || trim($order[$i]['client']) == '' ||
                trim($order[$i]['email']) == '' || trim($order[$i]['dateOrder']) == '' ||
                trim($order[$i]['minDate']) == '' || trim($order[$i]['maxDate']) == '' || trim($order[$i]['originalQuantity']) == ''
            ) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            if ($order[$i]['dateOrder'] > $order[$i]['minDate']) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Fecha de pedido mayor a la fecha minima fila: {$i}");
                break;
            }
            if ($order[$i]['minDate'] > $order[$i]['maxDate']) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Fecha de minima mayor a la fecha maxima fila: {$i}");
                break;
            }

            $date = date("Y-m-d");
            if ($order[$i]['minDate'] < $date || $order[$i]['maxDate'] < $date || $order[$i]['dateOrder'] < $date) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Fecha menor a la fecha actual fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($order[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Producto no existe en la base de datos.<br>Fila: {$i}");
                break;
            } else $order[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id vendedor
            $findSeller = $generalSellersDao->findSeller($order[$i], $id_company, 1);
            if (!$findSeller) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Cliente no existe en la base de datos o es tipo proveedor.<br>Fila: {$i}");
                break;
            } else $order[$i]['idSeller'] = $findSeller['id_seller'];

            // Obtener id cliente
            $findClient = $generalClientsDao->findClientByName($order[$i], $id_company, 1);
            if (!$findClient) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Cliente no existe en la base de datos o es tipo proveedor.<br>Fila: {$i}");
                break;
            } else $order[$i]['idClient'] = $findClient['id_client'];
        }

        if (!isset($dataImportOrder['error'])) {
            $importOrder = [];

            foreach ($order as $arr) {
                $repeat = false;

                for ($i = 0; $i < sizeof($importOrder); $i++) {
                    if ($importOrder[$i]['referenceProduct'] == trim($arr['referenceProduct']) && $importOrder[$i]['email'] == trim($arr['email']) && $importOrder[$i]['client'] == strtoupper(trim($arr['client']))) {
                        $importOrder[$i]['originalQuantity'] += $arr['originalQuantity'];
                        $repeat = true;
                        break;
                    }
                }

                if ($repeat == false) {
                    $importOrder[] = array(
                        'idProduct' => $arr['idProduct'],
                        'referenceProduct' => trim($arr['referenceProduct']),
                        'product' => $arr['product'],
                        'idSeller' => $arr['idSeller'],
                        'email' => trim($arr['email']),
                        'idClient' => $arr['idClient'],
                        'client' => strtoupper(trim($arr['client'])),
                        'dateOrder' => $arr['dateOrder'],
                        'minDate' => $arr['minDate'],
                        'maxDate' => $arr['maxDate'],
                        'originalQuantity' => $arr['originalQuantity'],
                    );
                }
            }

            for ($i = 0; $i < sizeof($importOrder); $i++) {
                $findOrder = $generalOrdersDao->findOrder($importOrder[$i], $id_company);
                !$findOrder ? $insert = $insert + 1 : $update = $update + 1;
                $dataImportOrder['insert'] = $insert;
                $dataImportOrder['update'] = $update;
            }
        }
    } else $dataImportOrder = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportOrder, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addOrder', function (Request $request, Response $response, $args) use (
    $ordersDao,
    $generalOrdersDao,
    $convertDataDao,
    $productsDao,
    $generalRMStockDao,
    $generalProductsDao,
    $generalClientsDao,
    $lastDataDao,
    $productsMaterialsDao,
    $licenseDao,
    $clientsDao,
    $compositeProductsDao,
    $generalPlanCiclesMachinesDao,
    $filterDataDao,
    $generalSellersDao,
    $explosionMaterialsDao,
    $explosionProductsDao,
    $generalExMaterialsDao,
    $generalExProductsDao,
    $generalRequisitionsProductsDao,
    $generalRequisitionsMaterialsDao,
    $requisitionsMaterialsDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOrder = $request->getParsedBody();
    $resolution = null;

    $dataOrders = sizeof($dataOrder);

    if ($dataOrders > 1) {

        $dataOrder = $convertDataDao->changeDateOrder($dataOrder);

        $findOrder = $generalOrdersDao->findSameOrder($dataOrder);
        if (!$findOrder) {
            $order = $generalOrdersDao->findLastNumOrderByCompany($id_company);
            $dataOrder['order'] = $order['num_order'];
            $dataOrder['typeOrder'] = 1;

            $resolution = $ordersDao->insertOrderByCompany($dataOrder, $id_company);

            if ($resolution == null) {
                $arr = $lastDataDao->findLastInsertedOrder($id_company);
                $dataOrder['idOrder'] = $arr['id_order'];
                $dataOrder['route'] = 1;

                $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($dataOrder['idProduct'], $dataOrder['idOrder']);

                if (!$programmingRoutes) {
                    $data = [];
                    $data['idProduct'] = $dataOrder['idProduct'];
                    $data['idOrder'] = $dataOrder['idOrder'];
                    $data['route'] = 1;

                    $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                }
            }

            $data[0] = $dataOrder['order'] . '-' . $dataOrder['idProduct'];

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Pedido ingresado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else {
            $resolution = 1;
            $resp = array('error' => true, 'message' => 'Pedido duplicado. Ingrese otro pedido');
        }
    } else {
        $order = $dataOrder['importOrder'];
        $importOrder = [];

        foreach ($order as $arr) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($arr, $id_company);
            $arr['idProduct'] = $findProduct['id_product'];

            // Obtener id vendedor
            $findSeller = $generalSellersDao->findSeller($arr, $id_company, 1);
            $arr['idSeller'] = $findSeller['id_seller'];

            // Obtener id cliente
            $findClient = $generalClientsDao->findClientByName($arr, $id_company, 1);
            $arr['idClient'] = $findClient['id_client'];

            $repeat = false;

            for ($i = 0; $i < sizeof($importOrder); $i++) {
                if ($importOrder[$i]['referenceProduct'] == trim($arr['referenceProduct']) && $importOrder[$i]['eamil'] == trim($arr['email']) && $importOrder[$i]['client'] == strtoupper(trim($arr['client']))) {
                    $importOrder[$i]['originalQuantity'] += $arr['originalQuantity'];
                    $repeat = true;
                    break;
                }
            }

            if ($repeat == false) {
                $importOrder[] = array(
                    'idProduct' => $arr['idProduct'],
                    'referenceProduct' => trim($arr['referenceProduct']),
                    'product' => $arr['product'],
                    'idSeller' => $arr['idSeller'],
                    'email' => trim($arr['email']),
                    'idClient' => $arr['idClient'],
                    'client' => strtoupper(trim($arr['client'])),
                    'dateOrder' => $arr['dateOrder'],
                    'minDate' => $arr['minDate'],
                    'maxDate' => $arr['maxDate'],
                    'originalQuantity' => $arr['originalQuantity'],
                );
            }
        }
        // $import = true;

        for ($i = 0; $i < sizeof($importOrder); $i++) {
            $importOrder[$i] = $convertDataDao->changeDateOrder($importOrder[$i]);

            // Consultar pedido
            $findOrder = $generalOrdersDao->findOrder($importOrder[$i], $id_company);
            if (!$findOrder) {
                $lastOrder = $generalOrdersDao->findLastNumOrderByCompany($id_company);
                $importOrder[$i]['order'] = $lastOrder['num_order'];
                $importOrder[$i]['typeOrder'] = 1;

                $resolution = $ordersDao->insertOrderByCompany($importOrder[$i], $id_company);

                $arr = $lastDataDao->findLastInsertedOrder($id_company);
                $importOrder[$i]['idOrder'] = $arr['id_order'];
                // $order[$i]['route'] = 1;

                if (isset($resolution['info'])) break;

                $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($importOrder[$i]['idProduct'], $importOrder[$i]['idOrder']);

                if (!$programmingRoutes) {
                    $data = [];
                    $data['idProduct'] = $importOrder[$i]['idProduct'];
                    $data['idOrder'] = $importOrder[$i]['idOrder'];
                    $data['route'] = 1;

                    $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                }
            } else {
                $importOrder[$i]['idOrder'] = $findOrder['id_order'];
                $resolution = $ordersDao->updateOrder($importOrder[$i]);
            }
            // Obtener todos los pedidos
            $data[$i] = $importOrder[$i]['order'] . '-' . $importOrder[$i]['idProduct'];

            if (isset($resolution['info'])) break;
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Pedido importado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al importar el pedido. Intente nuevamente');
    }

    $accumulated_quantity = 0;

    if ($resolution == null) { // Cambiar estado pedidos  
        $cicle = true;

        while ($cicle == true) {
            $cicle = false;
            // Productos
            $arr = $generalExProductsDao->findAllCompositeConsolidated($id_company);
            $products = $generalExProductsDao->setDataEXComposite($arr);

            for ($i = 0; $i < sizeof($products); $i++) {
                $findEX = $generalExProductsDao->findEXProduct($products[$i]['id_child_product']);

                if (!$findEX)
                    $resolution = $explosionProductsDao->insertNewEXPByCompany($products[$i], $id_company);
                else {
                    $products[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                    $resolution = $explosionProductsDao->updateEXProduct($products[$i]);
                }

                if (intval($products[$i]['available']) < 0 && abs($products[$i]['available']) > $products[$i]['quantity_material']) {
                    $data = [];
                    $arr2 = $generalOrdersDao->findLastOrderByNumOrder($products[$i]['num_order']);

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
                        $data['idProduct'] = $products[$i]['id_child_product'];
                        $data['idClient'] = $client['id_client'];
                        $data['idSeller'] = $seller['id_seller'];
                        $data['route'] = 1;
                        $data['originalQuantity'] = abs($products[$i]['available']);
                        $data['typeOrder'] = 2;

                        $findOrder = $generalOrdersDao->findLastSameOrder($data);
                        if (!$findOrder) {
                            $cicle = true;
                            $resolution = $ordersDao->insertOrderByCompany($data, $id_company);

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

            $arr = $generalExProductsDao->findAllChildrenCompositeConsolidaded($id_company);
            $cProducts = $generalExProductsDao->setDataEXComposite($arr);

            for ($i = 0; $i < sizeof($cProducts); $i++) {
                $findEX = $generalExProductsDao->findEXProduct($cProducts[$i]['id_child_product']);

                if (!$findEX)
                    $resolution = $explosionProductsDao->insertNewEXPByCompany($cProducts[$i], $id_company);
                else {
                    $cProducts[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                    $resolution = $explosionProductsDao->updateEXProduct($cProducts[$i]);
                }

                if (intval($cProducts[$i]['available']) < 0 && abs($cProducts[$i]['available']) > $cProducts[$i]['quantity_material']) {
                    $data = [];
                    $arr2 = $generalOrdersDao->findLastOrderByNumOrder($cProducts[$i]['num_order']);

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
                        $data['idProduct'] = $cProducts[$i]['id_child_product'];
                        $data['idClient'] = $client['id_client'];
                        $data['idSeller'] = $seller['id_seller'];
                        $data['route'] = 1;
                        $data['originalQuantity'] = abs($cProducts[$i]['available']);
                        $data['typeOrder'] = 2;

                        $findOrder = $generalOrdersDao->findLastSameOrder($data);
                        if (!$findOrder) {
                            $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                            if (isset($resolution['info'])) break;
                            $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                            $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($cProducts[$i]['id_child_product'], $lastOrder['id_order']);

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

            $arr = $generalExMaterialsDao->findAllChildrenMaterialsConsolidaded($id_company);
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

        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            if (
                $orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FINALIZADO' &&
                $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO'
            ) {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                    // Ficha tecnica 
                    $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
                    $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
                    $productsFTM = array_merge($productsMaterials, $compositeProducts);

                    $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

                    if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                        $orders[$i]['origin'] == 2 ? $status = 5 : $status = 13;
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], $status);
                        $status = false;
                    } else {
                        foreach ($planCicles as $arr) {
                            // Verificar Maquina Disponible
                            if ($arr['status'] == 0 && $arr['status_alternal_machine'] == 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 10);
                                $status = false;
                                break;
                            }
                            // Verificar Empleados
                            if ($arr['employees'] == 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 11);
                                $status = false;
                                break;
                            }
                        }

                        // Verificar Materia Prima
                        foreach ($productsFTM as $arr) {
                            if ($arr['quantity_material'] <= 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 6);
                                $status = false;
                                break;
                            }
                        }
                    }
                }

                if ($status == true) {
                    if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 2);
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
                    } else {
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'];
                    }

                    if ($orders[$i]['status'] != 'DESPACHO') {
                        $date = date('Y-m-d');

                        $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
                    }

                    $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
                }
            }

            // Pedidos automaticos
            if ($orders[$i]['status'] == 'FABRICADO') {
                $chOrders = $generalOrdersDao->findAllChildrenOrders($orders[$i]['num_order']);

                foreach ($chOrders as $arr) {
                    $resolution = $generalOrdersDao->changeStatus($arr['id_order'], 12);
                }
            }
        }

        // $arrayBD = [];
        // for ($i = 0; $i < sizeof($orders); $i++) {
        //     array_push($arrayBD, $orders[$i]['concate']);
        // }

        // $tam_arrayBD = sizeof($arrayBD);
        // $tam_result = sizeof($data);

        // if ($tam_arrayBD > $tam_result)
        //     $array_diff = array_diff($arrayBD, $data);
        // else
        //     $array_diff = array_diff($data, $arrayBD);

        // //reindezar array
        // $array_diff = array_values($array_diff);

        // if ($array_diff)
        //     for ($i = 0; $i < sizeof($array_diff); $i++) {
        //         $posicion =  strrpos($array_diff[$i], '-');
        //         $id_product = substr($array_diff[$i], $posicion + 1);
        //         $order = substr($array_diff[$i], 0, $posicion);
        //         $generalOrdersDao->changeStatusOrder($order, $id_product);
        //     }
        // // else if (sizeof($array_diff) == 0)
    }

    if (isset($resp['success'])) {
        $resp['accumulated_quantity'] = $accumulated_quantity;
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateOrder', function (Request $request, Response $response, $args) use (
    $ordersDao,
    $generalOrdersDao,
    $generalProductsDao,
    $convertDataDao,
    $generalRMStockDao,
    $lastDataDao,
    $generalClientsDao,
    $generalSellersDao,
    $licenseDao,
    $clientsDao,
    $generalPlanCiclesMachinesDao,
    $explosionMaterialsDao,
    $explosionProductsDao,
    $generalExProductsDao,
    $generalExMaterialsDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $requisitionsMaterialsDao,
    $generalRequisitionsMaterialsDao,
    $generalRequisitionsProductsDao,
    $productsMaterialsDao,
    $compositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOrder = $request->getParsedBody();

    $dataOrder = $convertDataDao->changeDateOrder($dataOrder);

    $resolution = $ordersDao->updateOrder($dataOrder);

    $accumulated_quantity = 0;

    if ($resolution == null) {
        $cicle = true;

        while ($cicle == true) {
            $cicle = false;
            // Productos
            $arr = $generalExProductsDao->findAllCompositeConsolidated($id_company);
            $products = $generalExProductsDao->setDataEXComposite($arr);

            for ($i = 0; $i < sizeof($products); $i++) {
                $findEX = $generalExProductsDao->findEXProduct($products[$i]['id_child_product']);

                if (!$findEX)
                    $resolution = $explosionProductsDao->insertNewEXPByCompany($products[$i], $id_company);
                else {
                    $products[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                    $resolution = $explosionProductsDao->updateEXProduct($products[$i]);
                }

                if (intval($products[$i]['available']) < 0 && abs($products[$i]['available']) > $products[$i]['quantity_material']) {
                    $data = [];
                    $arr2 = $generalOrdersDao->findLastOrderByNumOrder($products[$i]['num_order']);

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
                        $data['idProduct'] = $products[$i]['id_child_product'];
                        $data['idClient'] = $client['id_client'];
                        $data['idSeller'] = $seller['id_seller'];
                        $data['route'] = 1;
                        $data['originalQuantity'] = abs($products[$i]['available']);
                        $data['typeOrder'] = 2;

                        $findOrder = $generalOrdersDao->findLastSameOrder($data);
                        if (!$findOrder) {
                            $cicle = true;
                            $resolution = $ordersDao->insertOrderByCompany($data, $id_company);

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

            $arr = $generalExProductsDao->findAllChildrenCompositeConsolidaded($id_company);
            $cProducts = $generalExProductsDao->setDataEXComposite($arr);

            for ($i = 0; $i < sizeof($cProducts); $i++) {
                $findEX = $generalExProductsDao->findEXProduct($cProducts[$i]['id_child_product']);

                if (!$findEX)
                    $resolution = $explosionProductsDao->insertNewEXPByCompany($cProducts[$i], $id_company);
                else {
                    $cProducts[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                    $resolution = $explosionProductsDao->updateEXProduct($cProducts[$i]);
                }

                if (intval($cProducts[$i]['available']) < 0 && abs($cProducts[$i]['available']) > $cProducts[$i]['quantity_material']) {
                    $data = [];
                    $arr2 = $generalOrdersDao->findLastOrderByNumOrder($cProducts[$i]['num_order']);

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
                        $data['idProduct'] = $cProducts[$i]['id_child_product'];
                        $data['idClient'] = $client['id_client'];
                        $data['idSeller'] = $seller['id_seller'];
                        $data['route'] = 1;
                        $data['originalQuantity'] = abs($cProducts[$i]['available']);
                        $data['typeOrder'] = 2;

                        $findOrder = $generalOrdersDao->findLastSameOrder($data);
                        if (!$findOrder) {
                            $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                            if (isset($resolution['info'])) break;
                            $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                            $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($cProducts[$i]['id_child_product'], $lastOrder['id_order']);

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

            $arr = $generalExMaterialsDao->findAllChildrenMaterialsConsolidaded($id_company);
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
    }

    if ($resolution == null) {
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            if (
                $orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FINALIZADO' &&
                $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO'
            ) {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                    // Ficha tecnica 
                    $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
                    $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
                    $productsFTM = array_merge($productsMaterials, $compositeProducts);

                    $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

                    if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                        $orders[$i]['origin'] == 2 ? $status = 5 : $status = 13;

                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], $status);
                        $status = false;
                    } else {
                        foreach ($planCicles as $arr) {
                            // Verificar Maquina Disponible
                            if ($arr['status'] == 0 && $arr['status_alternal_machine'] == 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 10);
                                $status = false;
                                break;
                            }
                            // Verificar Empleados
                            if ($arr['employees'] == 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 11);
                                $status = false;
                                break;
                            }
                        }
                        // Verificar Material
                        foreach ($productsFTM as $arr) {
                            if ($arr['quantity_material'] <= 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 6);
                                $status = false;
                                break;
                            }
                        }
                    }
                }

                if ($status == true) {
                    if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 2);
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
                    } else {
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'];
                    }

                    if ($orders[$i]['status'] != 'DESPACHO') {
                        $date = date('Y-m-d');

                        $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
                    }

                    $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
                }
            }

            // Pedidos automaticos
            if ($orders[$i]['status'] == 'FABRICADO') {
                $chOrders = $generalOrdersDao->findAllChildrenOrders($orders[$i]['num_order']);

                foreach ($chOrders as $arr) {
                    $resolution = $generalOrdersDao->changeStatus($arr['id_order'], 12);
                }
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Pedido modificado correctamente', 'accumulated_quantity' => $accumulated_quantity);
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteOrder', function (Request $request, Response $response, $args) use (
    $ordersDao,
    $generalRMStockDao,
    $explosionMaterialsDao,
    $explosionProductsDao,
    $generalExMaterialsDao,
    $generalExProductsDao,
    $licenseDao,
    $clientsDao,
    $generalClientsDao,
    $generalSellersDao,
    $generalProductsDao,
    $generalOrdersDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $lastDataDao,
    $generalRequisitionsMaterialsDao,
    $requisitionsMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOrder = $request->getParsedBody();

    $orders = $ordersDao->findAllOrdersByCompany($id_company);

    $resolution = $ordersDao->deleteOrder($dataOrder['idOrder']);

    if ($resolution == null) {
        $resolution = $programmingRoutesDao->deleteProgrammingRoute($dataOrder['idOrder']);
    }

    if ($resolution == null) {
        // Si solo hay un pedido el cual se va a eliminar, entonces borra todos los requerimientos pendientes
        if (sizeof($orders) == 1) {
            $generalRequisitionsMaterialsDao->deleteAllRequisitionPending();
        } else {
            $cicle = true;

            while ($cicle == true) {
                $cicle = false;
                // Productos
                $arr = $generalExProductsDao->findAllCompositeConsolidated($id_company);
                $products = $generalExProductsDao->setDataEXComposite($arr);

                for ($i = 0; $i < sizeof($products); $i++) {
                    $findEX = $generalExProductsDao->findEXProduct($products[$i]['id_child_product']);

                    if (!$findEX)
                        $resolution = $explosionProductsDao->insertNewEXPByCompany($products[$i], $id_company);
                    else {
                        $products[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                        $resolution = $explosionProductsDao->updateEXProduct($products[$i]);
                    }

                    if (intval($products[$i]['available']) < 0 && abs($products[$i]['available']) > $products[$i]['quantity_material']) {
                        $data = [];
                        $arr2 = $generalOrdersDao->findLastOrderByNumOrder($products[$i]['num_order']);

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
                            $data['idProduct'] = $products[$i]['id_child_product'];
                            $data['idClient'] = $client['id_client'];
                            $data['idSeller'] = $seller['id_seller'];
                            $data['route'] = 1;
                            $data['originalQuantity'] = abs($products[$i]['available']);
                            $data['typeOrder'] = 2;

                            $findOrder = $generalOrdersDao->findLastSameOrder($data);
                            if (!$findOrder) {
                                $cicle = true;
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

                $arr = $generalExProductsDao->findAllChildrenCompositeConsolidaded($id_company);
                $cProducts = $generalExProductsDao->setDataEXComposite($arr);

                for ($i = 0; $i < sizeof($cProducts); $i++) {
                    $findEX = $generalExProductsDao->findEXProduct($cProducts[$i]['id_child_product']);

                    if (!$findEX)
                        $resolution = $explosionProductsDao->insertNewEXPByCompany($cProducts[$i], $id_company);
                    else {
                        $cProducts[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                        $resolution = $explosionProductsDao->updateEXProduct($cProducts[$i]);
                    }

                    if (intval($cProducts[$i]['available']) < 0 && abs($cProducts[$i]['available']) > $cProducts[$i]['quantity_material']) {
                        $data = [];
                        $arr2 = $generalOrdersDao->findLastOrderByNumOrder($cProducts[$i]['num_order']);

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
                            $data['idProduct'] = $cProducts[$i]['id_child_product'];
                            $data['idClient'] = $client['id_client'];
                            $data['idSeller'] = $seller['id_seller'];
                            $data['route'] = 1;
                            $data['originalQuantity'] = abs($cProducts[$i]['available']);
                            $data['typeOrder'] = 2;

                            $findOrder = $generalOrdersDao->findLastSameOrder($data);
                            if (!$findOrder) {
                                $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                                if (isset($resolution['info'])) break;
                                $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                                $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($cProducts[$i]['id_child_product'], $lastOrder['id_order']);

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

                $arr = $generalExMaterialsDao->findAllChildrenMaterialsConsolidaded($id_company);
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
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Pedido eliminado correctamente');
    else if ($resolution['info'])
        $resp = array('info' => true, 'message' => $resolution['info']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar el pedido. Existe información asociada a el');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
