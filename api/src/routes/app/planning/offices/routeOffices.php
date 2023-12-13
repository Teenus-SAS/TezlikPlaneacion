<?php

use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralOfficesDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\OfficesDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;

$officesDao = new OfficesDao();
$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalClientsDao = new GeneralClientsDao();
$generalOfficesDao = new GeneralOfficesDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$generalProductsDao = new GeneralProductsDao();

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

$app->post('/cancelOffice', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $generalOrdersDao,
) {
    $dataOrder = $request->getParsedBody();

    // $order = $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], $dataOrder['quantity'] + $dataOrder['originalQuantity'], 1);
    $order = $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Programar');

    $arr = $generalProductsDao->findProductReserved($dataOrder['idProduct']);
    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
    $generalProductsDao->updateReservedByProduct($dataOrder['idProduct'], $arr['reserved']);

    if ($order == null)
        $resp = array('success' => true, 'message' => 'Despacho cancelado correctamente');
    else if ($order['info'])
        $resp = array('info' => true, 'message' => $order['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/changeOffices', function (Request $request, Response $response, $args) use (
    $officesDao,
    $generalProductsDao,
    $ordersDao,
    $generalOrdersDao,
    $generalClientsDao,
    $productsMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOrder = $request->getParsedBody();

    $resolution = $officesDao->updateDeliveryDate($dataOrder);

    $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Entregado');
    $arr = $generalProductsDao->findProductReserved($dataOrder['idProduct']);
    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
    $generalProductsDao->updateReservedByProduct($dataOrder['idProduct'], $arr['reserved']);

    if ($dataOrder['stock'] > ($dataOrder['quantity'] - $dataOrder['originalQuantity'])) {
        $data = [];
        $arr = $generalOrdersDao->findLastNumOrder($id_company);
        $client = $generalClientsDao->findInternalClient($id_company);

        $data['order'] = $arr['num_order'];
        $data['dateOrder'] = date('Y-m-d');
        $data['minDate'] = '';
        $data['maxDate'] = '';
        $data['idProduct'] = $dataOrder['idProduct'];
        $data['idClient'] = $client['id_client'];
        // $data['originalQuantity'] = $dataOrder['quantity'] - $dataOrder['stock'];
        $data['originalQuantity'] =  $dataOrder['stock'] - ($dataOrder['quantity'] - $dataOrder['originalQuantity']);

        $resolution = $ordersDao->insertOrderByCompany($data, $id_company);

        // $status = true;

        // // Checkear cantidades
        // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($dataOrder['idOrder']);
        // if ($order['status'] != 'En Produccion' && $order['status'] = 'Entregado') {

        //     if ($order['original_quantity'] > $order['accumulated_quantity']) {
        //         // Ficha tecnica
        //         $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($dataOrder['idProduct'], $id_company);

        //         if (sizeof($productsMaterials) == 0) {
        //             $order = $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Sin Ficha Tecnica');
        //             $status = false;
        //         } else {
        //             foreach ($productsMaterials as $arr) {
        //                 if ($arr['quantity_material'] <= 0) {
        //                     $order = $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Sin Materia Prima');
        //                     $status = false;
        //                     break;
        //                 }
        //             }
        //         }
        //     }

        //     if ($status == true) {
        //         if ($order['original_quantity'] <= $order['accumulated_quantity']) {
        //             $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Despacho');
        //             $accumulated_quantity = $order['accumulated_quantity'] - $order['original_quantity'];
        //         } else {
        //             $accumulated_quantity = $order['accumulated_quantity'];
        //             // $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Alistamiento');
        //         }

        //         $arr = $generalProductsDao->findProductReserved($dataOrder['idProduct']);
        //         !$arr['reserved'] ? $arr['reserved'] = 0 : $arr;
        //         $generalProductsDao->updateReservedByProduct($dataOrder['idProduct'], $arr['reserved']);

        //         $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], $accumulated_quantity, 1);
        //     }
        // }
    }

    $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], $dataOrder['quantity'] - $dataOrder['originalQuantity'], 2);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Pedido modificado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
