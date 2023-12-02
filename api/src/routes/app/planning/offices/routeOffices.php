<?php

use TezlikPlaneacion\dao\GeneralOfficesDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\OfficesDao;

$officesDao = new OfficesDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalOfficesDao = new GeneralOfficesDao();
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
    $generalOrdersDao
) {
    $dataOrder = $request->getParsedBody();

    // $order = $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], $dataOrder['quantity'] + $dataOrder['originalQuantity'], 1);
    $order = $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Programar');

    $arr = $generalProductsDao->findProductReserved($dataOrder['idProduct']);
    !$arr['reserved'] ? $arr['reserved'] = 0 : $arr;
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
    $generalOrdersDao
) {
    $dataOrder = $request->getParsedBody();

    $order = $officesDao->updateDeliveryDate($dataOrder);

    $generalOrdersDao->changeStatus($dataOrder['idOrder'], 'Entregado');
    $arr = $generalProductsDao->findProductReserved($dataOrder['idProduct']);
    !$arr['reserved'] ? $arr['reserved'] = 0 : $arr;
    $generalProductsDao->updateReservedByProduct($dataOrder['idProduct'], $arr['reserved']);

    $generalProductsDao->updateAccumulatedQuantity($dataOrder['idProduct'], $dataOrder['quantity'] - $dataOrder['originalQuantity'], 2);

    if ($order == null)
        $resp = array('success' => true, 'message' => 'Pedido modificado correctamente');
    else if ($order['info'])
        $resp = array('info' => true, 'message' => $order['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
