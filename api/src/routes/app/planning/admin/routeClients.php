<?php

use TezlikPlaneacion\dao\ClientsDao;
use TezlikPlaneacion\dao\FilesDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\LastDataDao;

$clientsDao = new ClientsDao();
$generalClientsDao = new GeneralClientsDao();
$lastDataDao = new LastDataDao();
$FilesDao = new FilesDao();


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/clients', function (Request $request, Response $response, $args) use ($clientsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $clients = $clientsDao->findAllClientByCompany($id_company);
    $response->getBody()->write(json_encode($clients, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// $app->get('/providers', function (Request $request, Response $response, $args) use ($clientsDao) {
//     session_start();
//     $id_company = $_SESSION['id_company'];
//     $clients = $clientsDao->findAllClientByCompany($id_company);
//     $response->getBody()->write(json_encode($clients, JSON_NUMERIC_CHECK));
//     return $response->withHeader('Content-Type', 'application/json');
// });

$app->post('/clientsDataValidation', function (Request $request, Response $response, $args) use ($generalClientsDao) {
    $dataClient = $request->getParsedBody();

    if (isset($dataClient)) {
        session_start();
        $id_company = $_SESSION['id_company'];
        $insert = 0;
        $update = 0;

        $clients = $dataClient['importClients'];

        // Verificar duplicados
        $duplicateTracker = [];
        $dataImportClients = [];

        for ($i = 0; $i < count($clients); $i++) {
            if (
                empty($clients[$i]['nit']) || empty($clients[$i]['client']) || empty($clients[$i]['address']) ||
                empty($clients[$i]['phone']) || empty($clients[$i]['city']) || empty($clients[$i]['type'])
            ) {
                $i = $i + 2;
                $dataImportClients = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }

            if (
                trim(empty($clients[$i]['nit'])) || trim(empty($clients[$i]['client'])) || trim(empty($clients[$i]['address'])) ||
                trim(empty($clients[$i]['phone'])) || trim(empty($clients[$i]['city'])) || trim(empty($clients[$i]['type']))
            ) {
                $i = $i + 2;
                $dataImportClients = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }

            $item = $clients[$i];
            $nitClient = trim($item['nit']);
            $nameClient = trim($item['client']);

            if (isset($duplicateTracker[$nitClient]) || isset($duplicateTracker[$nameClient])) {
                $i = $i + 2;
                $dataImportClients =  array('error' => true, 'message' => "Duplicación encontrada en la fila: $i.<br>- NIT: $nitClient<br>- Cliente: $nameClient");
                break;
            } else {
                $duplicateTracker[$nitClient] = true;
                $duplicateTracker[$nameClient] = true;
            }

            $findClient = $generalClientsDao->findClientsByNitAndName($clients[$i], $id_company);

            if (sizeof($findClient) > 1) {
                $i = $i + 2;
                $dataImportClients =  array('error' => true, 'message' => "NIT y nombre de cliente ya existente, fila: $i.<br>- NIT: $nitClient<br>- Cliente: $nameClient");
                break;
            }

            if (isset($findClient[0]['nit']) != $nitClient || isset($findClient[0]['client']) != $nameClient) {
                $i = $i + 2;
                $dataImportClients =  array('error' => true, 'message' => "NIT o nombre de cliente ya existente, fila: $i.<br>- NIT: $nitClient<br>- Cliente: $nameClient");
                break;
            }
        }

        if (sizeof($dataImportClients) == 0) {
            for ($i = 0; $i < sizeof($clients); $i++) {
                $findClient = $generalClientsDao->findClient($clients[$i], $id_company);
                if (!$findClient) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportClients['insert'] = $insert;
                $dataImportClients['update'] = $update;
            }
        }
    } else
        $dataImportClients = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');
    $response->getBody()->write(json_encode($dataImportClients, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addClient', function (Request $request, Response $response, $args) use (
    $clientsDao,
    $generalClientsDao,
    $lastDataDao,
    $FilesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataClient = $request->getParsedBody();

    $countClients = sizeof($dataClient);

    if ($countClients > 1) {
        $findClient = $generalClientsDao->findClientsByNitAndName($dataClient, $id_company);

        if (!$findClient) {
            $dataClient['type'] = 1;
            $client = $clientsDao->insertClient($dataClient, $id_company);

            if (sizeof($_FILES) > 0) {
                $lastClient = $lastDataDao->findLastInsertedClient();

                // Insertar imagen
                $FilesDao->imageClient($lastClient['id_client'], $id_company);
            }

            if ($client == null)
                $resp = array('success' => true, 'message' => 'Cliente ingresado correctamente');
            else if (isset($client['info']))
                $resp = array('info' => true, 'message' => $client['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'Ya existe un cliente con el mismo nit. Ingrese nuevo nit');
    } else {
        $clients = $dataClient['importClients'];

        for ($i = 0; $i < sizeof($clients); $i++) {
            $findClient = $generalClientsDao->findClient($clients[$i], $id_company);

            if (!$findClient) {
                $clients[$i]['type'] = 1;
                $resolution = $clientsDao->insertClient($clients[$i], $id_company);

                $lastClient = $lastDataDao->findLastInsertedClient();
                $clients[$i]['idClient'] = $lastClient['id_client'];
            } else {
                $clients[$i]['idClient'] = $findClient['id_client'];
                $resolution = $clientsDao->updateClient($clients[$i]);
            }

            $clients[$i]['type'] == 'CLIENTE' ? $type = 1 : $type = 2;

            $resolution = $generalClientsDao->changeTypeClient($clients[$i]['idClient'], $type);
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
    $clientsDao,
    $generalClientsDao,
    $FilesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataClient = $request->getParsedBody();

    if (
        empty($dataClient['idClient']) || empty($dataClient['nit']) || empty($dataClient['client']) ||
        empty($dataClient['address']) || empty($dataClient['phone']) || empty($dataClient['city'])
    )
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $client = $generalClientsDao->findClient($dataClient, $id_company);
        $status = true;

        foreach ($client as $arr) {
            if ($arr['id_client'] != $dataClient['idClient']) {
                $status = false;
                break;
            }
        }

        if ($status == true) {
            $client = $clientsDao->updateClient($dataClient);

            if (sizeof($_FILES) > 0) {
                // Insertar imagen
                $FilesDao->imageClient($dataClient['idClient'], $id_company);
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

$app->post('/copyClient', function (Request $request, Response $response, $args) use (
    $clientsDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataClient = $request->getParsedBody();

    $client = $clientsDao->insertClient($dataClient, $id_company);

    if ($client == null)
        $resp = array('success' => true, 'message' => 'Cliente clonado correctamente');
    else if (isset($client['info']))
        $resp = array('info' => true, 'message' => $client['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/changeStatus/{id_client}', function (Request $request, Response $response, $args) use ($generalClientsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $client = $generalClientsDao->changeStatusClientByCompany($id_company);
    if ($client == null)
        $client = $generalClientsDao->changeStatusClient($args['id_client'], 1);

    if ($client == null)
        $resp = array('success' => true, 'message' => 'Cliente interno modificado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible modificar el cliente, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/changeTypeClient/{id_client}/{op}', function (Request $request, Response $response, $args) use ($generalClientsDao) {
    $client = $generalClientsDao->changeTypeClient($args['id_client'], $args['op']);

    if ($client == null)
        $resp = array('success' => true, 'message' => 'Cliente modificado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible modificar el cliente, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteClient/{id_client}', function (Request $request, Response $response, $args) use ($clientsDao) {
    $client = $clientsDao->deleteClient($args['id_client']);

    if ($client == null)
        $resp = array('success' => true, 'message' => 'Cliente eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el cliente, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
