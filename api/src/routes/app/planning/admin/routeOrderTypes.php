<?php

use TezlikPlaneacion\dao\GeneralOrderTypesDao;
use TezlikPlaneacion\dao\OrderTypesDao;

$orderTypesDao = new OrderTypesDao();
$generalOrderTypesDao = new GeneralOrderTypesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/orderTypes', function (Request $request, Response $response, $args) use ($orderTypesDao) {
    $orderTypes = $orderTypesDao->findAllOrderTypes();
    $response->getBody()->write(json_encode($orderTypes, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/orderTypesDataValidation', function (Request $request, Response $response, $args) use ($generalOrderTypesDao) {
    $dataOrderTypes = $request->getParsedBody();

    if (isset($dataOrderTypes)) {
        $insert = 0;
        $update = 0;

        $orderTypes = $dataOrderTypes['importOrderTypes'];

        for ($i = 0; $i < sizeof($orderTypes); $i++) {
            if (empty($orderTypes[$i]['orderType'])) {
                $i = $i + 1;
                $dataImportOrderTypes = array('error' => true, 'message' => "Campos vacios en fila: {$i}");
                break;
            } else {
                $findOrderType = $generalOrderTypesDao->findOrderType($orderTypes[$i]);
                if (!$findOrderType) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportOrderTypes['insert'] = $insert;
                $dataImportOrderTypes['update'] = $update;
            }
        }
    } else
        $dataImportOrderTypes = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');
    $response->getBody()->write(json_encode($dataImportOrderTypes, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addOrderTypes', function (Request $request, Response $response, $args) use (
    $orderTypesDao,
    $generalOrderTypesDao
) {
    $dataOrderTypes = $request->getParsedBody();

    if (empty($dataOrderTypes['importOrderTypes'])) {
        $orderTypes = $orderTypesDao->insertOrderTypes($dataOrderTypes);

        if ($orderTypes == null)
            $resp = array('success' => true, 'message' => 'Tipo de pedido ingresado correctamente');
        else if (isset($orderTypes['info']))
            $resp = array('info' => true, 'message' => $orderTypes['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $orderTypes = $dataOrderTypes['importOrderTypes'];

        for ($i = 0; $i < sizeof($orderTypes); $i++) {
            $findOrderType = $generalOrderTypesDao->findOrderType($orderTypes[$i]);
            if (!$findOrderType)
                $resolution = $orderTypesDao->insertOrderTypes($orderTypes[$i]);
            else {
                $orderTypes[$i]['idOrderType'] = $findOrderType['id_order_type'];
                $resolution = $orderTypesDao->updateOrderTypes($orderTypes[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Tipos de pedido importados correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateOrderType', function (Request $request, Response $response, $args) use ($orderTypesDao) {
    $dataOrderTypes = $request->getParsedBody();

    if (empty($dataOrderTypes['idOrderType']) || empty($dataOrderTypes['orderType']))
        $resp = array('error' => true, 'message' => 'No hubo cambio alguno');
    else {
        $orderTypes = $orderTypesDao->updateOrderTypes($dataOrderTypes);

        if ($orderTypes == null)
            $resp = array('success' => true, 'message' => 'Tipo de pedido actualizado correctamente');
        else if (isset($orderTypes['info']))
            $resp = array('info' => true, 'message' => $orderTypes['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteOrderType/{id_order_type}', function (Request $request, Response $response, $args) use ($orderTypesDao) {
    $orderTypes = $orderTypesDao->deleteOrderTypes($args['id_order_type']);

    if ($orderTypes == null)
        $resp = array('success' => true, 'message' => 'Tipo de pedido eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
