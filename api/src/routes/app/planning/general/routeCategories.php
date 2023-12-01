<?php

use TezlikPlaneacion\dao\GeneralCategoriesDao;
use TezlikPlaneacion\dao\invCategoriesDao;

$invCategoriesDao = new invCategoriesDao();
$generalCategoriesDao = new GeneralCategoriesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/categories', function (Request $request, Response $response, $args) use ($invCategoriesDao) {
    $categories = $invCategoriesDao->findAllCategories();
    $response->getBody()->write(json_encode($categories, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/categoriesDataValidation', function (Request $request, Response $response, $args) use ($generalCategoriesDao) {
    $dataCategories = $request->getParsedBody();

    if (isset($dataCategories)) {

        $insert = 0;
        $update = 0;

        $categories = $dataCategories['importCategories'];

        for ($i = 0; $i < sizeof($categories); $i++) {

            if (empty($categories[$i]['category']) || empty($categories[$i]['typeCategory'])) {
                $i = $i + 2;
                $dataimportCategories = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            } else {
                $findCategory = $generalCategoriesDao->findCategory($categories[$i]);
                if (!$findCategory) $insert = $insert + 1;
                else $update = $update + 1;
                $dataimportCategories['insert'] = $insert;
                $dataimportCategories['update'] = $update;
            }
        }
    } else
        $dataimportCategories = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataimportCategories, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addCategory', function (Request $request, Response $response, $args) use (
    $invCategoriesDao,
    $generalCategoriesDao
) {
    $dataCategories = $request->getParsedBody();

    if (empty($dataCategories['importCategories'])) {
        $category = $invCategoriesDao->insertCategory($dataCategories);

        if ($category == null)
            $resp = array('success' => true, 'message' => 'Categoria creada correctamente');
        else if (isset($category['info']))
            $resp = array('info' => true, 'message' => $category['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $categories = $dataCategories['importCategories'];

        for ($i = 0; $i < sizeof($categories); $i++) {
            $findCategory = $generalCategoriesDao->findCategory($categories[$i]);
            if (!$findCategory)
                $resolution = $invCategoriesDao->insertCategory($categories[$i]);
            else {
                $categories[$i]['idCategory'] = $findCategory['id_category'];
                $resolution = $invCategoriesDao->updateCategory($categories[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Categoria importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateCategory', function (Request $request, Response $response, $args) use ($invCategoriesDao) {
    $dataCategories = $request->getParsedBody();

    if (empty($dataCategories['category']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    else {
        $category = $invCategoriesDao->updateCategory($dataCategories);

        if ($category == null)
            $resp = array('success' => true, 'message' => 'Categoria actualizada correctamente');
        else if (isset($category['info']))
            $resp = array('info' => true, 'message' => $category['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteCategory/{id_category}', function (Request $request, Response $response, $args) use ($invCategoriesDao) {
    $category = $invCategoriesDao->deleteCategory($args['id_category']);

    if ($category == null)
        $resp = array('success' => true, 'message' => 'Categoria eliminada correctamente');
    else if (isset($category['info']))
        $resp = array('info' => true, 'message' => $category['message']);
    else if ($category != null)
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
