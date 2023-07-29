<?php

use TezlikPlaneacion\dao\GeneralOfficesDao;
use TezlikPlaneacion\dao\OfficesDao;

$officesDao = new OfficesDao();
$generalOfficesDao = new GeneralOfficesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/offices', function (Request $request, Response $response, $args) use ($officesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $offices = $officesDao->findAllOfficesByCompany($id_company);
    $response->getBody()->write(json_encode($offices, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/offices/{min_date}/{max_date}', function (Request $request, Response $response, $args) use (
    $generalOfficesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $orders = $generalOfficesDao->findAllFilterOrders($args['min_date'], $args['max_date'], $id_company);

    $response->getBody()->write(json_encode($orders, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/actualOffices', function (Request $request, Response $response, $args) use ($generalOfficesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $offices = $generalOfficesDao->findAllActualsOfficesByCompany($id_company);
    $response->getBody()->write(json_encode($offices, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/changeOffices', function (Request $request, Response $response, $args) use (
    $officesDao,
) {
    $dataOrder = $request->getParsedBody();

    $order = $officesDao->updateDeliveryDate($dataOrder);

    if ($order == null)
        $resp = array('success' => true, 'message' => 'Pedido modificado correctamente');
    else if ($order['info'])
        $resp = array('info' => true, 'message' => $order['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
