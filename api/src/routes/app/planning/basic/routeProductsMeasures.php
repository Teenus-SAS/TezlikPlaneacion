<?php

use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\Dao\GeneralCompositeProductsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralPMeasuresDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsMaterialsDao;
use TezlikPlaneacion\dao\GeneralProductsPlansDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\GeneralPStockDao;
use TezlikPlaneacion\dao\GeneralUnitSalesDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\MaterialsDao;
use TezlikPlaneacion\dao\MaterialsInventoryDao;
use TezlikPlaneacion\dao\PlanCiclesMachineDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\ProductsInventoryDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProductsMeasuresDao;
use TezlikPlaneacion\dao\ProductsPlansDao;
use TezlikPlaneacion\dao\ProductsTypeDao;
use TezlikPlaneacion\dao\RMStockDao;

$productsMeasuresDao = new ProductsMeasuresDao();
$materialsDao = new MaterialsDao();
$materialsInventoryDao = new MaterialsInventoryDao();
$generalPMeasuresDao = new GeneralPMeasuresDao();
$productsTypeDao = new ProductsTypeDao();
$productsInventoryDao = new ProductsInventoryDao();
$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();
$productMaterialDao = new ProductsMaterialsDao();
$generalProductsMaterialsDao = new GeneralProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$planCiclesMachineDao = new PlanCiclesMachineDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$productsPlansDao = new ProductsPlansDao();
$generalProductsPlansDao = new GeneralProductsPlansDao();
$generalPStockDao = new GeneralPStockDao();
$generalUnitSalesDao = new GeneralUnitSalesDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();
$lastDataDao = new LastDataDao();
$rMStockDao = new RMStockDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/productsMeasures', function (Request $request, Response $response, $args) use ($productsMeasuresDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $productsMeasuresDao->findAllProductsMeasuresByCompany($id_company);
    $response->getBody()->write(json_encode($products));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos importados */
