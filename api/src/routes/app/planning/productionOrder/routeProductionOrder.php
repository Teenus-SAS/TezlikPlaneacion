<?php

use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;

$generalProgrammingDao = new GeneralProgrammingDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProductsDao = new GeneralProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/productionOrder', function (Request $request, Response $response, $args) use (
    $generalProgrammingDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $programming = $generalProgrammingDao->findAllProductionOrder($id_company);
    $response->getBody()->write(json_encode($programming, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/changeStatusOP', function (Request $request, Response $response, $args) use (
    $generalOrdersDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOP = $request->getParsedBody();

    $result = $generalOrdersDao->changeStatus($dataOP['idOrder'], 'Fabricado');
    $generalProductsDao->updateAccumulatedQuantity($dataOP['idProduct'], $dataOP['quantity'], 2);

    // Cambiar estado pedidos
    $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

    for ($i = 0; $i < sizeof($orders); $i++) {
        // Checkear cantidades
        $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);
        if (
            $order['status'] != 'En Produccion' && $order['status'] != 'Entregado' && $order['status'] != 'Fabricado' &&
            $order['status'] != 'Sin Ficha Tecnica' && $order['status'] != 'Sin Materia Prima'
        ) {
            if ($order['original_quantity'] <= $order['accumulated_quantity']) {
                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Despacho');
                $accumulated_quantity = $order['accumulated_quantity'] - $order['original_quantity'];
            } else {
                $accumulated_quantity = $order['accumulated_quantity'];
            }

            if ($order['status'] != 'Despacho') {
                $date = Date('Y-m-d');

                $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
            }

            $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
            !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
            $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

            $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
        }
    }

    if ($result == null)
        $resp = array('success' => true, 'message' => 'Programa de producción eliminado correctamente');
    else if (isset($result['info']))
        $resp = array('info' => true, 'message' => $result['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
