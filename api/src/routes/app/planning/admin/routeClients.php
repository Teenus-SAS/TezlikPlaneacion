<?php

use TezlikPlaneacion\dao\ClientsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;

$clientsDao = new ClientsDao();
$generalClientsDao = new GeneralClientsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/clients', function (Request $request, Response $response, $args) use ($clientsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $clients = $clientsDao->findAllClientByCompany($id_company);
    $response->getBody()->write(json_encode($clients, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/clientsDataValidation', function (Request $request, Response $response, $args) use ($generalClientsDao) {
    $dataClient = $request->getParsedBody();

    if (isset($dataClient)) {
        session_start();
        $id_company = $_SESSION['id_company'];
        $insert = 0;
        $update = 0;

        $clients = $dataClient['importClients'];

        for ($i = 0; $i < sizeof($clients); $i++) {
            if (empty($clients[$i]['ean']) || empty($clients[$i]['nit']) || empty($clients[$i]['client'])) {
                $i = $i + 1;
                $dataImportClients = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            } else {
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
    $generalClientsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataClient = $request->getParsedBody();

    $countClients = sizeof($dataClient);

    if ($countClients > 1) {
        $client = $clientsDao->insertClient($dataClient, $id_company);

        if ($client == null)
            $resp = array('success' => true, 'message' => 'Cliente ingresado correctamente');
        else if (isset($client['info']))
            $resp = array('info' => true, 'message' => $client['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $clients = $dataClient['importClients'];

        for ($i = 0; $i < sizeof($clients); $i++) {
            $findClient = $generalClientsDao->findClient($clients[$i], $id_company);

            if (!$findClient) $resolution = $clientsDao->insertClient($clients[$i], $id_company);
            else {
                $clients[$i]['idClient'] = $findClient['id_client'];
                $resolution = $clientsDao->updateClient($clients[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Clientes importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateClient', function (Request $request, Response $response, $args) use ($clientsDao) {
    $dataClient = $request->getParsedBody();

    if (empty($dataClient['idClient']) || empty($dataClient['ean']) || empty($dataClient['nit']) || empty($dataClient['client']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $client = $clientsDao->updateClient($dataClient);

        if ($client == null)
            $resp = array('success' => true, 'message' => 'Cliente actualizado correctamente');
        else if (isset($client['info']))
            $resp = array('info' => true, 'message' => $client['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
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
