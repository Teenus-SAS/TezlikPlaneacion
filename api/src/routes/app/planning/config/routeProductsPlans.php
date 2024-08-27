<?php

use TezlikPlaneacion\dao\FilesDao;
use TezlikPlaneacion\dao\ProductsPlansDao;

$productsPlansDao = new ProductsPlansDao();
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

/*addProductPlan
updateProductPlan
deleteProductPlan */
