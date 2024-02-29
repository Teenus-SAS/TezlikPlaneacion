<?php

use TezlikPlaneacion\dao\ClientsDao;
use TezlikPlaneacion\dao\ConvertDataDao;
use TezlikPlaneacion\dao\DeliveryDateDao;
use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralOrderTypesDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\MallasDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;

$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProductsDao = new GeneralProductsDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new GeneralProductsDao();
$clientsDao = new ClientsDao();
$generalClientsDao = new GeneralClientsDao();
$orderTypesDao = new GeneralOrderTypesDao();
$mallasDao = new MallasDao();
$deliveryDateDao = new DeliveryDateDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$lastDataDao = new LastDataDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$filterDataDao = new FilterDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/orders', function (Request $request, Response $response, $args) use (
    $ordersDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $orders = $ordersDao->findAllOrdersByCompany($id_company);

    $response->getBody()->write(json_encode($orders, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/orders/{id_order}', function (Request $request, Response $response, $args) use ($generalOrdersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $data['order'] = $args['id_order'];
    $order = $generalOrdersDao->findOrdersByCompany($data, $id_company);
    $response->getBody()->write(json_encode($order, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/orderDataValidation', function (Request $request, Response $response, $args) use (
    $generalOrdersDao,
    $productsDao,
    $clientsDao,
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

        for ($i = 0; $i < sizeof($order); $i++) {
            if (
                empty($order[$i]['referenceProduct'])  || empty($order[$i]['product']) || empty($order[$i]['client']) ||
                empty($order[$i]['order'])  || empty($order[$i]['dateOrder']) || empty($order[$i]['minDate']) ||
                empty($order[$i]['maxDate']) || empty($order[$i]['originalQuantity'])
            ) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($order[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Producto no existe en la base de datos.<br>Fila: {$i}");
                break;
            } else $order[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id cliente
            $findClient = $generalClientsDao->findClientByName($order[$i], $id_company, 1);
            if (!$findClient) {
                $i = $i + 2;
                $dataImportOrder = array('error' => true, 'message' => "Cliente no existe en la base de datos o es tipo proveedor.<br>Fila: {$i}");
                break;
            } else $order[$i]['idClient'] = $findClient['id_client'];

            $findOrder = $generalOrdersDao->findOrder($order[$i], $id_company);
            !$findOrder ? $insert = $insert + 1 : $update = $update + 1;
            $dataImportOrder['insert'] = $insert;
            $dataImportOrder['update'] = $update;
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
    $programmingRoutesDao,
    $generalProductsDao,
    $generalClientsDao,
    $lastDataDao,
    $productsMaterialsDao,
    $generalPlanCiclesMachinesDao,
    $filterDataDao
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

        $cicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($dataOrder['idProduct'], $id_company);

        if (sizeof($cicles) > 0)
            $order = $programmingRoutesDao->insertProgrammingRoutes($dataOrder, $id_company);

        $data[0] = $dataOrder['order'] . '-' . $dataOrder['idProduct'];

        if ($order == null)
            $resp = array('success' => true, 'message' => 'Pedido ingresado correctamente');
        else if (isset($order['info']))
            $resp = array('info' => true, 'message' => $order['info']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $order = $dataOrder['importOrder'];
        // $import = true;

        for ($i = 0; $i < sizeof($order); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($order[$i], $id_company);
            $order[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id cliente
            $findClient = $generalClientsDao->findClientByName($order[$i], $id_company, 1);
            $order[$i]['idClient'] = $findClient['id_client'];

            $order[$i] = $convertDataDao->changeDateOrder($order[$i]);

            // Consultar pedido
            $findOrder = $generalOrdersDao->findOrder($order[$i], $id_company);
            if (!$findOrder) {
                $resolution = $ordersDao->insertOrderByCompany($order[$i], $id_company);

                $arr = $lastDataDao->findLastInsertedOrder($id_company);
                $order[$i]['idOrder'] = $arr['id_order'];
                $order[$i]['route'] = 1;

                $cicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($order[$i]['idProduct'], $id_company);

                if (sizeof($cicles) > 0)
                    $resolution = $programmingRoutesDao->insertProgrammingRoutes($order[$i], $id_company);
            } else {
                $order[$i]['idOrder'] = $findOrder['id_order'];
                $resolution = $ordersDao->updateOrder($order[$i]);
            }
            // Obtener todos los pedidos
            $data[$i] = $order[$i]['order'] . '-' . $order[$i]['idProduct'];
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Pedido importado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error al importar el pedido. Intente nuevamente');
    }

    $result = $generalProductsDao->updateAccumulatedQuantityGeneral($id_company);

    if ($result == null) {
        $allOrders = $generalOrdersDao->findAllOrdersWithMaterialsByCompany($id_company);
        $status = true;

        foreach ($allOrders as $arr) {
            if ($arr['original_quantity'] > $arr['accumulated_quantity']) {
                if ($arr['quantity_material'] == NULL || !$arr['quantity_material']) {
                    $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Ficha Tecnica');
                    $status = false;
                    break;
                } else if ($arr['quantity_material'] <= 0) {
                    $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Materia Prima');
                    $status = false;
                    break;
                }
            }

            foreach ($allOrders as &$order) {
                if ((!isset($arr['status_mp']) || $arr['status_mp'] === false) && $order['id_order'] == $arr['id_order']) {
                    // if ($order['id_order'] == $arr['id_order']) {
                    $order['status_mp'] = $status;
                }
            }
            unset($order);
        }

        $orders = $filterDataDao->filterDuplicateArray($allOrders, 'id_order');

        for ($i = 0; $i < sizeof($orders); $i++) {
            if (isset($orders[$i]['status_mp']) && $orders[$i]['status_mp'] == true) {
                if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Despacho');
                    $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
                } else {
                    $accumulated_quantity = $orders[$i]['accumulated_quantity'];
                }

                if ($orders[$i]['status'] != 'Despacho') {
                    $date = date('Y-m-d');

                    $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
                }

                $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
                !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
            }
        }

        foreach ($orders as &$order) {
            $order['concate'] = $order['num_order'] . '-' . $order['id_product'];
        }

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
    }
    /* 
        // Cambiar estado pedidos
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Checkear cantidades
            $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);
            if ($order['status'] != 'En Produccion' && $order['status'] != 'Entregado' && $order['status'] != 'Fabricado') {
                if ($order['original_quantity'] > $order['accumulated_quantity']) {
                    // Ficha tecnica
                    $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

                    if (sizeof($productsMaterials) == 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Sin Ficha Tecnica');
                        $status = false;
                    } else {
                        foreach ($productsMaterials as $arr) {
                            if ($arr['quantity_material'] <= 0) {
                                $order = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Sin Materia Prima');
                                $status = false;
                                break;
                            }
                        }
                    }
                }

                if ($status == true) {
                    if ($order['original_quantity'] <= $order['accumulated_quantity']) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Despacho');
                        $accumulated_quantity = $order['accumulated_quantity'] - $order['original_quantity'];
                    } else {
                        $accumulated_quantity = $order['accumulated_quantity'];
                        // $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Alistamiento');
                    }

                    if ($order['status'] != 'Despacho') {
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

        $result = $generalOrdersDao->findAllOrdersConcat($id_company);

        $arrayBD = [];
        for ($i = 0; $i < sizeof($result); $i++) {
            array_push($arrayBD, $result[$i]['concate']);
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
                $result = $generalOrdersDao->changeStatusOrder($order, $id_product);
            }
        else if (sizeof($array_diff) == 0)
            $result = null;
    */

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateOrder', function (Request $request, Response $response, $args) use (
    $ordersDao,
    $generalOrdersDao,
    $generalProductsDao,
    $convertDataDao,
    $filterDataDao,
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
        if ($order['status'] != 'En Produccion' && $order['status'] != 'Entregado' && $order['status'] != 'Fabricado') {
            if ($order['original_quantity'] > $order['accumulated_quantity']) {
                // Ficha tecnica
                $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($dataOrder['idProduct'], $id_company);

                if (sizeof($productsMaterials) == 0) {
                    $order = $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Sin Ficha Tecnica');
                    $status = false;
                } else {
                    foreach ($productsMaterials as $arr) {
                        if ($arr['quantity_material'] <= 0) {
                            $order = $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Sin Materia Prima');
                            $status = false;
                            break;
                        }
                    }
                }
            }

            if ($status == true) {
                if ($order['original_quantity'] <= $order['accumulated_quantity']) {
                    $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Despacho');
                    $accumulated_quantity = $order['accumulated_quantity'] - $order['original_quantity'];
                } else {
                    $accumulated_quantity = $order['accumulated_quantity'];
                    // $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Alistamiento');
                }

                if ($order['status'] != 'Despacho') {
                    $date = date('Y-m-d');

                    $generalOrdersDao->updateOfficeDate($dataOrder['idOrder'], $date);
                }

                $arr = $generalProductsDao->findProductReserved($dataOrder['idProduct']);
                !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                $generalProductsDao->updateReservedByProduct($dataOrder['idProduct'], $arr['reserved']);

                $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], $accumulated_quantity, 1);
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

$app->get('/deleteOrder/{id_order}', function (Request $request, Response $response, $args) use ($ordersDao) {
    $order = $ordersDao->deleteOrder($args['id_order']);
    // $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], 0, 1);

    if ($order == null)
        $resp = array('success' => true, 'message' => 'Pedido eliminado correctamente');
    else if ($order['info'])
        $resp = array('info' => true, 'message' => $order['info']);
    else
        $resp = array('error' => true, 'message' => 'No se pudo eliminar el pedido. Existe información asociada a el');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
