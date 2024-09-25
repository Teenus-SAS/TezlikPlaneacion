<?php

use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\ProductionOrderDao;
use TezlikPlaneacion\dao\ProductionOrderMPDao;
use TezlikPlaneacion\dao\ProductionOrderPartialDao;
use TezlikPlaneacion\dao\UsersProductionOrderPartialDao;

$generalProgrammingDao = new GeneralProgrammingDao();
$productionOrderDao = new ProductionOrderDao();
$productionOrderPartialDao = new ProductionOrderPartialDao();
$productionOrderMPDao = new ProductionOrderMPDao();
$usersProductionOrderPartialDao = new UsersProductionOrderPartialDao();
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

$app->post('/updateOPPartial', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    $dataOP = $request->getParsedBody();

    $resolution = $productionOrderPartialDao->updateOPPartial($dataOP);

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'Orden de producción modificada correctamente'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteOPPartial/{id_part_deliv}', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    $resolution = $productionOrderPartialDao->deleteOPPartial($args['id_part_deliv']);

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'Orden de producción eliminada correctamente'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/saveReceiveOPDate', function (Request $request, Response $response, $args) use (
    $productionOrderPartialDao,
    $usersProductionOrderPartialDao,
    $generalProductsDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];

    $dataOP = $request->getParsedBody();

    $resolution = $productionOrderPartialDao->updateDateReceive($dataOP);

    if ($resolution == null) {
        $resolution = $generalProductsDao->updateAccumulatedQuantity($dataOP['idProduct'], $dataOP['quantity'], 2);
    }

    if ($resolution == null) {
        $resolution = $usersProductionOrderPartialDao->saveUserOPPartial($id_company, $dataOP['idPartDeliv'], $id_user);
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Fecha guardada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/usersOPPartial/{id_part_deliv}', function (Request $request, Response $response, $args) use ($usersProductionOrderPartialDao) {
    $users = $usersProductionOrderPartialDao->findAllUserOPPartialById($args['id_part_deliv']);
    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});

// Material
$app->get('/productionOrderMaterial', function (Request $request, Response $response, $args) use ($productionOrderMPDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productionOrder = $productionOrderMPDao->findAllOPMaterialByCompany($id_company);

    $response->getBody()->write(json_encode($productionOrder));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/productionOrderMaterial/{id_programming}', function (Request $request, Response $response, $args) use ($productionOrderMPDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productionOrder = $productionOrderMPDao->findAllOPMaterialById($args['id_programming'], $id_company);

    $response->getBody()->write(json_encode($productionOrder));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addOPMaterial', function (Request $request, Response $response, $args) use ($productionOrderMPDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    // $id_user = $_SESSION['idUser'];

    $dataOP = $request->getParsedBody();
    // $dataOP['operator'] = $id_user;

    $resolution = $productionOrderMPDao->insertOPMaterialByCompany($dataOP, $id_company);

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'Materia prima entregada correctamente'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updateOPMaterial', function (Request $request, Response $response, $args) use ($productionOrderMPDao) {
    $dataOP = $request->getParsedBody();

    $resolution = $productionOrderMPDao->updateOPMaterial($dataOP);

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'Materia prima modificada correctamente'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteOPMaterial/{id_prod_order_material}', function (Request $request, Response $response, $args) use (
    $productionOrderMPDao
) {
    $resolution = $productionOrderMPDao->deleteOPMaterial($args['id_prod_order_material']);

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'Orden de producción eliminada correctamente'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
