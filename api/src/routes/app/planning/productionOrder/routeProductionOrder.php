<?php

use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\MaterialsComponentsUsersDao;
use TezlikPlaneacion\dao\ProductionOrderDao;
use TezlikPlaneacion\dao\ProductionOrderMPDao;
use TezlikPlaneacion\dao\ProductionOrderPartialDao;
use TezlikPlaneacion\dao\ProgrammingDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\UsersProductionOrderMPDao;
use TezlikPlaneacion\dao\UsersProductionOrderPartialDao;

$generalProgrammingDao = new GeneralProgrammingDao();
$productionOrderDao = new ProductionOrderDao();
$productionOrderPartialDao = new ProductionOrderPartialDao();
$programmingDao = new ProgrammingDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$lastDataDao = new LastDataDao();
$materialsComponentsUsersDao = new MaterialsComponentsUsersDao();
$productionOrderMPDao = new ProductionOrderMPDao();
$usersProductionOrderPartialDao = new UsersProductionOrderPartialDao();
$usersProductionOrderMPDao = new UsersProductionOrderMPDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProductsDao = new GeneralProductsDao();
$generalMaterialsDao = new GeneralMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productionOrder', function (Request $request, Response $response, $args) use (
    $productionOrderDao,
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];
    $programming = $productionOrderDao->findAllProductionOrder($id_user, $id_company);
    $response->getBody()->write(json_encode($programming));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/productionOrder/{id_order}/{id_product}', function (Request $request, Response $response, $args) use (
    $productionOrderDao,
) {
    $programming = $productionOrderDao->findAllProductionOrderByTypePG($args['id_order'], $args['id_product']);
    $response->getBody()->write(json_encode($programming));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/changeStatusOP', function (Request $request, Response $response, $args) use (
    $generalOrdersDao,
    $generalProductsDao,
    $generalMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataOP = $request->getParsedBody();

    $result = $generalOrdersDao->changeStatus($dataOP['idOrder'], 8);

    if ($result == null) {
        $result = $generalProductsDao->updateAccumulatedQuantity($dataOP['idProduct'], $dataOP['quantity'], 2);

        if ($result == null && $dataOP['origin'] == 1) {
            $product = $generalProductsDao->findProductById($dataOP['idProduct']);

            if ($product) {
                $data = [];
                $data['refRawMaterial'] = $product['reference'];
                $data['nameRawMaterial'] = $product['product'];

                $material = $generalMaterialsDao->findMaterial($data, $id_company);

                if ($material)
                    $result = $generalMaterialsDao->updateQuantityMaterial($material['id_material'], $dataOP['quantity']);
            }
        }

        // Cambiar estado pedidos
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            // Checkear cantidades
            // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);
            if (
                $orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FINALIZADO' && $orders[$i]['status'] != 'FABRICADO' &&
                $orders[$i]['status'] != 'DESPACHO' && $orders[$i]['status'] != 'SIN MATERIA PRIMA' && $orders[$i]['status'] != 'SIN FICHA TECNICA'
            ) {
                if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
                    $generalOrdersDao->changeStatus($orders[$i]['id_order'], 2);
                    $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
                } else {
                    $accumulated_quantity = $orders[$i]['accumulated_quantity'];
                }

                if ($orders[$i]['status'] != 'DESPACHO') {
                    $date = Date('Y-m-d');

                    $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
                }

                $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
                !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
            }

            // Pedidos automaticos
            if ($orders[$i]['status'] == 'FABRICADO') {
                $chOrders = $generalOrdersDao->findAllChildrenOrders($orders[$i]['num_order']);

                foreach ($chOrders as $arr) {
                    $result = $generalOrdersDao->changeStatus($arr['id_order'], 12);
                }
            }
        }
    }

    // if ($result == null){
    //     $orders = $generalOrdersDao-
    // }
    if ($result == null)
        $resp = array('success' => true, 'message' => 'Programa de producción eliminado correctamente');
    else if (isset($result['info']))
        $resp = array('info' => true, 'message' => $result['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras eliminaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/changeFlagCancelOP/{id_programming}/{flag}', function (Request $request, Response $response, $args) use ($generalProgrammingDao) {
    $resolution = $generalProgrammingDao->changeFlagProgramming($args['id_programming'], $args['flag']);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Orden de produccion modificada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras modificaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/changeFlagOP', function (Request $request, Response $response, $args) use (
    $productionOrderDao,
    $programmingDao,
    $generalProgrammingDao,
    $programmingRoutesDao,
    $lastDataDao,
    $generalProgrammingRoutesDao,
    $generalPlanCiclesMachinesDao,
    $generalOrdersDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $type_program = $_SESSION['type_program'];
    $dataOP = $request->getParsedBody();
    $id_programming = $dataOP['id_programming'];

    $resolution = $productionOrderDao->changeflagOPById($dataOP['id_programming'], 1);

    if ($resolution == null && $type_program == 1) {
        $resolution = $productionOrderDao->closeOPMachine($dataOP['id_programming'], 1);
        $machine = $generalPlanCiclesMachinesDao->findNextRouteByPG($dataOP['id_product'], $dataOP['route']);

        if ($machine) {
            $dataOP['id_machine'] = $machine['id_machine'];
            $dataOP['route'] = $machine['route'];
            $resolution = $programmingDao->insertProgrammingByCompany($dataOP, $id_company);

            if ($resolution == null) {
                $arr = $generalProgrammingRoutesDao->findProgrammingRoutes($dataOP['id_product'], $dataOP['id_order']);

                if ($arr) {
                    $dataOP['idProgrammingRoutes'] = $arr['id_programming_routes'];
                    $resolution = $programmingRoutesDao->updateProgrammingRoutes($dataOP);
                }
            }

            if ($resolution == null) {
                $lastData = $lastDataDao->findLastInsertedProgramming($id_company);

                $dataOP['id_programming'] = $lastData['id_programming'];
                $id_programming = $lastData['id_programming'];
                $resolution = $generalProgrammingDao->changeStatusProgramming($dataOP);
            }
        } else {
            $resolution = $generalOrdersDao->changeStatus($dataOP['id_order'], 12);
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Orden de produccion modificada correctamente', 'id_programming' => $id_programming);
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras modificaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/materialsComponents', function (Request $request, Response $response, $args) use (
    $materialsComponentsUsersDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $materials = $materialsComponentsUsersDao->findAllMaterialsComponentsByCompany($id_company);

    $response->getBody()->write(json_encode($materials));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/acceptMaterialReceive', function (Request $request, Response $response, $args) use (
    $materialsComponentsUsersDao,
    $generalRequisitionsProductsDao,
    $generalMaterialsDao,
    $generalProductsDao,
    $transitMaterialsDao,
    $usersRequisitonsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];

    $dataOP = $request->getParsedBody();

    $resolution = null;

    $resolution = $materialsComponentsUsersDao->insertMaterialComponentUser($dataOP, $id_user, $id_company);

    // if ($resolution == null){
    //     $resolution = $generalMaterialsDao->
    // }


    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Material aceptado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/materialsComponents/{id_programming}/{id_material}', function (Request $request, Response $response, $args) use (
    $materialsComponentsUsersDao
) {
    $materials = $materialsComponentsUsersDao->findAllMaterialsComponentsById($args['id_programming'], $args['id_material']);

    $response->getBody()->write(json_encode($materials));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

// $app->get('/productionOrderPartial', function (Request $request, Response $response, $args) use (
//     $productionOrderPartialDao
// ) {
//     session_start();
//     $id_company = $_SESSION['id_company'];
//     $productionOrder = $productionOrderPartialDao->findAllOPPartialBycompany($id_company);

//     $response->getBody()->write(json_encode($productionOrder));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->get('/productionOrderPartial/{id_programming}', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
//     session_start();
//     $id_company = $_SESSION['id_company'];

//     $productionOrder = $productionOrderPartialDao->findAllOPPartialById($args['id_programming'], $id_company);

//     $response->getBody()->write(json_encode($productionOrder));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/addOPPartial', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
//     session_start();
//     $id_company = $_SESSION['id_company'];
//     $id_user = $_SESSION['idUser'];

//     $dataOP = $request->getParsedBody();
//     $dataOP['operator'] = $id_user;

//     $resolution = $productionOrderPartialDao->insertOPPartialByCompany($dataOP, $id_company);

//     if ($resolution == null)
//         $resp = ['success' => true, 'message' => 'Orden de producción entregada correctamente'];
//     else if (isset($resolution['info']))
//         $resp = ['info' => true, 'message' => $resolution['message']];
//     else
//         $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/updateOPPartial', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
//     $dataOP = $request->getParsedBody();

//     $resolution = $productionOrderPartialDao->updateOPPartial($dataOP);

//     if ($resolution == null)
//         $resp = ['success' => true, 'message' => 'Orden de producción modificada correctamente'];
//     else if (isset($resolution['info']))
//         $resp = ['info' => true, 'message' => $resolution['message']];
//     else
//         $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->get('/deleteOPPartial/{id_part_deliv}', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
//     $resolution = $productionOrderPartialDao->deleteOPPartial($args['id_part_deliv']);

//     if ($resolution == null)
//         $resp = ['success' => true, 'message' => 'Orden de producción eliminada correctamente'];
//     else if (isset($resolution['info']))
//         $resp = ['info' => true, 'message' => $resolution['message']];
//     else
//         $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/saveReceiveOPPTDate', function (Request $request, Response $response, $args) use (
//     $productionOrderPartialDao,
//     $usersProductionOrderPartialDao,
//     $generalProductsDao,
// ) {
//     session_start();
//     $id_company = $_SESSION['id_company'];
//     $id_user = $_SESSION['idUser'];

//     $dataOP = $request->getParsedBody();

//     $resolution = $productionOrderPartialDao->updateDateReceive($dataOP);

//     if ($resolution == null) {
//         $resolution = $generalProductsDao->updateAccumulatedQuantity($dataOP['idProduct'], $dataOP['quantity'], 2);
//     }

//     if ($resolution == null) {
//         $resolution = $usersProductionOrderPartialDao->saveUserOPPartial($id_company, $dataOP['idPartDeliv'], $id_user);
//     }

//     if ($resolution == null)
//         $resp = array('success' => true, 'message' => 'Fecha guardada correctamente');
//     else if (isset($resolution['info']))
//         $resp = array('info' => true, 'message' => $resolution['message']);
//     else
//         $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });

// $app->get('/usersOPPT/{id_part_deliv}', function (Request $request, Response $response, $args) use ($usersProductionOrderPartialDao) {
//     $users = $usersProductionOrderPartialDao->findAllUserOPPartialById($args['id_part_deliv']);
//     $response->getBody()->write(json_encode($users));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// // Material
// $app->get('/productionOrderMaterial', function (Request $request, Response $response, $args) use ($productionOrderMPDao) {
//     session_start();
//     $id_company = $_SESSION['id_company'];
//     $materials = $productionOrderMPDao->findAllOPMaterialByCompany($id_company);

//     $response->getBody()->write(json_encode($materials));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->get('/productionOrderMaterial/{id_programming}', function (Request $request, Response $response, $args) use ($productionOrderMPDao) {
//     session_start();
//     $id_company = $_SESSION['id_company'];

//     $productionOrder = $productionOrderMPDao->findAllOPMaterialById($args['id_programming'], $id_company);

//     $response->getBody()->write(json_encode($productionOrder));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/addOPMaterial', function (Request $request, Response $response, $args) use ($productionOrderMPDao) {
//     session_start();
//     $id_company = $_SESSION['id_company'];
//     $id_user = $_SESSION['idUser'];

//     $dataOP = $request->getParsedBody();
//     $dataOP['operator'] = $id_user;

//     $resolution = $productionOrderMPDao->insertOPMaterialByCompany($dataOP, $id_company);

//     if ($resolution == null)
//         $resp = ['success' => true, 'message' => 'Materia prima entregada correctamente'];
//     else if (isset($resolution['info']))
//         $resp = ['info' => true, 'message' => $resolution['message']];
//     else
//         $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/updateOPMaterial', function (Request $request, Response $response, $args) use ($productionOrderMPDao) {
//     $dataOP = $request->getParsedBody();

//     $resolution = $productionOrderMPDao->updateOPMaterial($dataOP);

//     if ($resolution == null)
//         $resp = ['success' => true, 'message' => 'Materia prima modificada correctamente'];
//     else if (isset($resolution['info']))
//         $resp = ['info' => true, 'message' => $resolution['message']];
//     else
//         $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->get('/deleteOPMaterial/{id_prod_order_material}', function (Request $request, Response $response, $args) use (
//     $productionOrderMPDao
// ) {
//     $resolution = $productionOrderMPDao->deleteOPMaterial($args['id_prod_order_material']);

//     if ($resolution == null)
//         $resp = ['success' => true, 'message' => 'Orden de producción eliminada correctamente'];
//     else if (isset($resolution['info']))
//         $resp = ['info' => true, 'message' => $resolution['message']];
//     else
//         $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/saveReceiveOPMPDate', function (Request $request, Response $response, $args) use (
//     $productionOrderMPDao,
//     $usersProductionOrderMPDao,
//     $generalMaterialsDao,
// ) {
//     session_start();
//     $id_company = $_SESSION['id_company'];
//     $id_user = $_SESSION['idUser'];

//     $dataOP = $request->getParsedBody();

//     $resolution = $productionOrderMPDao->updateDateReceive($dataOP);

//     if ($resolution == null) {
//         $resolution = $generalMaterialsDao->updateQuantityMaterial($dataOP['idMaterial'], $dataOP['quantity']);
//     }

//     if ($resolution == null) {
//         $resolution = $usersProductionOrderMPDao->saveUserOPMP($id_company, $dataOP['idOPM'], $id_user);
//     }

//     if ($resolution == null)
//         $resp = array('success' => true, 'message' => 'Fecha guardada correctamente');
//     else if (isset($resolution['info']))
//         $resp = array('info' => true, 'message' => $resolution['message']);
//     else
//         $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });

// $app->get('/usersOPMP/{id_prod_order_material_user}', function (Request $request, Response $response, $args) use (
//     $usersProductionOrderMPDao
// ) {
//     $users = $usersProductionOrderMPDao->findAllUserOPMPById($args['id_prod_order_material_user']);
//     $response->getBody()->write(json_encode($users));
//     return $response->withHeader('Content-Type', 'application/json');
// });
