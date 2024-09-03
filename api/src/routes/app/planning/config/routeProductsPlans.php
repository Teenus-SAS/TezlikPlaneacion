<?php

use TezlikPlaneacion\dao\FilesDao;
use TezlikPlaneacion\dao\GeneralProductsPlansDao;
use TezlikPlaneacion\dao\ProductsPlansDao;

$productsPlansDao = new ProductsPlansDao();
$generalProductsPlansDao = new GeneralProductsPlansDao();
$filesDao = new FilesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productsPlans/{id_product}', function (Request $request, Response $response, $args) use ($productsPlansDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $products = $productsPlansDao->findAllProductsPlansByCompany($id_company, $args['id_product']);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductPlan', function (Request $request, Response $response, $args) use (
    $productsPlansDao,
    $filesDao,
    $generalProductsPlansDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    $findProduct = $generalProductsPlansDao->findProductPlans($dataProduct['idProduct']);

    if (!$findProduct) {
        $resolution = null;

        $mechanicalFile = $filesDao->saveProductsPlansFile($_FILES['mechanicalPlaneFile'], $id_company);

        if (isset($mechanicalFile['info']))
            $resolution = $mechanicalFile;

        $assemblyFile = $filesDao->saveProductsPlansFile($_FILES['assemblyPlaneFile'], $id_company);

        if (isset($assemblyFile['info']))
            $resolution = $assemblyFile;

        if ($resolution == null) {
            $dataProduct['mechanicalFile'] = $mechanicalFile;
            $dataProduct['assemblyFile'] = $assemblyFile;

            $resolution = $productsPlansDao->insertProductPlanByCompany($dataProduct, $id_company);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Planos creados correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $resp = array('info' => true, 'message' => 'Plano de producto ya existente. Ingrese uno nuevo');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProductPlan', function (Request $request, Response $response, $args) use (
    $productsPlansDao,
    $generalProductsPlansDao,
    $filesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    $resolution = null;
    $mechanicalFile = $filesDao->saveProductsPlansFile($_FILES['mechanicalPlaneFile'], $id_company);

    if (isset($mechanicalFile['info']))
        $resolution = $mechanicalFile;

    $assemblyFile = $filesDao->saveProductsPlansFile($_FILES['assemblyPlaneFile'], $id_company);

    if (isset($assemblyFile['info']))
        $resolution = $assemblyFile;

    if ($resolution == null) {
        $dataProduct['mechanicalFile'] = $mechanicalFile;
        $dataProduct['assemblyFile'] = $assemblyFile;

        $resolution = $productsPlansDao->updateProductPlan($dataProduct);
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Planos modificados correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras guardaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteProductPlan/{id_product_plan}', function (Request $request, Response $response, $args) use ($productsPlansDao) {
    $resolution = $productsPlansDao->deleteProductPlan($args['id_product_plan']);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Planos eliminados correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras eliminaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
