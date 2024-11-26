<?php

use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\ProductionOrderDao;
use TezlikPlaneacion\dao\ProductionOrderMPDao;
use TezlikPlaneacion\dao\ProductionOrderPartialDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\UsersProductionOrderMPDao;
use TezlikPlaneacion\dao\UsersProductionOrderPartialDao;

$generalProgrammingDao = new GeneralProgrammingDao();
$productionOrderDao = new ProductionOrderDao();
$productionOrderPartialDao = new ProductionOrderPartialDao();
$productionOrderMPDao = new ProductionOrderMPDao();
$usersProductionOrderPartialDao = new UsersProductionOrderPartialDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$usersProductionOrderMPDao = new UsersProductionOrderMPDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProductsDao = new GeneralProductsDao();
$generalMaterialsDao = new GeneralMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productionOrderPartial', function (Request $request, Response $response, $args) use (
    $productionOrderPartialDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $productionOrder = $productionOrderPartialDao->findAllOPPartialBycompany($id_company);

    $response->getBody()->write(json_encode($productionOrder));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/productionOrderPartial/{id_programming}', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];

    $productionOrder = $productionOrderPartialDao->findAllOPPartialById($args['id_programming'], $id_company, $id_user);

    $response->getBody()->write(json_encode($productionOrder));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addOPPartial', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];

    $dataOP = $request->getParsedBody();
    $dataOP['operator'] = $id_user;

    $resolution = $productionOrderPartialDao->insertOPPartialByCompany($dataOP, $id_company);

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'Orden de producción entregada correctamente'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updateOPPartial', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    $dataOP = $request->getParsedBody();

    $resolution = $productionOrderPartialDao->updateOPPartial($dataOP);

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'Orden de producción modificada correctamente'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteOPPartial/{id_part_deliv}', function (Request $request, Response $response, $args) use ($productionOrderPartialDao) {
    $resolution = $productionOrderPartialDao->deleteOPPartial($args['id_part_deliv']);

    if ($resolution == null)
        $resp = ['success' => true, 'message' => 'Orden de producción eliminada correctamente'];
    else if (isset($resolution['info']))
        $resp = ['info' => true, 'message' => $resolution['message']];
    else
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/saveReceiveOPPTDate', function (Request $request, Response $response, $args) use (
    $productionOrderPartialDao,
    $usersProductionOrderPartialDao,
    $generalProductsDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $generalPlanCiclesMachinesDao,
    $generalOrdersDao,
    $generalMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];

    $dataOP = $request->getParsedBody();

    $resolution = $productionOrderPartialDao->updateDateReceive($dataOP);

    if ($resolution == null) {
        $resolution = $generalProductsDao->updateAccumulatedQuantity($dataOP['idProduct'], $dataOP['quantity'], 2);
    }


    if ($resolution == null && $dataOP['origin'] == 1) {
        $product = $generalProductsDao->findProductById($dataOP['idProduct']);
        if ($product) {
            $data = [];
            $data['refRawMaterial'] = $product['reference'];
            $data['nameRawMaterial'] = $product['product'];

            $material = $generalMaterialsDao->findMaterial($data, $id_company);

            if ($material)
                $resolution = $generalMaterialsDao->updateQuantityMaterial($material['id_material'], $dataOP['quantity']);
        }
    }

    if ($resolution == null) {
        $resolution = $usersProductionOrderPartialDao->saveUserOPPartial($id_company, $dataOP['idPartDeliv'], $id_user);
    }

    if ($resolution == null) {
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            if (
                $orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FINALIZADO' &&
                $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO'
            ) {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                    // Ficha tecnica 
                    $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
                    $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
                    $productsFTM = array_merge($productsMaterials, $compositeProducts);

                    $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

                    if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                        $orders[$i]['origin'] == 2 ? $status = 5 : $status = 13;
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], $status);
                        $status = false;
                    } else {
                        foreach ($planCicles as $arr) {
                            // Verificar Maquina Disponible
                            if ($arr['status'] == 0 && $arr['status_alternal_machine'] == 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 10);
                                $status = false;
                                break;
                            }
                            // Verificar Empleados
                            if ($arr['employees'] == 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 11);
                                $status = false;
                                break;
                            }
                        }

                        // Verificar Materia Prima
                        foreach ($productsFTM as $arr) {
                            if ($arr['quantity_material'] <= 0) {
                                $generalOrdersDao->changeStatus($orders[$i]['id_order'], 6);
                                $status = false;
                                break;
                            }
                        }
                    }
                }

                if ($status == true) {
                    if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 2);
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
                    } else {
                        $accumulated_quantity = $orders[$i]['accumulated_quantity'];
                    }

                    if ($orders[$i]['status'] != 'DESPACHO') {
                        $date = date('Y-m-d');

                        $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
                    }

                    $arr = $generalProductsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $generalProductsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $generalProductsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
                }
            }

            // Pedidos automaticos
            if ($orders[$i]['status'] == 'FABRICADO') {
                $chOrders = $generalOrdersDao->findAllChildrenOrders($orders[$i]['num_order']);

                foreach ($chOrders as $arr) {
                    $resolution = $generalOrdersDao->changeStatus($arr['id_order'], 12);
                }
            }
        }
    }


    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Fecha guardada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/usersOPPT/{id_part_deliv}', function (Request $request, Response $response, $args) use ($usersProductionOrderPartialDao) {
    $users = $usersProductionOrderPartialDao->findAllUserOPPartialById($args['id_part_deliv']);
    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});
