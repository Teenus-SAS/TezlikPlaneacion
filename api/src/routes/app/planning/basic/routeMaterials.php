<?php

use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\Dao\MagnitudesDao;
use TezlikPlaneacion\dao\MaterialsDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\UnitsDao;

$materialsDao = new MaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$generalOrdersDao = new GeneralOrdersDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$productsDao = new GeneralProductsDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$filterDataDao = new FilterDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/materials', function (Request $request, Response $response, $args) use (
    $materialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $materialsDao->findAllMaterialsByCompany($id_company);
    $response->getBody()->write(json_encode($materials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar Materias prima importada */
$app->post('/materialsDataValidation', function (Request $request, Response $response, $args) use (
    $generalMaterialsDao,
    $magnitudesDao,
    $unitsDao
) {
    $dataMaterial = $request->getParsedBody();

    if (isset($dataMaterial)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $materials = $dataMaterial['importMaterials'];

        for ($i = 0; $i < sizeof($materials); $i++) {
            if (
                empty($materials[$i]['refRawMaterial']) || empty($materials[$i]['nameRawMaterial']) ||
                empty($materials[$i]['magnitude']) || empty($materials[$i]['unit'])
            ) {
                $i = $i + 2;
                $dataImportMaterial = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            }

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($materials[$i]);

            if (!$magnitude) {
                $i = $i + 2;
                $dataImportMaterial = array('error' => true, 'message' => "Magnitud no existe en la base de datos. Fila: $i");
                break;
            }

            $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($materials[$i]);

            if (!$unit) {
                $i = $i + 2;
                $dataImportMaterial = array('error' => true, 'message' => "Unidad no existe en la base de datos. Fila: $i");
                break;
            }

            $findMaterial = $generalMaterialsDao->findMaterial($materials[$i], $id_company);
            if (!$findMaterial) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportMaterial['insert'] = $insert;
            $dataImportMaterial['update'] = $update;
        }
    } else
        $dataImportMaterial = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportMaterial, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao,
    $generalMaterialsDao,
    $magnitudesDao,
    $unitsDao
) {
    session_start();
    $dataMaterial = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $dataMaterials = sizeof($dataMaterial);

    if ($dataMaterials > 1) {
        $material = $generalMaterialsDao->findMaterial($dataMaterial, $id_company);
        if (!$material) {
            $materials = $materialsDao->insertMaterialsByCompany($dataMaterial, $id_company);
            if ($materials == null)
                $resp = array('success' => true, 'message' => 'Materia Prima creada correctamente');
            else if (isset($materials['info']))
                $resp = array('info' => true, 'message' => $materials['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'La materia prima ya existe. Ingrese una nueva');
    } else {
        $materials = $dataMaterial['importMaterials'];

        for ($i = 0; $i < sizeof($materials); $i++) {

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($materials[$i]);
            $materials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($materials[$i]);
            $materials[$i]['unit'] = $unit['id_unit'];

            $material = $generalMaterialsDao->findMaterial($materials[$i], $id_company);

            if (!$material)
                $resolution = $materialsDao->insertMaterialsByCompany($materials[$i], $id_company);
            else {
                $materials[$i]['idMaterial'] = $material['id_material'];
                $resolution = $materialsDao->updateMaterialsByCompany($materials[$i]);
            }
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia Prima Importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateMaterials', function (Request $request, Response $response, $args) use (
    $materialsDao,
    $generalMaterialsDao,
    $generalOrdersDao,
    $filterDataDao,
    $productsDao,
    $generalProgrammingDao
) {
    session_start();
    $dataMaterial = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $material = $generalMaterialsDao->findMaterial($dataMaterial, $id_company);
    !is_array($material) ? $data['id_material'] = 0 : $data = $material;
    if ($data['id_material'] == $dataMaterial['idMaterial'] || $data['id_material'] == 0) {
        $materials = $materialsDao->updateMaterialsByCompany($dataMaterial);

        // Cambiar estado pedidos
        $allOrders = $generalOrdersDao->findAllOrdersWithMaterialsByCompany($id_company);

        foreach ($allOrders as $arr) {
            $status = true;
            if ($arr['original_quantity'] > $arr['accumulated_quantity']) {
                if ($arr['quantity_material'] == NULL || !$arr['quantity_material']) {
                    $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Ficha Tecnica');
                    $status = false;
                    // break;
                } else if ($arr['quantity_material'] <= 0) {
                    $generalOrdersDao->changeStatus($arr['id_order'], 'Sin Materia Prima');
                    $status = false;
                    // break;
                }
            }

            foreach ($allOrders as &$order) {
                if ((!isset($arr['status_mp']) || $arr['status_mp'] === false) && $order['id_order'] == $arr['id_order']) {
                    // if ($order['id_order'] == $arr['id_order']) {
                    $order['status_mp'] = $status;
                }
            }
            unset($order);

            if ($status == true && $arr['programming'] != 0) {
                $generalOrdersDao->changeStatus($arr['id_order'], 'Programado');

                $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
                !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
                $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
            }
        }

        $orders = $filterDataDao->filterDuplicateArray($allOrders, 'id_order');

        for ($i = 0; $i < sizeof($orders); $i++) {
            if ($orders[$i]['status_mp'] == true) {
                if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
                    $generalOrdersDao->changeStatus(
                        $orders[$i]['id_order'],
                        'Despacho'
                    );
                    $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
                } else {
                    $accumulated_quantity = $orders[$i]['accumulated_quantity'];
                }

                if ($orders[$i]['status'] != 'Despacho') {
                    $date = date('Y-m-d');

                    $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
                }

                $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
                !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                $arr = $generalMaterialsDao->findReservedMaterial($orders[$i]['id_product']);
                !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                $generalMaterialsDao->updateReservedMaterial($orders[$i]['id_product'], $arr['reserved']);

                $generalMaterialsDao->updateQuantityMaterial($orders[$i]['id_product'], $accumulated_quantity, 1);
            }
        }
        // $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        // for ($i = 0; $i < sizeof($orders); $i++) {
        //     $status = true;
        //     // Checkear cantidades
        //     $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);

        //     if ($order['status'] != 'En Produccion' && $order['status'] != 'Entregado' && $order['status'] != 'Fabricado') {
        //         if ($order['original_quantity'] > $order['accumulated_quantity']) {
        //             // Ficha tecnica
        //             $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

        //             if (sizeof($productsMaterials) == 0) {
        //                 $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Sin Ficha Tecnica');
        //                 $status = false;
        //             } else {
        //                 foreach ($productsMaterials as $arr) {
        //                     if ($arr['quantity_material'] <= 0) {
        //                         $order = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Sin Materia Prima');
        //                         $status = false;
        //                         break;
        //                     }
        //                 }
        //             }
        //         }

        //         if ($status == true) {
        //             if ($order['original_quantity'] <= $order['accumulated_quantity']) {
        //                 $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Despacho');
        //                 $accumulated_quantity = $order['accumulated_quantity'] - $order['original_quantity'];
        //             } else {
        //                 $accumulated_quantity = $order['accumulated_quantity'];
        //                 $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Programar');
        //             }

        //             if ($order['status'] != 'Despacho') {
        //                 $date = date('Y-m-d');

        //                 $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
        //             }

        //             $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
        //             !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
        //             $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

        //             $productsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
        //             $programming = $generalProgrammingDao->findProgrammingByOrder($orders[$i]['id_order']);
        //             if (sizeof($programming) > 0) {
        //                 $generalOrdersDao->changeStatus($orders[$i]['id_order'], 'Programado');

        //                 $productsMaterials = $productsMaterialsDao->findAllProductsmaterials($orders[$i]['id_product'], $id_company);

        //                 foreach ($productsMaterials as $arr) {
        //                     $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
        //                     !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
        //                     $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
        //                 }
        //             }
        //         }
        //     }
        // }

        if ($materials == null)
            $resp = array('success' => true, 'message' => 'Materia Prima actualizada correctamente');
        else if (isset($materials['info']))
            $resp = array('info' => true, 'message' => $materials['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'La materia prima ya existe. Ingrese una nueva');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteMaterial/{id_material}', function (Request $request, Response $response, $args) use (
    $materialsDao
) {
    $materials = $materialsDao->deleteMaterial($args['id_material']);
    if ($materials == null)
        $resp = array('success' => true, 'message' => 'Material eliminado correctamente');

    if ($materials != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar el material, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
