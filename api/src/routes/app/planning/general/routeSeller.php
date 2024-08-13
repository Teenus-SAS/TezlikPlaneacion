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
                $dataImportSellers =  array('error' => true, 'message' => "Duplicación encontrada en la fila: $i.<br>- Email: $email");
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
            $resp = array('info' => true, 'message' => 'Ya existe un vendedor con la misma información. Ingrese nuevo vendedor');
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

            if(isset($resolution['info']))break;
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Clientes importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateClient', function (Request $request, Response $response, $args) use (
    $sellersDao,
    $generalSellersDao,
    $FilesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataSeller = $request->getParsedBody();

    if (
        empty($dataSeller['idClient']) || empty($dataSeller['firstname']) || empty($dataSeller['lastname']) ||
        empty($dataSeller['email']) || empty($dataSeller['phone']) || empty($dataSeller['city'])
    )
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $client = $generalSellersDao->find$findSeller($dataSeller, $id_company);
        $status = true;

        foreach ($client as $arr) {
            if ($arr['id_client'] != $dataSeller['idClient']) {
                $status = false;
                break;
            }
        }

        if ($status == true) {
            $client = $sellersDao->updateClient($dataSeller);

            if (sizeof($_FILES) > 0) {
                // Insertar imagen
                $FilesDao->imageClient($dataSeller['idClient'], $id_company);
            }

            if ($client == null)
                $resp = array('success' => true, 'message' => 'Cliente actualizado correctamente');
            else if (isset($client['info']))
                $resp = array('info' => true, 'message' => $client['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Ya existe un cliente con el mismo nit. Ingrese nuevo nit');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteClient/{id_client}', function (Request $request, Response $response, $args) use ($sellersDao) {
    $client = $sellersDao->deleteClient($args['id_client']);

    if ($client == null)
        $resp = array('success' => true, 'message' => 'Cliente eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el cliente, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
