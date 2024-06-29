<?php

use TezlikPlaneacion\dao\ClassificationDao;
use TezlikPlaneacion\dao\CompaniesLicenseStatusDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralUnitSalesDao;
use TezlikPlaneacion\dao\InventoryABCDao;
use TezlikPlaneacion\dao\ProductsDao;

$inventoryABCDao = new InventoryABCDao();
$companiesLicenseDao = new CompaniesLicenseStatusDao();
$generalUnitSalesDao = new GeneralUnitSalesDao();
$classificationDao = new ClassificationDao();
$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/inventoryABC', function (Request $request, Response $response, $args) use ($inventoryABCDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $inventory = $inventoryABCDao->findAllInventoryABCByComapny($id_company);
    $response->getBody()->write(json_encode($inventory, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// $app->post('/categoriesDataValidation', function (Request $request, Response $response, $args) use ($generalCategoriesDao) {
//     $dataInventory = $request->getParsedBody();

//     if (isset($dataInventory)) {

//         $insert = 0;
//         $update = 0;

//         $categories = $dataInventory['importCategories'];

//         for ($i = 0; $i < sizeof($categories); $i++) {

//             if (empty($categories[$i]['category']) || empty($categories[$i]['typeCategory'])) {
//                 $i = $i + 2;
//                 $dataimportCategories = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
//                 break;
//             } else {
//                 $findCategory = $generalCategoriesDao->findCategory($categories[$i]);
//                 if (!$findCategory) $insert = $insert + 1;
//                 else $update = $update + 1;
//                 $dataimportCategories['insert'] = $insert;
//                 $dataimportCategories['update'] = $update;
//             }
//         }
//     } else
//         $dataimportCategories = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

//     $response->getBody()->write(json_encode($dataimportCategories, JSON_NUMERIC_CHECK));
//     return $response->withHeader('Content-Type', 'application/json');
// });

$app->post('/addInventoryABC', function (Request $request, Response $response, $args) use (
    $inventoryABCDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataInventory = $request->getParsedBody();

    // if (empty($dataInventory['importCategories'])) {
    $category = $inventoryABCDao->insertInventoryABC($dataInventory, $id_company);

    if ($category == null)
        $resp = array('success' => true, 'message' => 'Inventario creada correctamente');
    else if (isset($category['info']))
        $resp = array('info' => true, 'message' => $category['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    // } else {
    //     $categories = $dataInventory['importCategories'];

    //     for ($i = 0; $i < sizeof($categories); $i++) {
    //         $findCategory = $generalCategoriesDao->findCategory($categories[$i]);
    //         if (!$findCategory)
    //             $resolution = $invCategoriesDao->insertCategory($categories[$i]);
    //         else {
    //             $categories[$i]['idCategory'] = $findCategory['id_category'];
    //             $resolution = $invCategoriesDao->updateCategory($categories[$i]);
    //         }
    //     }
    //     if ($resolution == null)
    //         $resp = array('success' => true, 'message' => 'Inventario importada correctamente');
    //     else
    //         $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    // }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateInventoryABC', function (Request $request, Response $response, $args) use (
    $inventoryABCDao,
    $companiesLicenseDao,
    $productsDao,
    $classificationDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataInventory = $request->getParsedBody();

    $resolution = $inventoryABCDao->updateInventoryABC($dataInventory);

    if ($resolution == null) {
        $license = $companiesLicenseDao->status($id_company);

        if ($license['months'] > 0) {
            $products = $productsDao->findAllProductsByCompany($id_company);

            $resolution = $generalProductsDao->updateGeneralClassification($id_company);

            for ($i = 0; $i < sizeof($products); $i++) {
                if (isset($resolution)) break;
                $inventory = $classificationDao->calcInventoryABCBYProduct($products[$i]['id_product'], $license['months']);

                $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);

                $resolution = $classificationDao->updateProductClassification($products[$i]['id_product'], $inventory['classification']);
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Inventario actualizado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

// $app->get('/deleteCategory/{id_category}', function (Request $request, Response $response, $args) use ($invCategoriesDao) {
//     $category = $invCategoriesDao->deleteCategory($args['id_category']);

//     if ($category == null)
//         $resp = array('success' => true, 'message' => 'Inventario eliminada correctamente');
//     else if (isset($category['info']))
//         $resp = array('info' => true, 'message' => $category['message']);
//     else if ($category != null)
//         $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });
