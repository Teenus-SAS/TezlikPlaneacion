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

// Material
$app->get('/productionOrderMaterial', function (Request $request, Response $response, $args) use ($productionOrderMPDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $productionOrderMPDao->findAllOPMaterialByCompany($id_company);

    $response->getBody()->write(json_encode($materials));
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
    $id_user = $_SESSION['idUser'];

    $dataOP = $request->getParsedBody();
    $dataOP['operator'] = $id_user;

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

$app->post('/saveReceiveOPMPDate', function (Request $request, Response $response, $args) use (
    $productionOrderMPDao,
    $usersProductionOrderMPDao,
    $generalMaterialsDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];

    $dataOP = $request->getParsedBody();

    $resolution = $productionOrderMPDao->updateDateReceive($dataOP);

    if ($resolution == null) {
        $resolution = $generalMaterialsDao->updateQuantityMaterial($dataOP['idMaterial'], $dataOP['quantity']);
    }

    if ($resolution == null) {
        $product = $generalProductsDao->findProduct($dataOP, $id_company);

        if ($product) {
            $resolution = $generalProductsDao->updateAccumulatedQuantity($product['id_product'], $dataOP['quantity'], 2);
        }
    }

    if ($resolution == null) {
        $resolution = $usersProductionOrderMPDao->saveUserOPMP($id_company, $dataOP['idOPM'], $id_user);
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

$app->get('/usersOPMP/{id_prod_order_material_user}', function (Request $request, Response $response, $args) use (
    $usersProductionOrderMPDao
) {
    $users = $usersProductionOrderMPDao->findAllUserOPMPById($args['id_prod_order_material_user']);
    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});
