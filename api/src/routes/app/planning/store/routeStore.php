<?php

use TezlikPlaneacion\dao\AutenticationUserDao;
use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\StoreDao;
use TezlikPlaneacion\dao\UsersStoreDao;

$storeDao = new StoreDao();
$autenticationDao = new AutenticationUserDao();
$programmingDao = new GeneralProgrammingDao();
$generalOrdersDao = new GeneralOrdersDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$generalProductsDao = new GeneralProductsDao();
$usersStoreDao = new UsersStoreDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/store', function (Request $request, Response $response, $args) use (
    $storeDao,
    $programmingDao,
    $productsMaterialsDao,
    $generalMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $store = $storeDao->findAllStore($id_company);

    $programming = $programmingDao->findAllProgrammingByCompany($id_company);

    for ($i = 0; $i < sizeof($programming); $i++) {
        $materials = $productsMaterialsDao->findAllProductsMaterials($programming[$i]['id_product'], $id_company);

        $status = true;

        for ($j = 0; $j < sizeof($materials); $j++) {
            if ($materials[$j]['status'] == 0) {
                $status = false;
                break;
            }
        }

        for ($j = 0; $j < sizeof($materials); $j++) {
            if ($status == true) {
                $data = [];
                $data['idMaterial'] = $materials[$j]['id_material'];
                $storeDao->saveDelivery($data, 0);
                $generalMaterialsDao->updateDeliveryDateMaterial($data['idMaterial'], NULL);
            } else break;
        }
    }

    $response->getBody()->write(json_encode($store, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/allStore', function (Request $request, Response $response, $args) use ($storeDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // $store = $usersStoreDao->findAllStoreByCompany($id_company);
    $store = $storeDao->findAllStore($id_company);

    $response->getBody()->write(json_encode($store));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/deliverStore', function (Request $request, Response $response, $args) use (
    $storeDao,
    $autenticationDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $generalPlanCiclesMachinesDao,
    $generalProductsDao,
    $generalMaterialsDao,
    $usersStoreDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_rol = $_SESSION['rol'];
    $id_user = $_SESSION['idUser'];
    $dataStore = $request->getParsedBody();

    if ($id_rol != 2) {
        $resp = array('error' => true, 'message' => 'Usuario no autorizado, valide nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    $store = $storeDao->saveDelivery($dataStore, 1);
    if ($store == null) {
        $store = $generalMaterialsDao->updateQuantityMaterial($dataStore['idMaterial'], $dataStore['stored']);

        if ($dataStore['pending'] == 0) {
            date_default_timezone_set('America/Bogota');

            $date = date('Y-m-d H:i:s');

            $store = $generalMaterialsDao->updateDeliveryDateMaterial($dataStore['idMaterial'], $date);
        }
    }

    if ($store == null) {
        $product = $generalProductsDao->findProduct($dataStore, $id_company);

        if ($product) {
            $store = $generalProductsDao->updateAccumulatedQuantity($product['id_product'], $dataStore['stored'], 2);
        }
    }

    if ($store == null) {
        $store = $usersStoreDao->saveUserDeliveredMaterial(
            $id_company,
            $id_user,
            $dataStore
        );
    }

    if ($store == null)
        $store = $generalMaterialsDao->updateReservedMaterial($dataStore['idMaterial'], $dataStore['pending']);

    if ($store == null) {
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
                    $store = $generalOrdersDao->changeStatus($arr['id_order'], 12);
                }
            }
        }
    }

    if ($store == null)
        $resp = array('success' => true, 'message' => 'Materia prima entregada correctamente');
    else if (isset($store['info']))
        $resp = array('info' => true, 'message' => $store['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/usersStore/{id_programming}/{id_material}', function (Request $request, Response $response, $args) use ($usersStoreDao) {
    $users = $usersStoreDao->findAllUserStoreById($args['id_programming'], $args['id_material']);
    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/saveDLVS', function (Request $request, Response $response, $args) use ($usersStoreDao) {
    $dataStore = $request->getParsedBody();

    $users = $dataStore['data'];

    for ($i = 0; $i < sizeof($users); $i++) {
        $store = $usersStoreDao->updateUserDeliveredMaterial($users[$i]);
    }

    if ($store == null)
        $resp = array('success' => true, 'message' => 'Materia prima modificada correctamente');
    else if (isset($store['info']))
        $resp = array('info' => true, 'message' => $store['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
