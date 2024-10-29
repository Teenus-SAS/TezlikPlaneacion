<?php

use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsMaterialsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsProductsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\RequisitionsMaterialsDao;
use TezlikPlaneacion\dao\RequisitionsproductsDao;
use TezlikPlaneacion\dao\TransitMaterialsDao;
use TezlikPlaneacion\dao\UsersRequisitionsDao;

$requisitionsMaterialsDao = new RequisitionsMaterialsDao();
$requisitionsProductsDao = new RequisitionsproductsDao();
$generalRequisitionsMaterialsDao = new GeneralRequisitionsMaterialsDao();
$generalRequisitionsProductsDao = new GeneralRequisitionsProductsDao();
$usersRequisitonsDao = new UsersRequisitionsDao();
$transitMaterialsDao = new TransitMaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$generalProductsDao = new GeneralProductsDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalClientsDao = new GeneralClientsDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$generalExMaterialsDao = new GeneralExplosionMaterialsDao();
$generalRMStockDao = new GeneralRMStockDao();
$lastDataDao = new LastDataDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/requisitions', function (Request $request, Response $response, $args) use (
    $generalRequisitionsMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $materials = $generalRequisitionsMaterialsDao->findAllActualRequisitionByCompany($id_company);

    $response->getBody()->write(json_encode($materials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/requisitionsMaterials', function (Request $request, Response $response, $args) use ($generalRequisitionsMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $requisitions = $generalRequisitionsMaterialsDao->findAllActualRequisitionByCompany($id_company);
    $response->getBody()->write(json_encode($requisitions, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/requisitions/{min_date}/{max_date}', function (Request $request, Response $response, $args) use (
    $generalRequisitionsMaterialsDao,
    $generalRequisitionsProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $materials = $generalRequisitionsMaterialsDao->findAllMinAndMaxRequisitionByCompany($args['min_date'], $args['max_date'], $id_company);
    $products = $generalRequisitionsProductsDao->findAllMinAndMaxRequisitionByCompany($args['min_date'], $args['max_date'], $id_company);

    $requisitions = array_merge($materials, $products);

    $response->getBody()->write(json_encode($requisitions, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/requisitionDataValidation', function (Request $request, Response $response, $args) use (
    $generalMaterialsDao,
    $generalRequisitionsMaterialsDao,
    $generalClientsDao
) {
    $dataRequisition = $request->getParsedBody();

    if (isset($dataRequisition)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $requisition = $dataRequisition['importRequisition'];
        $dataImportRequisition = [];
        $debugg = [];

        for ($i = 0; $i < sizeof($requisition); $i++) {
            if (
                empty($requisition[$i]['refRawMaterial']) || empty($requisition[$i]['nameRawMaterial']) || empty($requisition[$i]['applicationDate']) ||
                empty($requisition[$i]['deliveryDate']) || empty($requisition[$i]['quantity']) || empty($requisition[$i]['purchaseOrder'])
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios. Fila: {$row}"));
            }
            if (
                trim(empty($requisition[$i]['refRawMaterial'])) || trim(empty($requisition[$i]['nameRawMaterial'])) || trim(empty($requisition[$i]['applicationDate'])) ||
                trim(empty($requisition[$i]['deliveryDate'])) || trim(empty($requisition[$i]['quantity'])) || trim(empty($requisition[$i]['purchaseOrder']))
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios. Fila: {$row}"));
            }

            // Obtener id material
            $findMaterial = $generalMaterialsDao->findMaterial($requisition[$i], $id_company);
            if (!$findMaterial) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Material no existe en la base de datos<br>Fila: {$row}"));
            } else $requisition[$i]['idMaterial'] = $findMaterial['id_material'];

            // Obtener id proveedor
            $findClient = $generalClientsDao->findClientByName($requisition[$i], $id_company, 2);
            if (!$findClient) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Cliente no existe en la base de datos o es tipo cliente.<br>Fila: {$row}"));
            } else $requisition[$i]['idProvider'] = $findClient['id_client'];

            if (sizeof($debugg) == 0) {
                $findRequisition = $generalRequisitionsMaterialsDao->findRequisition($requisition[$i], $id_company);
                !$findRequisition ? $insert = $insert + 1 : $update = $update + 1;
                $dataImportRequisition['insert'] = $insert;
                $dataImportRequisition['update'] = $update;
            }
        }
    } else
        $dataImportRequisition = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportRequisition;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addRequisition', function (Request $request, Response $response, $args) use (
    $requisitionsMaterialsDao,
    $transitMaterialsDao,
    $lastDataDao,
    $generalRequisitionsMaterialsDao,
    $generalMaterialsDao,
    $generalClientsDao,
    $generalRMStockDao,
    $explosionMaterialsDao,
    $generalExMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];
    $dataRequisition = $request->getParsedBody();

    $count = sizeof($dataRequisition);

    if ($count > 1) {
        $dataRequisition['idUser'] = $id_user;
        $requisition = $requisitionsMaterialsDao->insertRequisitionManualByCompany($dataRequisition, $id_company);

        if ($requisition == null) {
            $material = $transitMaterialsDao->calcQuantityTransitByMaterial($dataRequisition['idMaterial']);

            if (isset($material['transit']))
                $requisition = $transitMaterialsDao->updateQuantityTransitByMaterial($dataRequisition['idMaterial'], $material['transit']);
        }
        if ($requisition == null)
            $resp = array('success' => true, 'message' => 'Requisicion creada correctamente');
        else if (isset($requisition['info']))
            $resp = array('info' => true, 'message' => $requisition['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $requisition = $dataRequisition['importRequisition'];

        for ($i = 0; $i < sizeof($requisition); $i++) {
            $findMaterial = $generalMaterialsDao->findMaterial($requisition[$i], $id_company);
            $requisition[$i]['idMaterial'] = $findMaterial['id_material'];
            $findClient = $generalClientsDao->findClientByName($requisition[$i], $id_company, 2);
            $requisition[$i]['idProvider'] = $findClient['id_client'];

            $requisition[$i]['idUser'] = $id_user;
            $findRequisition = $generalRequisitionsMaterialsDao->findRequisition($requisition[$i], $id_company);

            if (!$findRequisition) {
                $resolution = $requisitionsMaterialsDao->insertRequisitionManualByCompany($requisition[$i], $id_company);
            } else {
                $requisition[$i]['idRequisition'] = $findRequisition['id_requisition_material'];
                $resolution = $requisitionsMaterialsDao->updateRequisitionManual($requisition[$i]);
            }

            if (isset($resolution['info'])) break;

            $material = $transitMaterialsDao->calcQuantityTransitByMaterial($requisition[$i]['idMaterial']);

            if (isset($material['transit']))
                $resolution = $transitMaterialsDao->updateQuantityTransitByMaterial($requisition[$i]['idMaterial'], $material['transit']);
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Requisicions importados correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $arr = $generalExMaterialsDao->findAllMaterialsConsolidated($id_company);

    $materials = $generalExMaterialsDao->setDataEXMaterials($arr);

    for ($i = 0; $i < sizeof($materials); $i++) {
        $findEX = $generalExMaterialsDao->findEXMaterial($materials[$i]['id_material']);

        if (!$findEX)
            $resolution = $explosionMaterialsDao->insertNewEXMByCompany($materials[$i], $id_company);
        else {
            $materials[$i]['id_explosion_material'] = $findEX['id_explosion_material'];
            $resolution = $explosionMaterialsDao->updateEXMaterials($materials[$i]);
        }

        if (intval($materials[$i]['available']) < 0) {
            $data = [];
            $data['idMaterial'] = $materials[$i]['id_material'];

            $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

            $id_provider = 0;

            if (isset($provider['id_provider'])) $id_provider = $provider['id_provider'];

            $data['numOrder'] = $materials[$i]['num_order'];
            $data['idProvider'] = $id_provider;
            $data['applicationDate'] = '';
            $data['deliveryDate'] = '';
            $data['requiredQuantity'] = abs($materials[$i]['available']);
            $data['purchaseOrder'] = '';

            $requisition = $generalRequisitionsMaterialsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

            if (!$requisition)
                $generalRequisitionsMaterialsDao->insertRequisitionAutoByCompany($data, $id_company);
            else {
                $data['idRequisition'] = $requisition['id_requisition_material'];
                $generalRequisitionsMaterialsDao->updateRequisitionAuto($data);
            }
        }
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateRequisition', function (Request $request, Response $response, $args) use (
    $requisitionsMaterialsDao,
    $requisitionsProductsDao,
    $transitMaterialsDao,
    $generalRequisitionsMaterialsDao
) {
    session_start();
    $id_user = $_SESSION['idUser'];
    $dataRequisition = $request->getParsedBody();

    if (!isset($dataRequisition['idUser']))
        $dataRequisition['idUser'] = $id_user;

    $requisition = null;

    $requisition = $requisitionsMaterialsDao->updateRequisitionManual($dataRequisition);

    if ($requisition == null) {
        $material = $transitMaterialsDao->calcQuantityTransitByMaterial($dataRequisition['idMaterial']);

        if (isset($material['transit']))
            $requisition = $transitMaterialsDao->updateQuantityTransitByMaterial($dataRequisition['idMaterial'], $material['transit']);
    }

    if ($requisition == null)
        $resp = array('success' => true, 'message' => 'Requisicion modificada correctamente');
    else if (isset($requisition['info']))
        $resp = array('info' => true, 'message' => $requisition['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/saveAdmissionDate', function (Request $request, Response $response, $args) use (
    $generalRequisitionsMaterialsDao,
    $generalRequisitionsProductsDao,
    $generalMaterialsDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $generalPlanCiclesMachinesDao,
    $generalProductsDao,
    $transitMaterialsDao,
    $generalOrdersDao,
    $usersRequisitonsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];

    $dataRequisition = $request->getParsedBody();

    $resolution = null;

    $resolution = $generalRequisitionsMaterialsDao->updateDateRequisition($dataRequisition);

    if ($resolution == null) {
        $material = $generalMaterialsDao->calcMaterialRecieved($dataRequisition['idMaterial']);

        $resolution = $generalMaterialsDao->updateQuantityMaterial($dataRequisition['idMaterial'], $material['quantity']);
    }

    if ($resolution == null) {
        $product = $generalProductsDao->findProduct($dataRequisition, $id_company);

        if ($product) {
            $resolution = $generalProductsDao->updateAccumulatedQuantity($product['id_product'], $material['quantity'], 2);
        }
    }

    if ($resolution == null) {
        $resolution = $usersRequisitonsDao->saveUserDeliverRequisitionMaterial($id_company, $dataRequisition['idRequisition'], $id_user);
    }

    if ($resolution == null) {
        $resolution = $transitMaterialsDao->updateQuantityTransitByMaterial($dataRequisition['idMaterial'], 0);
    }

    if ($resolution == null) {
        // $orders = explode(',', $dataRequisition['order']);

        // foreach ($orders as $arr) {
        //     $requisitions = $generalRequisitionsMaterialsDao->findRequisitionsByOrder($arr);

        //     if (sizeof($requisitions) == 0) {
        //         $order = $generalOrdersDao->findOrderByNumOrder($arr);

        //         if ($order[0]['status'] == 13)
        //             $resolution = $generalOrdersDao->changeStatus($arr, 12);
        //     }
        // }
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
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 1);
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

$app->post('/deleteRequisition', function (Request $request, Response $response, $args) use (
    $requisitionsMaterialsDao,
    $requisitionsProductsDao,
    $generalRequisitionsMaterialsDao,
    $generalRequisitionsProductsDao,
    $transitMaterialsDao
) {

    $dataRequisition = $request->getParsedBody();

    $requisitions = null;

    if ($dataRequisition['op'] == 1) {
        $requisitions = $requisitionsMaterialsDao->deleteRequisition($dataRequisition['idRequisition']);
    } else {
        $requisitions = $generalRequisitionsMaterialsDao->clearDataRequisition($dataRequisition['idRequisition']);

        if ($requisitions == null) {
            $material = $transitMaterialsDao->calcQuantityTransitByMaterial($dataRequisition['idMaterial']);

            if (isset($material['transit']))
                $requisitions = $transitMaterialsDao->updateQuantityTransitByMaterial($dataRequisition['idMaterial'], $material['transit']);
        }
    }

    if ($requisitions == null)
        $resp = array('success' => true, 'message' => 'Requisicion eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la Requisicion, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/usersRequisitions/{id_requisition}', function (Request $request, Response $response, $args) use ($usersRequisitonsDao) {
    $users = $usersRequisitonsDao->findAllUsersRequesitionsMaterialsById($args['id_requisition']);
    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});
