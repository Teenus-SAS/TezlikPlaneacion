<?php

use TezlikPlaneacion\dao\FilesDao;
use TezlikPlaneacion\dao\GeneralSellersDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\SellersDao;

$sellersDao = new SellersDao();
$generalSellersDao = new GeneralSellersDao();
$lastDataDao = new LastDataDao();
$FilesDao = new FilesDao();


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/sellers', function (Request $request, Response $response, $args) use ($sellersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $sellers = $sellersDao->findAllSellersByCompany($id_company);
    $response->getBody()->write(json_encode($sellers, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/sellersDataValidation', function (Request $request, Response $response, $args) use ($generalSellersDao) {
    $dataSeller = $request->getParsedBody();

    if (isset($dataSeller)) {
        session_start();
        $id_company = $_SESSION['id_company'];
        $insert = 0;
        $update = 0;

        $sellers = $dataSeller['importSellers'];

        // Verificar duplicados
        $duplicateTracker = [];
        $dataImportSellers = [];

        for ($i = 0; $i < count($sellers); $i++) {
            if (
                empty($sellers[$i]['firstname']) || empty($sellers[$i]['lastname']) || empty($sellers[$i]['email'])
            ) {
                $i = $i + 2;
                $dataImportSellers = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }

            if (
                trim(empty($sellers[$i]['firstname'])) || trim(empty($sellers[$i]['lastname'])) || trim(empty($sellers[$i]['email']))
            ) {
                $i = $i + 2;
                $dataImportSellers = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }

            $item = $sellers[$i];
            $email = trim($item['email']);

            if (isset($duplicateTracker[$email])) {
                $i = $i + 2;
                $dataImportSellers =  array('error' => true, 'message' => "fila-$i: Duplicidad encontrada: Email: $email");
                break;
            } else {
                $duplicateTracker[$email] = true;
            }
        }

        if (sizeof($dataImportSellers) == 0) {
            for ($i = 0; $i < sizeof($sellers); $i++) {
                $findSeller = $generalSellersDao->findSeller($sellers[$i], $id_company);
                if (!$findSeller) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportSellers['insert'] = $insert;
                $dataImportSellers['update'] = $update;
            }
        }
    } else
        $dataImportSellers = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');
    $response->getBody()->write(json_encode($dataImportSellers, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addSeller', function (Request $request, Response $response, $args) use (
    $sellersDao,
    $generalSellersDao,
    $lastDataDao,
    $FilesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataSeller = $request->getParsedBody();

    $countSellers = sizeof($dataSeller);

    if ($countSellers > 1) {
        $findSeller = $generalSellersDao->findSeller($dataSeller, $id_company);

        if (!$findSeller) {
            $resolution = $sellersDao->insertSellerByCompany($dataSeller, $id_company);

            if (sizeof($_FILES) > 0) {
                $lastSeller = $lastDataDao->findLastInsertedSeller();

                // Insertar imagen
                $FilesDao->avatarSeller($lastSeller['id_seller'], $id_company);
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Vendedor ingresado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Ya existe un vendedor con el mismo correo. Ingrese nuevo correo');
    } else {
        $sellers = $dataSeller['importSellers'];

        for ($i = 0; $i < sizeof($sellers); $i++) {
            $findSeller = $generalSellersDao->findSeller($sellers[$i], $id_company);

            if (!$findSeller) {
                $resolution = $sellersDao->insertSellerByCompany($sellers[$i], $id_company);
            } else {
                $sellers[$i]['idSeller'] = $findSeller['id_seller'];
                $resolution = $sellersDao->updateSeller($sellers[$i]);
            }

            if (isset($resolution['info'])) break;
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Vendedores importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateSeller', function (Request $request, Response $response, $args) use (
    $sellersDao,
    $generalSellersDao,
    $FilesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataSeller = $request->getParsedBody();

    $seller = $generalSellersDao->findSeller($dataSeller, $id_company);

    !is_array($seller) ? $data['id_seller'] = 0 : $data = $seller;
    if ($data['id_seller'] == $dataSeller['idSeller'] || $data['id_seller'] == 0) {
        $resolution = $sellersDao->updateSeller($dataSeller);

        if ($resolution == null) {
            if (sizeof($_FILES) > 0) {
                // Insertar imagen
                $FilesDao->avatarSeller($dataSeller['idSeller'], $id_company);
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Vendedor actualizado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'Ya existe un vendedor con el mismo correo. Ingrese nuevo correo');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/changeStatusSeller/{id_seller}', function (Request $request, Response $response, $args) use ($generalSellersDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $resolution = $generalSellersDao->changeStatusSellerByCompany($id_company);
    if ($resolution == null)
        $resolution = $generalSellersDao->changeStatusSeller($args['id_seller'], 1);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Vendedor interno modificado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible modificar el vendedor, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteSeller/{id_seller}', function (Request $request, Response $response, $args) use ($sellersDao) {
    $resolution = $sellersDao->deleteSeller($args['id_seller']);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Vendedor eliminado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el vendedor, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
