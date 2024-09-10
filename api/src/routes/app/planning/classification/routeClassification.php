<?php

use TezlikPlaneacion\dao\ClassificationDao;
use TezlikPlaneacion\dao\CompaniesLicenseStatusDao;
use TezlikPlaneacion\Dao\GeneralCompositeProductsDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\ProductsDao;

$classificationDao = new ClassificationDao();
$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();
$companiesLicenseDao = new CompaniesLicenseStatusDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/classification/{months}', function (Request $request, Response $response, $args) use (
    $classificationDao,
    $productsDao,
    $generalProductsDao,
    $companiesLicenseDao,
    $generalCompositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $products = $productsDao->findAllProductsByCompany($id_company);

    $resolution = $generalProductsDao->updateGeneralClassification($id_company);

    for ($i = 0; $i < sizeof($products); $i++) {
        if (isset($resolution)) break;
        $composite = $generalCompositeProductsDao->findCompositeProductByChild($products[$i]['id_product']);

        if (sizeof($composite) > 0) {
            $inventory = $generalProductsDao->findProductById($composite[0]['id_product']);
        } else {
            $inventory = $classificationDao->calcInventoryABCBYProduct($products[$i]['id_product'], $args['months']);
            $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);
        }

        $resolution = $classificationDao->updateProductClassification($products[$i]['id_product'], $inventory['classification']);
    }

    if ($resolution == null)
        $resolution = $companiesLicenseDao->monthLicense($args['months'], $id_company);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Se calculó la clasificación correctamente');
    else if (isset($resolution))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras calculaba. Intente nuevamente');

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
