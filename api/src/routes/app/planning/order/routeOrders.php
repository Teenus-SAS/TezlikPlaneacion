<?php

use TezlikPlaneacion\dao\ClientsDao;
use TezlikPlaneacion\dao\ConvertDataDao;
use TezlikPlaneacion\dao\DeliveryDateDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralOrderTypesDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\GeneralSellersDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\MallasDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\RequisitionsDao;

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
$mallasDao = new MallasDao();
$deliveryDateDao = new DeliveryDateDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$lastDataDao = new LastDataDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$requisitionsDao = new RequisitionsDao();
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
                empty($order[$i]['email'])  || empty($order[$i]['order'])  || empty($order[$i]['dateOrder']) ||
                empty($order[$i]['minDate']) || empty($order[$i]['maxDate']) || empty($order[$i]['originalQuantity'])
            ) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            if (
                trim($order[$i]['referenceProduct']) == '' || trim($order[$i]['product']) == '' || trim($order[$i]['client']) == '' ||
                trim($order[$i]['email']) == '' || trim($order[$i]['order']) == '' || trim($order[$i]['dateOrder']) == '' ||
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
                        'order' => $arr['order'],
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
    $generalPlanCiclesMachinesDao,
    $filterDataDao,
    $generalSellersDao,
    $explosionMaterialsDao,
    $generalRequisitionsDao,
    $requisitionsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOrder = $request->getParsedBody();

    $dataOrders = sizeof($dataOrder);

    if ($dataOrders > 1) {
        // $import = false;

        $dataOrder = $convertDataDao->changeDateOrder($dataOrder);

        $order = $ordersDao->insertOrderByCompany($dataOrder, $id_company);

        $arr = $lastDataDao->findLastInsertedOrder($id_company);
        $dataOrder['idOrder'] = $arr['id_order'];
        $dataOrder['route'] = 1;

        // $cicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($dataOrder['idProduct'], $id_company);

        // if (sizeof($cicles) > 0)
        //     $order = $programmingRoutesDao->insertProgrammingRoutes($dataOrder, $id_company);

        $data[0] = $dataOrder['order'] . '-' . $dataOrder['idProduct'];

        if ($order == null)
            $resp = array('success' => true, 'message' => 'Pedido ingresado correctamente');
        else if (isset($order['info']))
            $resp = array('info' => true, 'message' => $order['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
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

            $k = $generalOrdersDao->findSameOrder($arr);
            if ($k) $arr['originalQuantity'] += $k['original_quantity'];

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
                    'order' => $arr['order'],
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
                $resolution = $ordersDao->insertOrderByCompany($importOrder[$i], $id_company);

                $arr = $lastDataDao->findLastInsertedOrder($id_company);
                // $order[$i]['idOrder'] = $arr['id_order'];
                // $order[$i]['route'] = 1;

                // $cicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($order[$i]['idProduct'], $id_company);

                // if (sizeof($cicles) > 0)
                //     $resolution = $programmingRoutesDao->insertProgrammingRoutes($order[$i], $id_company);
            } else {
                $importOrder[$i]['idOrder'] = $findOrder['id_order'];
                $resolution = $ordersDao->updateOrder($importOrder[$i]);
            }
            // Obtener todos los pedidos
            $data[$i] = $importOrder[$i]['order'] . '-' . $importOrder[$i]['idProduct'];
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Pedido importado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al importar el pedido. Intente nuevamente');
    }

    // $result = $generalProductsDao->updateAccumulatedQuantityGeneral($id_company);

    // if (!$result) {
    //     $allOrders = $generalOrdersDao->findAllOrdersWithMaterialsByCompany($id_company);
    //     $status = true;

    //     foreach ($allOrders as $arr) {
    //         if ($arr['original_quantity'] > $arr['accumulated_quantity']) {
    //             if ($arr['status_ds'] == 0)
    //                 $generalOrdersDao->changeStatus($arr['id_order'], 5); //5 sin ficha tecnica MP
    //             else if ($arr['quantity_material'] <= 0)
    //                 $generalOrdersDao->changeStatus($arr['id_order'], 6); // 6 sin cantidad materia prima
    //             $status = false;
    //         }

    //         foreach ($allOrders as &$order) {
    //             if ((!isset($arr['status_mp']) || $arr['status_mp'] === false) && $order['id_order'] == $arr['id_order']) {
    //                 // if ($order['id_order'] == $arr['id_order']) {
    //                 $order['status_mp'] = $status;
    //             }
    //         }
    //         unset($order);
    //     }

    //     $orders = $filterDataDao->filterDuplicateArray($allOrders, 'id_order');

    //     for ($i = 0; $i < sizeof($orders); $i++) {
    //         if (isset($orders[$i]['status_mp']) && $orders[$i]['status_mp'] == true) {
    //             if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
    //                 $generalOrdersDao->changeStatus($orders[$i]['id_order'], 2);
    //                 $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
    //             } else {
    //                 $accumulated_quantity = $orders[$i]['accumulated_quantity'];
    //             }

    //             if ($orders[$i]['status'] != 2) {
    //                 $date = date('Y-m-d');

    //                 $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
    //             }

    //             $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
    //             !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
    //             $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

    //             $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
    //         }
    //     }

    //     foreach ($orders as &$order) {
    //         $order['concate'] = $order['num_order'] . '-' . $order['id_product'];
    //     }

    //     $arrayBD = [];
    //     for ($i = 0; $i < sizeof($orders); $i++) {
    //         array_push($arrayBD, $orders[$i]['concate']);
    //     }

    //     $tam_arrayBD = sizeof($arrayBD);
    //     $tam_result = sizeof($data);

    //     if ($tam_arrayBD > $tam_result)
    //         $array_diff = array_diff($arrayBD, $data);
    //     else
    //         $array_diff = array_diff($data, $arrayBD);

    //     //reindezar array
    //     $array_diff = array_values($array_diff);

    //     if ($array_diff)
    //         for ($i = 0; $i < sizeof($array_diff); $i++) {
    //             $posicion =  strrpos($array_diff[$i], '-');
    //             $id_product = substr($array_diff[$i], $posicion + 1);
    //             $order = substr($array_diff[$i], 0, $posicion);
    //             $generalOrdersDao->changeStatusOrder($order, $id_product);
    //         }
    // }

    // Cambiar estado pedidos
    $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

    for ($i = 0; $i < sizeof($orders); $i++) {
        $status = true;
        // Checkear cantidades
        // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);
        if ($orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'PROGRAMADO' && $orders[$i]['status'] != 'FABRICADO') {
            if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);
                $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

                if (sizeof($productsMaterials) == 0 || sizeof($planCicles) == 0) {
                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 5);
                    $status = false;
                } else {
                    foreach ($productsMaterials as $arr) {
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
                    // $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Alistamiento');
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
    }

    // $result = $generalOrdersDao->findAllOrdersConcat($id_company);

    $arrayBD = [];
    for ($i = 0; $i < sizeof($orders); $i++) {
        array_push($arrayBD, $orders[$i]['concate']);
    }

    $tam_arrayBD = sizeof($arrayBD);
    $tam_result = sizeof($data);

    if ($tam_arrayBD > $tam_result)
        $array_diff = array_diff($arrayBD, $data);
    else
        $array_diff = array_diff($data, $arrayBD);

    //reindezar array
    $array_diff = array_values($array_diff);

    if ($array_diff)
        for ($i = 0; $i < sizeof($array_diff); $i++) {
            $posicion =  strrpos($array_diff[$i], '-');
            $id_product = substr($array_diff[$i], $posicion + 1);
            $order = substr($array_diff[$i], 0, $posicion);
            $generalOrdersDao->changeStatusOrder($order, $id_product);
        }
    // else if (sizeof($array_diff) == 0)

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

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateOrder', function (Request $request, Response $response, $args) use (
    $ordersDao,
    $generalOrdersDao,
    $generalProductsDao,
    $convertDataDao,
    $generalRMStockDao,
    $generalPlanCiclesMachinesDao,
    $explosionMaterialsDao,
    $requisitionsDao,
    $generalRequisitionsDao,
    $productsMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOrder = $request->getParsedBody();

    if (empty($dataOrder['order']) || empty($dataOrder['idProduct']) || empty($dataOrder['idClient']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $dataOrder = $convertDataDao->changeDateOrder($dataOrder);

        $result = $ordersDao->updateOrder($dataOrder);
        $result = $generalProductsDao->updateAccumulatedQuantityGeneral($id_company);

        $status = true;

        // $allOrders = $generalOrdersDao->findAllOrdersWithMaterialsByCompany($id_company);

        // foreach ($allOrders as $arr) {
        //     if ($arr['original_quantity'] > $arr['accumulated_quantity']) {
        //         if ($arr['quantity_material'] == NULL || !$arr['quantity_material']) {
        //             $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Ficha Tecnica');
        //             $status = false;
        //             // break;
        //         } else if ($arr['quantity_material'] <= 0) {
        //             $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Materia Prima');
        //             $status = false;
        //             // break;
        //         }
        //     }

        //     foreach ($allOrders as &$order) {
        //         if ((!isset($arr['status_mp']) || $arr['status_mp'] === false) && $order['id_order'] == $arr['id_order']) {
        //             // if ($order['id_order'] == $arr['id_order']) {
        //             $order['status_mp'] = $status;
        //         }
        //     }
        //     unset($order);
        // }

        // $orders = $filterDataDao->filterDuplicateArray($allOrders, 'id_order');

        // for ($i = 0; $i < sizeof($orders); $i++) {
        //     if ($orders[$i]['status_mp'] == true) {
        //         if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
        //             $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Despacho');
        //             $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
        //         } else {
        //             $accumulated_quantity = $orders[$i]['accumulated_quantity'];
        //         }

        //         if ($orders[$i]['status'] != 'Despacho') {
        //             $date = date('Y-m-d');

        //             $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
        //         }

        //         $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
        //         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
        //         $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

        //         $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
        //     }
        // }


        // Checkear cantidades
        $order = $generalOrdersDao->checkAccumulatedQuantityOrder($dataOrder['idOrder']);
        if ($order['status'] != 'EN PRODUCCION' && $order['status'] != 'FABRICADO' && $order['status'] != 'PROGRAMADO') {
            if ($order['original_quantity'] > $order['accumulated_quantity']) {
                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($dataOrder['idProduct'], $id_company);
                $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($dataOrder['idProduct'], $id_company);

                if (sizeof($productsMaterials) == 0 || sizeof($planCicles) == 0) {
                    $generalOrdersDao->changeStatus($dataOrder['idOrder'], 5);
                    $status = false;
                } else {
                    foreach ($productsMaterials as $arr) {
                        if ($arr['quantity_material'] <= 0) {
                            $generalOrdersDao->changeStatus($dataOrder['idOrder'], 6);
                            $status = false;
                            break;
                        }
                    }
                }
            }

            if ($status == true) {
                if ($order['original_quantity'] <= $order['accumulated_quantity']) {
                    $generalOrdersDao->changeStatus($dataOrder['idOrder'], 2);
                    $accumulated_quantity = $order['accumulated_quantity'] - $order['original_quantity'];
                } else {
                    $accumulated_quantity = $order['accumulated_quantity'];
                    // $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Alistamiento');
                }

                if ($order['status'] != 'DESPACHO') {
                    $date = date('Y-m-d');

                    $generalOrdersDao->updateOfficeDate($dataOrder['idOrder'], $date);
                }

                $arr = $generalProductsDao->findProductReserved($dataOrder['idProduct']);
                !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                $generalProductsDao->updateReservedByProduct($dataOrder['idProduct'], $arr['reserved']);

                $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], $accumulated_quantity, 1);
            }
        }

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

        if ($result == null)
            $resp = array('success' => true, 'message' => 'Pedido modificado correctamente');
        else if (isset($result['info']))
            $resp = array('info' => true, 'message' => $result['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteOrder', function (Request $request, Response $response, $args) use (
    $ordersDao,
    $generalRMStockDao,
    $explosionMaterialsDao,
    $generalRequisitionsDao,
    $requisitionsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOrder = $request->getParsedBody();

    $orders = $ordersDao->findAllOrdersByCompany($id_company);

    $resolution = $ordersDao->deleteOrder($dataOrder['idOrder']);

    if ($resolution == null) {
        // Si solo hay un pedido el cual se va a eliminar, entonces borra todos los requerimientos pendientes
        if (sizeof($orders) == 1) {
            $generalRequisitionsDao->deleteAllRequisitionPending();
        } else {
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
                    $data['purchaseOrder'] = '';
                    $data['requestedQuantity'] = 0;

                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                    if ($requisition) {
                        $available = abs($materials[$i]['available']);
                        // $quantity_required = floatval($requisition['quantity_required']);

                        $data['requiredQuantity'] = $available;

                        if ($data['requiredQuantity'] <= 0 || $data['requiredQuantity'] < 0.01) {
                            $requisitionsDao->deleteRequisition($requisition['id_requisition']);
                        } else {
                            $data['idRequisition'] = $requisition['id_requisition'];
                            $generalRequisitionsDao->updateRequisitionAuto($data);
                        }
                    }
                } else {
                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);
                    if ($requisition) {
                        $requisitionsDao->deleteRequisition($requisition['id_requisition']);
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
