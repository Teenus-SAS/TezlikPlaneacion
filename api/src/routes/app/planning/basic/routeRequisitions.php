<?php

use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsMaterialsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsProductsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\LastDataDao;
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
$generalClientsDao = new GeneralClientsDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$generalExMaterialsDao = new GeneralExplosionMaterialsDao();
$generalRMStockDao = new GeneralRMStockDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/requisitions', function (Request $request, Response $response, $args) use (
    $generalRequisitionsMaterialsDao,
    $generalRequisitionsProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $materials = $generalRequisitionsMaterialsDao->findAllActualRequisitionByCompany($id_company);
    // $products = $generalRequisitionsProductsDao->findAllActualRequisitionByCompany($id_company);

    // $requisitions = array_merge($materials, $products);

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
        // $findRequisition = $generalRequisitionsDao->findRequisition($dataRequisition, $id_company);
        // if (!$findRequisition) {
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
        // } else
        // $resp = array('error' => true, 'message' => 'Material ya existente en la requisicion. Ingrese nuevo material');
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
            // $lastData = $lastDataDao->lastInsertedRequisitionId($id_company);
            // $resolution = $generalRequisitionsDao->saveUserRequisition($lastData['id_requisition'], $id_user);

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

    // if (isset($dataRequisition['idMaterial'])) {
    $requisition = $requisitionsMaterialsDao->updateRequisitionManual($dataRequisition);

    if ($requisition == null) {
        $material = $transitMaterialsDao->calcQuantityTransitByMaterial($dataRequisition['idMaterial']);

        if (isset($material['transit']))
            $requisition = $transitMaterialsDao->updateQuantityTransitByMaterial($dataRequisition['idMaterial'], $material['transit']);
    }
    // }

    // if (isset($dataRequisition['idProduct']))
    //     $requisition = $requisitionsProductsDao->updateRequisitionManual($dataRequisition);

    if ($requisition == null)
        $resp = array('success' => true, 'message' => 'Requisicion modificada correctamente');
    else if (isset($requisition['info']))
        $resp = array('info' => true, 'message' => $requisition['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    // } else
    // $resp = array('error' => true, 'message' => 'Material ya existente en la requisicion. Ingrese nuevo material');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/saveAdmissionDate', function (Request $request, Response $response, $args) use (
    $generalRequisitionsMaterialsDao,
    $generalRequisitionsProductsDao,
    $generalMaterialsDao,
    $generalProductsDao,
    $transitMaterialsDao,
    $usersRequisitonsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $id_user = $_SESSION['idUser'];

    $dataRequisition = $request->getParsedBody();

    $requisition = null;

    // if (isset($dataRequisition['idMaterial'])) {
    $requisition = $generalRequisitionsMaterialsDao->updateDateRequisition($dataRequisition);

    if ($requisition == null) {
        $material = $generalMaterialsDao->calcMaterialRecieved($dataRequisition['idMaterial']);

        $requisition = $generalMaterialsDao->updateQuantityMaterial($dataRequisition['idMaterial'], $material['quantity']);
    }

    if ($requisition == null) {
        $requisition = $usersRequisitonsDao->saveUserDeliverRequisitionMaterial($id_company, $dataRequisition['idRequisition'], $id_user);
    }

    if ($requisition == null) {
        // $material = $transitMaterialsDao->calcQuantityTransitByMaterial($dataRequisition['idMaterial']);

        // if (isset($material['transit']))
        $requisition = $transitMaterialsDao->updateQuantityTransitByMaterial($dataRequisition['idMaterial'], 0);
    }
    // } else {
    //     $requisition = $generalRequisitionsProductsDao->updateDateRequisition($dataRequisition);

    //     if ($requisition == null) {
    //         $product = $generalProductsDao->calcProductRecieved($dataRequisition['idProduct']);

    //         $requisition = $generalProductsDao->updateAccumulatedQuantity($dataRequisition['idProduct'], $product['quantity'], 2);
    //     }

    //     if ($requisition == null) {
    //         $requisition = $usersRequisitonsDao->saveUserDeliverRequisitionproduct($id_company, $dataRequisition['idRequisition'], $id_user);
    //     }
    // }

    if ($requisition == null)
        $resp = array('success' => true, 'message' => 'Fecha guardada correctamente');
    else if (isset($requisition['info']))
        $resp = array('info' => true, 'message' => $requisition['message']);
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

    // if (isset($dataRequisition['idMaterial'])) {
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
    // }

    // if (isset($dataRequisition['idProduct'])) {
    //     if ($dataRequisition['op'] == 1) {
    //         $requisitions = $requisitionsProductsDao->deleteRequisition($dataRequisition['idRequisition']);
    //     } else {
    //         $requisitions = $generalRequisitionsProductsDao->clearDataRequisition($dataRequisition['idRequisition']);
    //     }
    // }

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
