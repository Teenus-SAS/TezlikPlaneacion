<?php

use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\ExplosionProductsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralExplosionProductsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\GeneralSellersDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\RequisitionsDao;

$explosionMaterialsDao = new ExplosionMaterialsDao();
$explosionProductsDao = new ExplosionProductsDao();
$generalExMaterialsDao = new GeneralExplosionMaterialsDao();
$generalExProductsDao = new GeneralExplosionProductsDao();
$requisitionsDao = new RequisitionsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$generalRMStockDao = new GeneralRMStockDao();
$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalClientsDao = new GeneralClientsDao();
$generalSellersDao = new GeneralSellersDao();
$generalProductsDao = new GeneralProductsDao();
$lastDataDao = new LastDataDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/explosionMaterials', function (Request $request, Response $response, $args) use (
    $explosionMaterialsDao,
    $explosionProductsDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $products = $explosionProductsDao->findAllExplosionProductsByCompany($id_company);
    $materials = $explosionMaterialsDao->findAllExplosionMaterialsByCompany($id_company);

    $explosion = array_merge($materials, $products);

    $response->getBody()->write(json_encode($explosion, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
