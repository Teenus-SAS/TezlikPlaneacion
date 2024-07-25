<?php

use TezlikPlaneacion\Dao\ConversionUnitsDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsMaterialsDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\RequisitionsDao;
use TezlikPlaneacion\dao\RMStockDao;

$stockDao = new RMStockDao();
$generalStockDao = new GeneralRMStockDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$generalProductsDao = new GeneralProductsDao();
$minimumStockDao = new MinimumStockDao();
$productMaterialsDao = new ProductsMaterialsDao();
$generalProductsMaterialsDao = new GeneralProductsMaterialsDao();
$generalClientsDao = new GeneralClientsDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$generalRMStockDao = new GeneralRMStockDao();
$requisitionsDao = new RequisitionsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/rMStock', function (Request $request, Response $response, $args) use ($stockDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $stock = $stockDao->findAllStockByCompany($id_company);
    $response->getBody()->write(json_encode($stock, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/stockMaterials', function (Request $request, Response $response, $args) use ($generalMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $materials = $generalMaterialsDao->findAllMaterialsStockByCompany($id_company);
    $response->getBody()->write(json_encode($materials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/rMStockDataValidation', function (Request $request, Response $response, $args) use (
    $generalStockDao,
    $generalMaterialsDao,
    $generalClientsDao
) {
    $dataStock = $request->getParsedBody();

    if (isset($dataStock)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $stock = $dataStock['importStock'];

        for ($i = 0; $i < sizeof($stock); $i++) {
            if (
                empty($stock[$i]['refRawMaterial']) || empty($stock[$i]['nameRawMaterial']) ||
                $stock[$i]['min'] == '' || $stock[$i]['max'] == '' || $stock[$i]['quantity'] == ''
            ) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            if (
                empty(trim($stock[$i]['refRawMaterial'])) || empty(trim($stock[$i]['nameRawMaterial'])) ||
                trim($stock[$i]['min']) == '' || trim($stock[$i]['max']) == '' || trim($stock[$i]['quantity']) == ''
            ) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            $min = str_replace(',', '.', $stock[$i]['min']);
            $max = str_replace(',', '.', $stock[$i]['max']);
            $quantity = str_replace(',', '.', $stock[$i]['quantity']);

            $data = $min * $max * $quantity;

            if ($data <= 0 || is_nan($data)) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "La cantidad debe ser mayor a cero (0)<br>Fila: {$i}");
                break;
            }

            // Obtener id materia prima
            $findMaterial = $generalMaterialsDao->findMaterial($stock[$i], $id_company);
            if (!$findMaterial) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "Materia prima no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $stock[$i]['idMaterial'] = $findMaterial['id_material'];

            // Obtener id proveedor
            $findClient = $generalClientsDao->findClientByName($stock[$i], $id_company, 2);
            if (!$findClient) {
                $i = $i + 2;
                $dataImportStock = array('error' => true, 'message' => "Cliente no existe en la base de datos o es tipo cliente.<br>Fila: {$i}");
                break;
            } else $stock[$i]['idProvider'] = $findClient['id_client'];

            $findstock = $generalStockDao->findStock($stock[$i]);
            if (!$findstock) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportStock['insert'] = $insert;
            $dataImportStock['update'] = $update;
        }
    } else
        $dataImportStock = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportStock, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addRMStock', function (Request $request, Response $response, $args) use (
    $stockDao,
    $generalStockDao,
    $generalMaterialsDao,
    $generalClientsDao,
    $productsMaterialsDao,
    $generalProductsMaterialsDao,
    $conversionUnitsDao,
    $generalProductsDao,
    $minimumStockDao,
    $explosionMaterialsDao,
    $generalRMStockDao,
    $requisitionsDao,
    $generalRequisitionsDao
) {
    session_start();
    $dataStock = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    if (empty($dataStock['importStock'])) {

        $findStock = $generalStockDao->findStock($dataStock);

        if (!$findStock) {
            $resolution = $stockDao->insertStockByCompany($dataStock, $id_company);

            if ($resolution == null) {
                $dataProducts = $generalProductsDao->findProductByMaterial($dataStock['idMaterial'], $id_company);

                foreach ($dataProducts as $j) {
                    if ($j['id_product'] != 0) {
                        if (isset($resolution['info'])) break;

                        // Calcular precio total materias
                        // Consultar todos los datos del producto
                        $productsMaterial = $productsMaterialsDao->findAllProductsmaterials($j['id_product'], $id_company);

                        foreach ($productsMaterial as $k) {
                            // Obtener materia prima
                            $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                            // Convertir unidades
                            $quantity = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                            // Guardar Unidad convertida
                            $generalProductsMaterialsDao->saveQuantityConverted($k['id_product_material'], $quantity);

                            $arr = $minimumStockDao->calcStockByMaterial($dataStock['idMaterial']);

                            if (isset($arr['stock']))
                                $resolution = $generalMaterialsDao->updateStockMaterial($dataStock['idMaterial'], $arr['stock']);
                        }
                    }
                }
            }

            if ($resolution == null) {
                $arr = $explosionMaterialsDao->findAllMaterialsConsolidatedByMaterial($dataStock['idMaterial']);

                $materials = $explosionMaterialsDao->setDataEXMaterials($arr);

                for ($i = 0; $i < sizeof($materials); $i++) {
                    if ($materials[$i]['available'] < 0) {
                        $data = [];
                        $data['idMaterial'] = $materials[$i]['id_material'];

                        $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                        $id_provider = 0;

                        if ($provider) $id_provider = $provider['id_provider'];

                        $data['idProvider'] = $id_provider;

                        $data['applicationDate'] = '';
                        $data['deliveryDate'] = '';
                        $data['requiredQuantity'] = abs($materials[$i]['available']);
                        $data['purchaseOrder'] = '';
                        $data['requestedQuantity'] = 0;

                        $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                        if (!$requisition)
                            $requisitionsDao->insertRequisitionByCompany($data, $id_company);
                        else {
                            $data['idRequisition'] = $requisition['id_requisition'];
                            $requisitionsDao->updateRequisition($data);
                        }
                    }
                }
            }

            // if ($resolution == null) {
            //     $products = $generalProductsMaterialsDao->findAllProductByMaterial($dataStock['idMaterial']);

            //     foreach ($products as $arr) {
            //         $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
            //         if (isset($product['stock']))
            //             $resolution = $generalProductsDao->updateStockByProduct($arr['id_product'], $product['stock']);

            //         if (isset($resolution['info'])) break;
            //     }
            // }


            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Stock creado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Stock ya existe. Ingrese uno nuevo');
    } else {
        $stock = $dataStock['importStock'];

        $resolution = 1;

        for ($i = 0; $i < sizeof($stock); $i++) {
            if (isset($resolution['info'])) break;
            // Obtener id materia prima
            $findMaterial = $generalMaterialsDao->findMaterial($stock[$i], $id_company);
            $stock[$i]['idMaterial'] = $findMaterial['id_material'];

            // Obtener id proveedor
            $findClient = $generalClientsDao->findClientByName($stock[$i], $id_company, 2);
            $stock[$i]['idProvider'] = $findClient['id_client'];

            $findstock = $generalStockDao->findstock($stock[$i], $id_company);
            if (!$findstock)
                $resolution = $stockDao->insertStockByCompany($stock[$i], $id_company);
            else {
                $stock[$i]['idStock'] = $findstock['id_stock_material'];
                $resolution = $stockDao->updateStock($stock[$i]);
            }

            if (isset($resolution['info'])) break;

            $dataProducts = $generalProductsDao->findProductByMaterial($stock[$i]['idMaterial'], $id_company);

            foreach ($dataProducts as $j) {
                if ($j['id_product'] != 0) {
                    if (isset($resolution['info'])) break;

                    // Calcular precio total materias
                    // Consultar todos los datos del producto
                    $productsMaterial = $productsMaterialsDao->findAllProductsmaterials($j['id_product'], $id_company);

                    foreach ($productsMaterial as $k) {
                        // Obtener materia prima
                        $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                        // Convertir unidades
                        $quantity = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                        // Guardar Unidad convertida
                        $generalProductsMaterialsDao->saveQuantityConverted($k['id_product_material'], $quantity);

                        $arr = $minimumStockDao->calcStockByMaterial($stock[$i]['idMaterial']);

                        if (isset($arr['stock']))
                            $resolution = $generalMaterialsDao->updateStockMaterial($stock[$i]['idMaterial'], $arr['stock']);
                    }
                }
            }

            if (isset($resolution['info'])) break;

            // $products = $generalProductsMaterialsDao->findAllProductByMaterial($stock[$i]['idMaterial']);

            // foreach ($products as $arr) {
            //     $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
            //     if (isset($product['stock']))
            //         $resolution = $generalProductsDao->updateStockByProduct($arr['id_product'], $product['stock']);

            //     if (isset($resolution['info'])) break;
            // }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Stock importado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateRMStock', function (Request $request, Response $response, $args) use (
    $stockDao,
    $generalStockDao,
    $productsMaterialsDao,
    $generalProductsMaterialsDao,
    $generalProductsDao,
    $generalMaterialsDao,
    $minimumStockDao,
    $conversionUnitsDao,
    $explosionMaterialsDao,
    $generalRMStockDao,
    $requisitionsDao,
    $generalRequisitionsDao
) {
    session_start();

    $id_company = $_SESSION['id_company'];
    $dataStock = $request->getParsedBody();

    $stock = $generalStockDao->findStock($dataStock);
    !is_array($stock) ? $data['id_stock_material'] = 0 : $data = $stock;

    if ($data['id_stock_material'] == $dataStock['idStock'] || $data['id_stock_material'] == 0) {
        $resolution = $stockDao->updateStock($dataStock);

        if ($resolution == null) {
            $dataProducts = $generalProductsDao->findProductByMaterial($dataStock['idMaterial'], $id_company);

            foreach ($dataProducts as $j) {
                if ($j['id_product'] != 0) {
                    if (isset($resolution['info'])) break;

                    // Calcular precio total materias
                    // Consultar todos los datos del producto
                    $productsMaterial = $productsMaterialsDao->findAllProductsmaterials($j['id_product'], $id_company);

                    foreach ($productsMaterial as $k) {
                        // Obtener materia prima
                        $material = $generalMaterialsDao->findMaterialAndUnits($k['id_material'], $id_company);

                        // Convertir unidades
                        $quantity = $conversionUnitsDao->convertUnits($material, $k, $k['quantity']);

                        // Guardar Unidad convertida
                        $generalProductsMaterialsDao->saveQuantityConverted($k['id_product_material'], $quantity);

                        $arr = $minimumStockDao->calcStockByMaterial($dataStock['idMaterial']);

                        if (isset($arr['stock']))
                            $resolution = $generalMaterialsDao->updateStockMaterial($dataStock['idMaterial'], $arr['stock']);
                    }
                }
            }
        }

        if ($resolution == null) {
            $arr = $explosionMaterialsDao->findAllMaterialsConsolidatedByMaterial($dataStock['idMaterial']);

            $materials = $explosionMaterialsDao->setDataEXMaterials($arr);

            for ($i = 0; $i < sizeof($materials); $i++) {
                if ($materials[$i]['available'] < 0) {
                    $data = [];
                    $data['idMaterial'] = $materials[$i]['id_material'];

                    $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                    $id_provider = 0;

                    if ($provider) $id_provider = $provider['id_provider'];

                    $data['idProvider'] = $id_provider;

                    $data['applicationDate'] = '';
                    $data['deliveryDate'] = '';
                    $data['requestedQuantity'] = abs($materials[$i]['available']);
                    $data['purchaseOrder'] = '';
                    $data['requestedQuantity'] = 0;

                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                    if (!$requisition)
                        $requisitionsDao->insertRequisitionByCompany($data, $id_company);
                    else {
                        $data['idRequisition'] = $requisition['id_requisition'];
                        $requisitionsDao->updateRequisition($data);
                    }
                }
            }
        }

        // if ($resolution == null) {
        //     $products = $generalProductsMaterialsDao->findAllProductByMaterial($dataStock['idMaterial']);

        //     foreach ($products as $arr) {
        //         $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
        //         if (isset($product['stock']))
        //             $resolution = $generalProductsDao->updateStockByProduct($arr['id_product'], $product['stock']);

        //         if (isset($resolution['info'])) break;
        //     }
        // }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Stock actualizado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Stock ya existe. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

// $app->get('/deletePlanstock/{id_stock}', function (Request $request, Response $response, $args) use ($stockDao) {
//     $stock = $stockDao->deletestock($args['id_stock']);

//     if ($stock == null)
//         $resp = array('success' => true, 'message' => 'Stock eliminado correctamente');

//     if ($stock != null)
//         $resp = array('error' => true, 'message' => 'No es posible eliminar el Stock, existe información asociada a él');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withHeader('Content-Type', 'application/json');
// });
