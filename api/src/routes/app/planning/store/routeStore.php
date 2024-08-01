<?php

use TezlikPlaneacion\dao\AutenticationUserDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\StoreDao;

$storeDao = new StoreDao();
$autenticationDao = new AutenticationUserDao();
$programmingDao = new GeneralProgrammingDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();

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
        $materials = $productsMaterialsDao->findAllProductsmaterials($programming[$i]['id_product'], $id_company);

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

$app->post('/deliverStore', function (Request $request, Response $response, $args) use (
    $storeDao,
    $autenticationDao,
    $generalMaterialsDao
) {
    session_start();
    $id_rol = $_SESSION['rol'];
    $id_user = $_SESSION['idUser'];
    $dataStore = $request->getParsedBody();

    // $user = $autenticationDao->findByEmail($dataStore['email'], 1);

    // if (!$user) {
    //     $resp = array('error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente');
    //     $response->getBody()->write(json_encode($resp));
    //     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    // }

    // /* Valida el password del usuario */
    // if (!password_verify($dataStore['password'], $user['password'])) {
    //     $resp = array('error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente');
    //     $response->getBody()->write(json_encode($resp));
    //     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    // }

    if ($id_rol != 2) {
        $resp = array('error' => true, 'message' => 'Usuario no autorizado, valide nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    $store = $storeDao->saveDelivery($dataStore, 1);
    if ($store == null) {
        $store = $generalMaterialsDao->updateStoreMaterial($dataStore);

        if ($dataStore['pending'] == 0) {
            date_default_timezone_set('America/Bogota');

            $date = date('Y-m-d H:i:s');

            $store = $generalMaterialsDao->updateDeliveryDateMaterial($dataStore['idMaterial'], $date);
        }
    }

    if ($store == null) {
        $store = $generalMaterialsDao->saveUserDeliveredMaterial($dataStore['idMaterial'], $id_user);
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