$app->post('/productsMeasuresDataValidation', function (Request $request, Response $response, $args) use (
    $generalPMeasuresDao,
    $generalProductsDao,
    $productsTypeDao
) {
    $dataProduct = $request->getParsedBody();

    if (isset($dataProduct)) {
        session_start();
        $id_company = $_SESSION['id_company'];
        $flag_products_measure = $_SESSION['flag_products_measure'];

        $products = $dataProduct['importProducts'];

        $dataImportProduct = [];

        $debugg = [];

        for ($i = 0; $i < count($products); $i++) {
            if (
                empty($products[$i]['referenceProduct']) || empty($products[$i]['product']) ||
                empty($products[$i]['origin']) || empty($products[$i]['composite'])
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
            }

            if (
                empty(trim($products[$i]['referenceProduct'])) || empty(trim($products[$i]['product'])) ||
                empty(trim($products[$i]['origin'])) || empty(trim($products[$i]['composite']))
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
            }

            if ($flag_products_measure == '1') {
                if (empty($products[$i]['productType'])) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
                }

                if (empty(trim($products[$i]['productType']))) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
                }

                $findPType = $productsTypeDao->findProductsType($products[$i], $id_company);

                if (!$findPType) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Tipo de producto no existe en la base de datos. Fila: $row"));
                }

                $origin = $products[$i]['origin'];

                if ($origin == 'MANUFACTURADO') {
                    if (
                        empty($products[$i]['width']) || empty($products[$i]['length']) ||
                        empty($products[$i]['usefulLength']) || empty($products[$i]['totalWidth']) || empty($products[$i]['productType'])
                    ) {
                        $row = $i + 2;
                        array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
                    }

                    if (
                        empty(trim($products[$i]['width'])) || empty(trim($products[$i]['length'])) ||
                        empty(trim($products[$i]['usefulLength'])) || empty(trim($products[$i]['totalWidth'])) || empty(trim($products[$i]['productType']))
                    ) {
                        $row = $i + 2;
                        array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
                    }

                    $validate = floatval($products[$i]['width']) * floatval($products[$i]['length']) * floatval($products[$i]['usefulLength']) *
                        floatval($products[$i]['totalWidth']);

                    if (is_nan($validate) || $validate <= 0) {
                        $row = $i + 2;
                        array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila: $row"));
                    }
                }
            }

            $item = $products[$i];
            $refProduct = trim($item['referenceProduct']);
            $nameProduct = strtoupper(trim($item['product']));

            if (isset($duplicateTracker[$refProduct]) || isset($duplicateTracker[$nameProduct])) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "fila-$row: Duplicidad encontrada: $refProduct, $nameProduct"));
            } else {
                $duplicateTracker[$refProduct] = true;
                $duplicateTracker[$nameProduct] = true;
            }

            $findProduct = $generalProductsDao->findProductByReferenceOrName($products[$i], $id_company);

            if (sizeof($findProduct) > 1) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "fila-$row: Referencia y/o producto ya existe, $refProduct, $nameProduct"));
            }

            if ($findProduct) {
                if ($findProduct[0]['product'] != $nameProduct || $findProduct[0]['reference'] != $refProduct) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "fila-$row: Referencia y/o producto ya existe: $refProduct, $nameProduct"));
                }
            }
        }

        $insert = 0;
        $update = 0;

        if (sizeof($debugg) == 0) {
            for ($i = 0; $i < count($products); $i++) {
                $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);

                if (!$findProduct)
                    $insert = $insert + 1;
                else $update = $update + 1;

                $dataImportProduct['insert'] = $insert;
                $dataImportProduct['update'] = $update;
            }
        }
    } else
        $dataImportProduct = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportProduct;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductMeasure', function (Request $request, Response $response, $args) use (
    $productsMeasuresDao,
    $materialsDao,
    $lastDataDao,
    $productsInventoryDao,
    $productsTypeDao,
    $productsDao,
    $materialsInventoryDao,
    $generalPMeasuresDao,
    $productMaterialDao,
    $rMStockDao,
    $generalProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag_products_measure = $_SESSION['flag_products_measure'];
    $dataProduct = $request->getParsedBody();

    /* Inserta datos */
    $dataProducts = sizeof($dataProduct);

    if ($dataProducts > 1) {
        $findProduct = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

        if (!$findProduct) {
            $resolution = $productsDao->insertProductByCompany($dataProduct, $id_company);

            if ($resolution == null && $dataProduct['origin'] == 1) {
                $data = [];
                $data['idMaterialType'] = 0;
                $data['refRawMaterial'] = $dataProduct['referenceProduct'];
                $data['nameRawMaterial'] = $dataProduct['product'];
                $data['unit'] = 12;

                $resolution = $materialsDao->insertMaterialsByCompany($data, $id_company);

                if ($resolution == null) {
                    $lastDataMP = $lastDataDao->lastInsertedMaterialId($id_company);
                    $dataMaterial['idMaterial'] = $lastDataMP['id_material'];
                    $dataMaterial['quantity'] = 0;

                    $resolution = $materialsInventoryDao->insertMaterialInventory($dataMaterial, $id_company);
                }

                if ($resolution == null) {
                    $lastDataPT = $lastDataDao->lastInsertedProductId($id_company);
                    $data = [];
                    $data['material'] = $lastDataMP['id_material'];
                    $data['unit'] = 12;
                    $data['idProduct'] = $lastDataPT['id_product'];
                    $data['quantity'] = 1;

                    $resolution = $productMaterialDao->insertProductsMaterialsByCompany($data, $id_company);
                }

                if ($resolution == null) {
                    $data = [];
                    $data['idMaterial'] = $lastDataMP['id_material'];
                    $data['idProvider'] = 0;
                    $data['min'] = 0;
                    $data['max'] = 0;
                    $data['quantity'] = 0;

                    $resolution = $rMStockDao->insertStockByCompany($data, $id_company);
                }
            }

            if ($resolution == null) {
                $lastData = $lastDataDao->lastInsertedProductId($id_company);
                $dataProduct['idProduct'] = $lastData['id_product'];
                $dataProduct['quantity'] = 0;

                $resolution = $productsInventoryDao->insertProductsInventory($dataProduct, $id_company);
            }

            if ($resolution == null && $flag_products_measure == '1') {
                $resolution = $productsMeasuresDao->insertPMeasureByCompany($dataProduct, $id_company);
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Producto creado correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
        } else {
            $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');
        }
    } else {
        $products = $dataProduct['importProducts'];

        for ($i = 0; $i < sizeof($products); $i++) {
            $products[$i]['origin'] == 'COMERCIALIZADO' ? $products[$i]['origin'] = 1 : $products[$i]['origin'] = 2;
            $products[$i]['composite'] == 'SI' ? $products[$i]['composite'] = 1 : $products[$i]['composite'] = 0;

            if ($flag_products_measure == '1') {
                $findPType = $productsTypeDao->findProductsType($products[$i], $id_company);
                $products[$i]['idProductType'] = $findPType['id_product_type'];
            } else $products[$i]['idProductType'] = 0;

            $findProduct = $generalProductsDao->findProduct($products[$i], $id_company);

            if (!$findProduct) {
                $resolution = $productsDao->insertProductByCompany($products[$i], $id_company);
                if (isset($resolution['info'])) break;

                $lastData = $lastDataDao->lastInsertedProductId($id_company);
                $products[$i]['idProduct'] = $lastData['id_product'];
                $products[$i]['quantity'] = 0;

                if (isset($resolution['info'])) break;

                $resolution = $productsInventoryDao->insertProductsInventory($products[$i], $id_company);
            } else {
                $products[$i]['idProduct'] = $findProduct['id_product'];

                $resolution = $productsDao->updateProductByCompany($products[$i], $id_company);
            }
            if (isset($resolution['info'])) break;

            $resolution = $generalProductsDao->changeCompositeProducts($products[$i]['idProduct'], $products[$i]['composite']);

            if (isset($resolution['info'])) break;

            if ($flag_products_measure == '1') {
                $findPMeasure = $generalPMeasuresDao->findProductMeasure($products[$i], $id_company);

                if (!$findPMeasure)
                    $resolution = $productsMeasuresDao->insertPMeasureByCompany($products[$i], $id_company);
                else {
                    $products[$i]['idProductMeasure'] = $findPMeasure['id_product_measure'];

                    $resolution = $productsMeasuresDao->updatePMeasure($products[$i]);
                }
            }
            if (isset($resolution['info'])) break;

            if ($products[$i]['origin'] == 1) {
                $data = [];
                $data['idMaterialType'] = 0;
                $data['refRawMaterial'] = $products[$i]['referenceProduct'];
                $data['nameRawMaterial'] = $products[$i]['product'];
                $data['unit'] = 12;

                $resolution = $materialsDao->insertMaterialsByCompany($data, $id_company);

                if (isset($resolution['info'])) break;

                $lastDataMP = $lastDataDao->lastInsertedMaterialId($id_company);
                $products[$i]['idMaterial'] = $lastDataMP['id_material'];
                $products[$i]['quantity'] = 0;

                $resolution = $materialsInventoryDao->insertMaterialInventory($products[$i], $id_company);

                if (isset($resolution['info'])) break;

                $lastDataPT = $lastDataDao->lastInsertedProductId($id_company);
                $data = [];
                $data['material'] = $lastDataMP['id_material'];
                $data['unit'] = 12;
                $data['idProduct'] = $lastDataPT['id_product'];
                $data['quantity'] = 1;

                $resolution = $productMaterialDao->insertProductsMaterialsByCompany($data, $id_company);

                if (isset($resolution['info'])) break;

                $data = [];
                $data['idMaterial'] = $lastDataMP['id_material'];
                $data['idProvider'] = 0;
                $data['min'] = 0;
                $data['max'] = 0;
                $data['quantity'] = 0;

                $resolution = $rMStockDao->insertStockByCompany($data, $id_company);

                if (isset($resolution['info'])) break;
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Productos importados correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras importaba los datos. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateProductMeasure', function (Request $request, Response $response, $args) use (
    $productsMeasuresDao,
    $productsDao,
    $generalProductsDao,
    $generalPMeasuresDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag_products_measure = $_SESSION['flag_products_measure'];
    $status = true;
    $dataProduct = $request->getParsedBody();

    $products = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

    foreach ($products as $arr) {
        if ($arr['id_product'] != $dataProduct['idProduct']) {
            $status = false;
            break;
        }
    }

    if ($status == true) {
        $resolution = $productsDao->updateProductByCompany($dataProduct, $id_company);

        if ($resolution == null && $flag_products_measure == '1')
            $resolution = $productsMeasuresDao->updatePMeasure($dataProduct);

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Producto actualizado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras guardaba los datos. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteProductMeasure', function (Request $request, Response $response, $args) use (
    $productsDao,
    $productsMeasuresDao,
    $generalProductsDao,
    $generalProductsMaterialsDao,
    $generalCompositeProductsDao,
    $generalPlanCiclesMachinesDao,
    $generalProductsPlansDao,
    $generalPStockDao,
    $generalUnitSalesDao,
    $generalOrdersDao,
    $generalProgrammingDao,
    $generalProgrammingRoutesDao
) {
    session_start();
    $dataProduct = $request->getParsedBody();
    $flag_products_measure = $_SESSION['flag_products_measure'];
    $resolution = null;

    if ($resolution == null) {
        $resolution = $generalProductsMaterialsDao->deleteProductMaterialByProduct($dataProduct['idProduct']);
        $resolution = $generalCompositeProductsDao->deleteCompositeProductByProduct($dataProduct['idProduct']);
        $resolution = $generalCompositeProductsDao->deleteChildProductByProduct($dataProduct['idProduct']);
        $resolution = $generalPlanCiclesMachinesDao->deletePlanCiclesMachineByProduct($dataProduct['idProduct']);
        $resolution = $generalProductsPlansDao->deleteProductPlanByProduct($dataProduct['idProduct']);
        $resolution = $generalPStockDao->deleteStockByProduct($dataProduct['idProduct']);
        $resolution = $generalUnitSalesDao->deleteSaleByProduct($dataProduct['idProduct']);
        $resolution = $generalOrdersDao->deleteOrderByProduct($dataProduct['idProduct']);
        $resolution = $generalProgrammingDao->deleteProgrammingByProduct($dataProduct['idProduct']);
        $resolution = $generalProgrammingRoutesDao->deleteProgrammingRouteByProduct($dataProduct['idProduct']);
    }

    if ($resolution == null) {
        if ($flag_products_measure == '1') {
            $resolution = $productsMeasuresDao->deletePMeasure($dataProduct['idProductMeasure']);
            if ($resolution == null)
                $resolution = $productsDao->deleteProduct($dataProduct['idProduct']);
            if ($resolution == null)
                $resolution = $generalProductsDao->deleteProductInventoryByProduct($dataProduct['idProduct']);
        } else {
            $resolution = $productsDao->deleteProduct($dataProduct['idProduct']);
            if ($resolution == null)
                $resolution = $generalProductsDao->deleteProductInventoryByProduct($dataProduct['idProduct']);
        }
    }


    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Producto eliminado correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras eliminaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/copyProduct', function (Request $request, Response $response, $args) use (
    $productsMeasuresDao,
    $productsDao,
    $generalProductsDao,
    $lastDataDao,
    $productsInventoryDao,
    $productMaterialDao,
    $compositeProductsDao,
    $planCiclesMachineDao,
    $generalPlanCiclesMachinesDao,
    $productsPlansDao,
    $generalProductsPlansDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag_products_measure = $_SESSION['flag_products_measure'];
    $dataProduct = $request->getParsedBody();

    $product = $generalProductsDao->findProductByReferenceOrName($dataProduct, $id_company);

    if (!$product) {
        $resolution = $productsDao->insertProductByCompany($dataProduct, $id_company);

        if ($resolution == null) {
            $lastData = $lastDataDao->lastInsertedProductId($id_company);
            $dataProduct['idProduct'] = $lastData['id_product'];
        }

        // Inventario
        if ($resolution == null) {
            $invProduct = $generalProductsDao->findProductInventory($dataProduct['idOldProduct'], $id_company);

            $dataProduct['quantity'] = $invProduct['quantity'];
            $dataProduct['accumulated_quantity'] = $invProduct['accumulated_quantity'];
            $dataProduct['classification'] = $invProduct['classification'];
            $dataProduct['reserved'] = $invProduct['reserved'];
            $dataProduct['minimum_stock'] = $invProduct['minimum_stock'];
            $dataProduct['days'] = $invProduct['days'];

            $resolution = $productsInventoryDao->insertCopyProductsInventory($dataProduct, $id_company);
        }

        // Medidas
        if ($resolution == null && $flag_products_measure == '1')
            $resolution = $productsMeasuresDao->insertPMeasureByCompany($dataProduct, $id_company);

        // Ficha Tecnica Materiales
        if ($resolution == null) {
            $findFTPM = $productMaterialDao->findAllProductsMaterials($dataProduct['idOldProduct'], $id_company);

            foreach ($findFTPM as $arr) {
                $arr['material'] = $arr['id_material'];
                $arr['unit'] = $arr['id_unit'];
                $arr['idProduct'] = $dataProduct['idProduct'];

                $resolution = $productMaterialDao->insertProductsMaterialsByCompany($arr, $id_company);

                if (isset($resolution['info'])) break;
            }
        }

        // Productos Compuestos
        if ($resolution == null) {
            $findFTPCP = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataProduct['idOldProduct'], $id_company);

            foreach ($findFTPCP as $arr) {
                $arr['idProduct'] = $dataProduct['idProduct'];
                $arr['compositeProduct'] = $arr['id_child_product'];
                $arr['unit'] = $arr['id_unit'];

                $resolution = $compositeProductsDao->insertCompositeProductByCompany($arr, $id_company);

                if (isset($resolution['info'])) break;
            }
        }

        // Ficha Tecnica Ciclos Maquinas
        if ($resolution == null) {
            $findFTPC = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($dataProduct['idOldProduct'], $id_company);

            foreach ($findFTPC as $arr) {
                $arr['idProduct'] = $dataProduct['idProduct'];
                $arr['idProcess'] = $arr['id_process'];
                $arr['idMachine'] = $arr['id_machine'];
                $arr['ciclesHour'] = $arr['cicles_hour'];

                $resolution = $planCiclesMachineDao->addPlanCiclesMachines($arr, $id_company);

                if (isset($resolution['info'])) break;
            }
        }

        // Ficha Tecnica Planos
        if ($resolution == null && $flag_products_measure == '1') {
            $findFTPP = $generalProductsPlansDao->findProductPlans($dataProduct['idOldProduct']);

            $data = [];

            $data['idProduct'] = $dataProduct['idProduct'];
            $data['mechanicalFile'] = $findFTPP['mechanical_plan'];
            $data['assemblyFile'] = $findFTPP['assembly_plan'];

            $resolution = $productsPlansDao->insertProductPlanByCompany($data, $id_company);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Producto clonado correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras guardaba los datos. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El producto ya existe en la base de datos. Ingrese uno nuevo');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

/* Cambiar Producto Compuesto */
$app->get('/changeComposite/{id_product}/{op}', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $generalCompositeProductsDao
) {
    $status = true;

    if ($args['op'] == 0) {
        $product = $generalCompositeProductsDao->findCompositeProductByChild($args['id_product']);
        if (sizeof($product) > 0)
            $status = false;
    }

    if ($status == true) {
        $product = $generalProductsDao->changeCompositeProducts($args['id_product'], $args['op']);

        if ($product == null)
            $resp = array('success' => true, 'message' => 'Producto modificado correctamente');
        else if (isset($product['info']))
            $resp = array('info' => true, 'message' => $product['message']);
        else
            $resp = array('error' => true, 'message' => 'No se pudo modificar la información. Intente de nuevo');
    } else
        $resp = array(
            'error' => true,
            'message' => 'No se pudo desactivar el producto. Tiene datos relacionados a él'
        );

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
