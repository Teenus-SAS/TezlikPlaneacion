<?php

use TezlikPlaneacion\dao\ProductsTypeDao;

$productsTypeDao = new ProductsTypeDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productsType', function (Request $request, Response $response, $args) use (
    $productsTypeDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsTypeDao->findAllProductsTypeByCompany($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar Tipo de productos importadas */
$app->post('/productsTypeDataValidation', function (Request $request, Response $response, $args) use (
    $productsTypeDao
) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {

            if (empty($products[$i]['productType'])) {
                $i = $i + 2;
                $dataImportProducts = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            } else {
                $findProduct = $productsTypeDao->findProductsType($products[$i], $id_company);
                if (!$findProduct) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportProducts['insert'] = $insert;
                $dataImportProducts['update'] = $update;
            }
        }
    } else
        $dataImportProducts = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProducts, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Agregar Tipo de productos */
$app->post('/addProductsTypes', function (Request $request, Response $response, $args) use (
    $productsTypeDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProduct = $request->getParsedBody();

    if (empty($dataProduct['importproducts'])) {
        $findProduct = $productsTypeDao->findProductsType($dataProduct, $id_company);

        if (!$findProduct) {
            $resolution = $productsTypeDao->insertProductsTypeByCompany($dataProduct, $id_company);

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Tipo de producto creado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Tipo de producto ya existe. Ingrese uno nuevo');
    } else {
        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {
            $findProduct = $productsTypeDao->findProductsType($products[$i], $id_company);

            if (!$findProduct) {
                $resolution = $productsTypeDao->insertProductsTypeByCompany($products[$i], $id_company);
                if (isset($resolution['info'])) break;
            } else {
                $products[$i]['idProductType'] = $findProduct['id_product_type'];
                $resolution = $productsTypeDao->updateProductType($products[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Tipo de producto Importado correctamente');
        else if ($resolution['info'] == 'true')
            $resp = $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Actualizar Tipo de producto */
$app->post('/updateProductsTypes', function (Request $request, Response $response, $args) use (
    $productsTypeDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataProduct = $request->getParsedBody();

    $product = $productsTypeDao->findProductsType($dataProduct, $id_company);
    !is_array($product) ? $data['id_product_type'] = 0 : $data = $product;

    if ($data['id_product_type'] == $dataProduct['idProductType'] || $data['id_product_type'] == 0) {
        $resolution = $productsTypeDao->updateProductType($dataProduct);

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Tipo de producto actualizada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Tipo de producto ya existe. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Eliminar Tipo de producto */
$app->get('/deleteProductsType/{id_product_type}', function (Request $request, Response $response, $args) use ($productsTypeDao) {
    $resolution = $productsTypeDao->deleteProductType($args['id_product_type']);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Tipo de producto eliminado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el tipo de producto, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
