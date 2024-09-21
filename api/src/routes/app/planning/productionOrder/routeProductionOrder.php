<?php

use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\ProductionOrderDao;
use TezlikPlaneacion\dao\ProductionOrderPartialDao;

$generalProgrammingDao = new GeneralProgrammingDao();
$productionOrderDao = new ProductionOrderDao();
$productionOrderPartialDao = new ProductionOrderPartialDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProductsDao = new GeneralProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/productionOrder', function (Request $request, Response $response, $args) use (
    $productionOrderDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $programming = $productionOrderDao->findAllProductionOrder($id_company);
    $response->getBody()->write(json_encode($programming));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/changeStatusOP', function (Request $request, Response $response, $args) use (
    $generalOrdersDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOP = $request->getParsedBody();

    $result = $generalOrdersDao->changeStatus($dataOP['idOrder'], 8);
    $generalProductsDao->updateAccumulatedQuantity($dataOP['idProduct'], $dataOP['quantity'], 2);

    // Cambiar estado pedidos
    $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

    for ($i = 0; $i < sizeof($orders); $i++) {
        // Checkear cantidades
        // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);
        if (
            $orders[$i]['status'] != 'EN PRODUCCION' && /* $orders[$i]['status'] != 'PROGRAMADO' &&*/ $orders[$i]['status'] != 'FABRICADO' &&
            $orders[$i]['status'] != 'DESPACHO' && $orders[$i]['status'] != 'SIN MATERIA PRIMA' && $orders[$i]['status'] != 'SIN FICHA TECNICA'
        ) {
            if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 2);
                $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
            } else {
                $accumulated_quantity = $orders[$i]['accumulated_quantity'];
            }

            if ($orders[$i]['status'] != 'DESPACHO') {
                $date = Date('Y-m-d');

                $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
            }

            $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
            !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
            $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

            $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
        }
    }

    if ($result == null)
        $resp = array('success' => true, 'message' => 'Programa de producción eliminado correctamente');
    else if (isset($result['info']))
        $resp = array('info' => true, 'message' => $result['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/changeFlagOP/{id_programming}/{flag}', function (Request $request, Response $response, $args) use ($generalProgrammingDao) {
    $resolution = $generalProgrammingDao->changeFlagProgramming($args['id_programming'], $args['flag']);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Orden de produccion modificada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras modificaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/productionOrderPartial', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productionOrder = $productionOrderPartialDao->findAllOPPartialBycompany($id_company);

    $response->getBody()->write(json_encode($productionOrder));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/productionOrderPartial/{id_programming}', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productionOrder = $productionOrderPartialDao->findAllOPPartialById($args['id_programming'], $id_company);

    $response->getBody()->write(json_encode($productionOrder));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addOPPartial', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];

    $dataOP = $request->getParsedBody();
    $dataOP['operator'] = $id_user;

    $resolution = $productionOrderPartialDao->insertOPPartialByCompany($dataOP, $id_company);

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'Orden de producción entregada correctamente'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/saveReceiveOCDate', function (Request $request, Response $response, $args) use (
    $productionOrderPartialDao,
    $generalProductsDao,
) {
    // session_start();
    // $id_company = $_SESSION['id_company'];
    // $id_user = $_SESSION['idUser'];

    $dataOP = $request->getParsedBody();

    $resolution = $productionOrderPartialDao->updateDateReceive($dataOP);

    if ($resolution == null) {
        $resolution = $generalProductsDao->updateAccumulatedQuantity($dataOP['idProduct'], $dataOP['quantity'], 2);
    }

    // if ($requisition == null) {
    //     $requisition = $usersRequisitonsDao->saveUserDeliverRequisition($id_company, $dataRequisition['idRequisition'], $id_user);
    // } 

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Fecha guardada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
