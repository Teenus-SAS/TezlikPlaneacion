<?php

use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\ProductionOrderDao;
use TezlikPlaneacion\dao\ProductionOrderMPDao;
use TezlikPlaneacion\dao\ProductionOrderPartialDao;
use TezlikPlaneacion\dao\UsersProductionOrderMPDao;
use TezlikPlaneacion\dao\UsersProductionOrderPartialDao;

$generalProgrammingDao = new GeneralProgrammingDao();
$productionOrderDao = new ProductionOrderDao();
$productionOrderPartialDao = new ProductionOrderPartialDao();
$productionOrderMPDao = new ProductionOrderMPDao();
$usersProductionOrderPartialDao = new UsersProductionOrderPartialDao();
$usersProductionOrderMPDao = new UsersProductionOrderMPDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProductsDao = new GeneralProductsDao();
$generalMaterialsDao = new GeneralMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productionOrderPartial', function (Request $request, Response $response, $args) use (
    $productionOrderPartialDao
) {
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

$app->post('/saveReceiveOPPTDate', function (Request $request, Response $response, $args) use (
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

$app->get('/usersOPPT/{id_part_deliv}', function (Request $request, Response $response, $args) use ($usersProductionOrderPartialDao) {
    $users = $usersProductionOrderPartialDao->findAllUserOPPartialById($args['id_part_deliv']);
    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});
