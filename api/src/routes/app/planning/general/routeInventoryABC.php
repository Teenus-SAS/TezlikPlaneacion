<?php

use TezlikPlaneacion\dao\ClassificationDao;
use TezlikPlaneacion\dao\CompaniesLicenseStatusDao;
use TezlikPlaneacion\Dao\GeneralCompositeProductsDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralUnitSalesDao;
use TezlikPlaneacion\dao\InventoryABCDao;
use TezlikPlaneacion\dao\ProductsDao;

$inventoryABCDao = new InventoryABCDao();
$companiesLicenseDao = new CompaniesLicenseStatusDao();
$generalUnitSalesDao = new GeneralUnitSalesDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
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

$app->post('/addInventoryABC', function (Request $request, Response $response, $args) use (
    $inventoryABCDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataInventory = $request->getParsedBody();

    $category = $inventoryABCDao->insertInventoryABC($dataInventory, $id_company);

    if ($category == null)
        $resp = array('success' => true, 'message' => 'Inventario creada correctamente');
    else if (isset($category['info']))
        $resp = array('info' => true, 'message' => $category['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateInventoryABC', function (Request $request, Response $response, $args) use (
    $inventoryABCDao,
    $companiesLicenseDao,
    $productsDao,
    $classificationDao,
    $generalCompositeProductsDao,
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
                // $inventory = $classificationDao->calcInventoryABCBYProduct($products[$i]['id_product'], $license['months']);

                // $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);

                $composite = $generalCompositeProductsDao->findCompositeProductByChild($products[$i]['id_product']);
                $classification = '';

                if (sizeof($composite) > 0) {
                    // $inventory = $generalProductsDao->findProductById($composite[0]['id_product']);
                    $inventory = $classificationDao->calcInventoryABCBYProduct($composite[0]['id_product'], $license['months']);
                    $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);
                    $classification = $inventory['classification'];
                } else {
                    $inventory = $classificationDao->calcInventoryABCBYProduct($products[$i]['id_product'], $license['months']);
                    $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);
                    $classification = $inventory['classification'];
                }

                $resolution = $classificationDao->updateProductClassification($products[$i]['id_product'], $classification);
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
