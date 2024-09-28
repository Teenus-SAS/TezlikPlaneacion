<?php

use TezlikPlaneacion\dao\ClientsDao;
use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\Dao\ConversionUnitsDao;
use TezlikPlaneacion\dao\ConvertDataDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\ExplosionProductsdao;
use TezlikPlaneacion\dao\FilterDataDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\Dao\GeneralCompositeProductsDao;
use TezlikPlaneacion\dao\GeneralExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralExplosionProductsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsMaterialsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\GeneralSellersDao;
use TezlikPlaneacion\dao\InventoryDaysDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\LicenseCompanyDao;
use TezlikPlaneacion\Dao\MagnitudesDao;
use TezlikPlaneacion\dao\MaterialsTypeDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\RequisitionsDao;
use TezlikPlaneacion\dao\UnitsDao;

$productsMaterialsDao = new ProductsMaterialsDao();
$licenseDao = new LicenseCompanyDao();
$clientsDao = new ClientsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$generalProductsMaterialsDao = new GeneralProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$generalProductsDao = new GeneralProductsDao();
$convertDataDao = new ConvertDataDao();
$productsDao = new GeneralProductsDao();
$materialsDao = new GeneralMaterialsDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$inventoryDaysDao = new InventoryDaysDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$materialsTypeDao = new MaterialsTypeDao();
$minimumStockDao = new MinimumStockDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$filterDataDao = new FilterDataDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$explosionProductsDao = new ExplosionProductsdao();
$generalExMaterialsDao = new GeneralExplosionMaterialsDao();
$generalExProductsDao = new GeneralExplosionProductsDao();
$generalRMStockDao = new GeneralRMStockDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$requisitionsDao = new RequisitionsDao();
$generalClientsDao = new GeneralClientsDao();
$generalSellersDao = new GeneralSellersDao();
$ordersDao = new OrdersDao();
$lastDataDao = new LastDataDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/productsMaterials/{idProduct}', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productMaterials = $productsMaterialsDao->findAllProductsMaterials($args['idProduct'], $id_company);

    $response->getBody()->write(json_encode($productMaterials, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/allProductsMaterials', function (Request $request, Response $response, $args) use ($generalProductsMaterialsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productMaterials = $generalProductsMaterialsDao->findAllProductsMaterials($id_company);

    $response->getBody()->write(json_encode($productMaterials));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/productsMaterialsDataValidation', function (Request $request, Response $response, $args) use (
    $generalProductsMaterialsDao,
    $productsDao,
    $generalCompositeProductsDao,
    $materialsDao,
    $unitsDao,
    $materialsTypeDao,
    $magnitudesDao
) {
    $dataProductMaterial = $request->getParsedBody();

    if (isset($dataProductMaterial)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $productMaterials = $dataProductMaterial['importProducts'];
        $debugg = [];
        $dataImportProductsMaterials = [];

        for ($i = 0; $i < sizeof($productMaterials); $i++) {
            if (
                empty($productMaterials[$i]['referenceProduct']) || empty($productMaterials[$i]['product']) || empty($productMaterials[$i]['refRawMaterial']) ||
                empty($productMaterials[$i]['nameRawMaterial']) || $productMaterials[$i]['quantity'] == '' || empty($productMaterials[$i]['type'])
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Columna vacia"));
            }
            if (
                empty(trim($productMaterials[$i]['referenceProduct'])) || empty(trim($productMaterials[$i]['product'])) || empty(trim($productMaterials[$i]['refRawMaterial'])) ||
                empty(trim($productMaterials[$i]['nameRawMaterial'])) || trim($productMaterials[$i]['quantity']) == '' || empty($productMaterials[$i]['type'])
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Columna vacia"));
            }

            if ($_SESSION['flag_products_measure'] == '1') {
                if (empty($productMaterials[$i]['materialType']) || empty(trim($productMaterials[$i]['materialType']))) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Columna vacia en la fila: $row"));
                }
            }

            $quantity = str_replace(',', '.', $productMaterials[$i]['quantity']);

            $quantity = 1 * $quantity;

            if ($quantity <= 0 || is_nan($quantity)) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila: $row: La cantidad debe ser mayor a cero (0)"));
            }

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($productMaterials[$i]);

            if (!$magnitude) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Magnitud no Existe."));
            } else {
                $productMaterials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

                // Consultar unidad
                $unit = $unitsDao->findUnit($productMaterials[$i]);

                if (!$unit) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Fila-$row: Unidad no Existe."));
                }
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            if (!$findProduct) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Producto no Existe"));
            } else $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            $type = $productMaterials[$i]['type'];

            if ($type == 'MATERIAL') {
                // Obtener id materia prima
                $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
                if (!$findMaterial) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Fila-$row: Materia prima no Existe"));
                } else $productMaterials[$i]['material'] = $findMaterial['id_material'];

                if ($_SESSION['flag_products_measure'] == '1') {
                    // Consultar tipo material
                    $materialType = $materialsTypeDao->findMaterialsType($productMaterials[$i], $id_company);

                    if (!$materialType) {
                        $row = $i + 2;
                        array_push($debugg, array('error' => true, 'message' => "Tipo de material no existe en la base de datos. Fila: $row"));
                    }
                }

                if (sizeof($debugg) == 0) {
                    $findProductsMaterials = $generalProductsMaterialsDao->findProductMaterial($productMaterials[$i], $id_company);
                    if (!$findProductsMaterials) $insert = $insert + 1;
                    else $update = $update + 1;
                }
            } else if ($type == 'PRODUCTO') {
                // Obtener id productos compuestos
                $arr = [];
                $arr['referenceProduct'] = $productMaterials[$i]['refRawMaterial'];
                $arr['product'] = $productMaterials[$i]['nameRawMaterial'];

                $findProduct = $productsDao->findProduct($arr, $id_company);
                if (!$findProduct) {
                    $row = $i + 2;
                    array_push($debugg, array('error' => true, 'message' => "Fila-$row: Producto no existe."));
                }

                if (sizeof($debugg) == 0) {
                    if ($findProduct['composite'] == 0) {
                        $row = $i + 2;
                        array_push($debugg, array('error' => true, 'message' => "Fila-$row: Producto no está definido como compuesto."));
                    }

                    $productMaterials[$i]['compositeProduct'] = $findProduct['id_product'];

                    $findComposite = $generalCompositeProductsDao->findCompositeProduct($productMaterials[$i]);
                    if (!$findComposite) $insert += 1;
                    else $update += 1;
                }
            }

            if (sizeof($debugg) == 0) {
                $dataImportProductsMaterials['insert'] = $insert;
                $dataImportProductsMaterials['update'] = $update;
            }
        }
    } else
        $dataImportProductsMaterials = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportProductsMaterials;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductsMaterials', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao,
    $generalProductsMaterialsDao,
    $compositeProductsDao,
    $generalCompositeProductsDao,
    $generalProductsDao,
    $generalPlanCiclesMachinesDao,
    $conversionUnitsDao,
    $explosionMaterialsDao,
    $clientsDao,
    $licenseDao,
    $explosionProductsDao,
    $generalExMaterialsDao,
    $generalExProductsDao,
    $inventoryDaysDao,
    $generalRMStockDao,
    $generalRequisitionsDao,
    $requisitionsDao,
    $productsDao,
    $materialsDao,
    $materialsTypeDao,
    $generalClientsDao,
    $generalSellersDao,
    $ordersDao,
    $lastDataDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $magnitudesDao,
    $unitsDao,
    $generalOrdersDao,
    $generalProgrammingDao,
    $minimumStockDao,
    $generalMaterialsDao,
    $filterDataDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $flag_products_measure = $_SESSION['flag_products_measure'];

    $dataProductMaterial = $request->getParsedBody();
    $resolution = null;
    $dataProductMaterials = sizeof($dataProductMaterial);

    if ($dataProductMaterials > 1) {

        $productMaterials = $generalProductsMaterialsDao->findProductMaterial($dataProductMaterial, $id_company);
        if (!$productMaterials) {
            $resolution = $productsMaterialsDao->insertProductsMaterialsByCompany($dataProductMaterial, $id_company);

            // Estado producto
            if ($resolution == null)
                $resolution = $generalProductsDao->updateStatusByProduct($dataProductMaterial['idProduct'], 1);

            if ($resolution == null) {
                // Consultar todos los datos del producto
                $products = $productsMaterialsDao->findAllProductsMaterials($dataProductMaterial['idProduct'], $id_company);

                foreach ($products as $arr) {
                    // Calculo Dias Inventario Materiales  
                    $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);
                    if (isset($inventory['days']))
                        $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $inventory['days']);

                    if (isset($resolution['info'])) break;

                    // Obtener materia prima
                    $material = $generalMaterialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                    // Convertir unidades
                    $quantity = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                    // Guardar Unidad convertida
                    $generalProductsMaterialsDao->saveQuantityConverted($arr['id_product_material'], $quantity);

                    $k = $minimumStockDao->calcStockByMaterial($arr['id_material']);

                    if (isset($arr['stock']))
                        $resolution = $generalMaterialsDao->updateStockMaterial($arr['id_material'], $k['stock']);
                }

                // if ($resolution == null) {
                //     $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataProductMaterial['idProduct'], $id_company);

                //     foreach ($compositeProducts as $k) {
                //         $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

                //         $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

                //         if (isset($arr['stock']) && isset($product['stock'])) {
                //             $stock = $product['stock'] + $arr['stock'];

                //             $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
                //         }
                //     }
                // }
            }

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Materia prima asignada correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras asignaba la información. Intente nuevamente');
        } else
            $resp = array('info' => true, 'message' => 'El material ya existe. Ingrese nuevo material');
    } else {
        $productMaterials = $dataProductMaterial['importProducts'];

        $resolution = 1;

        for ($i = 0; $i < sizeof($productMaterials); $i++) {
            if (isset($resolution['info'])) break;
            // Obtener id producto
            $findProduct = $productsDao->findProduct($productMaterials[$i], $id_company);
            $productMaterials[$i]['idProduct'] = $findProduct['id_product'];

            // Consultar magnitud
            $magnitude = $magnitudesDao->findMagnitude($productMaterials[$i]);
            $productMaterials[$i]['idMagnitude'] = $magnitude['id_magnitude'];

            // Consultar unidad
            $unit = $unitsDao->findUnit($productMaterials[$i]);
            $productMaterials[$i]['unit'] = $unit['id_unit'];

            $type = $productMaterials[$i]['type'];

            switch ($type) {
                case 'MATERIAL':
                    // Obtener id materia prima
                    $findMaterial = $materialsDao->findMaterial($productMaterials[$i], $id_company);
                    $productMaterials[$i]['material'] = $findMaterial['id_material'];

                    $findProductsMaterials = $generalProductsMaterialsDao->findProductMaterial($productMaterials[$i]);

                    if ($flag_products_measure == '1') {
                        $dataMaterial = $generalMaterialsDao->findMaterialById($productMaterials[$i]['material']);
                        $dataProduct = $generalProductsDao->findProductById($productMaterials[$i]['idProduct']);
                        $weight = 0;

                        if ($productMaterials[$i]['materialType'] == 'PAPEL') {
                            $weight = ((floatval($dataMaterial['grammage']) * floatval($dataProduct['length']) * floatval($dataProduct['total_width'])) / 10000000) / floatval($dataProduct['window']);
                        } else {
                            $quantity = floatval($productMaterials[$i]['quantity']);
                            $quantityFTM = 0;

                            $arr = [];
                            $arr['idProduct'] = $productMaterials[$i]['idProduct'];

                            $materialType = $materialsTypeDao->findMaterialsType($productMaterials[$i], $id_company);
                            $arr['type'] = $materialType['id_material_type'];

                            $dataFTM = $generalProductsMaterialsDao->findProductsMaterialsByCompany($arr, $id_company);

                            if ($dataFTM) $quantityFTM = $dataFTM['quantity'];

                            $weight = $quantity * $quantityFTM;
                        }

                        $productMaterials[$i]['quantity'] = $weight;
                    }

                    if (!$findProductsMaterials) $resolution = $productsMaterialsDao->insertProductsMaterialsByCompany($productMaterials[$i], $id_company);
                    else {
                        $productMaterials[$i]['idProductMaterial'] = $findProductsMaterials['id_product_material'];
                        $resolution = $productsMaterialsDao->updateProductsMaterials($productMaterials[$i]);
                    }
                    break;

                case 'PRODUCTO':
                    // Obtener id productos compuestos
                    $arr = [];
                    $arr['referenceProduct'] = $productMaterials[$i]['refRawMaterial'];
                    $arr['product'] = $productMaterials[$i]['nameRawMaterial'];

                    $findProduct = $productsDao->findProduct($arr, $id_company);
                    $productMaterials[$i]['compositeProduct'] = $findProduct['id_product'];

                    $findComposite = $generalCompositeProductsDao->findCompositeProduct($productMaterials[$i]);

                    if (!$findComposite) {
                        $resolution = $compositeProductsDao->insertCompositeProductByCompany($productMaterials[$i], $id_company);
                    } else {
                        $productMaterials[$i]['idCompositeProduct'] = $findComposite['id_composite_product'];
                        $resolution = $compositeProductsDao->updateCompositeProduct($productMaterials[$i]);
                    }
                    break;
            };

            if (isset($resolution['info'])) break;

            // Consultar todos los datos del producto
            $products = $productsMaterialsDao->findAllProductsMaterials($productMaterials[$i]['idProduct'], $id_company);

            foreach ($products as $arr) {
                // Calculo Dias Inventario Materiales  
                $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);
                if (isset($inventory['days']))
                    $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $inventory['days']);

                if (isset($resolution['info'])) break;
                // Obtener materia prima
                $material = $generalMaterialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                // Convertir unidades
                $quantity = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                // Guardar Unidad convertida
                $generalProductsMaterialsDao->saveQuantityConverted($arr['id_product_material'], $quantity);

                $k = $minimumStockDao->calcStockByMaterial($arr['id_material']);

                if (isset($k['stock']))
                    $resolution = $generalMaterialsDao->updateStockMaterial($arr['id_material'], $k['stock']);
            }

            if (isset($resolution['info'])) break;

            // $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($productMaterials[$i]['idProduct'], $id_company);

            // foreach ($compositeProducts as $k) {
            //     $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

            //     $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

            //     if (isset($arr['stock']) && isset($product['stock'])) {
            //         $stock = $product['stock'] + $arr['stock'];

            //         $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
            //     }
            // }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Ficha Técnica Materia Prima importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);

        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importada la información. Intente nuevamente');
    }

    // $products = $productsDao->updateAccumulatedQuantityGeneral($id_company);

    // // Cambiar estado pedidos 
    // $allOrders = $generalOrdersDao->findAllOrdersWithMaterialsByCompany($id_company);

    // foreach ($allOrders as $arr) {
    //     $status = true;
    //     if ($arr['original_quantity'] > $arr['accumulated_quantity']) {
    //         if ($arr['status_ds'] == 0) {
    //             $generalOrdersDao->changeStatus($arr['id_order'], 5);
    //             $status = false;
    //             // break;
    //         } else if ($arr['quantity_material'] <= 0) {
    //             $generalOrdersDao->changeStatus($arr['id_order'], 6);
    //             $status = false;
    //             // break;
    //         }
    //     }

    //     foreach ($allOrders as &$order) {
    //         if ((!isset($arr['status_mp']) || $arr['status_mp'] === false) && $order['id_order'] == $arr['id_order']) {
    //             // if ($order['id_order'] == $arr['id_order']) {
    //             $order['status_mp'] = $status;
    //         }
    //     }
    //     unset($order);

    //     if ($status == true && $arr['programming'] != 0) {
    //         $generalOrdersDao->changeStatus($arr['id_order'], 4);

    //         $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
    //         !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
    //         $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
    //     }
    // }

    // $orders = $filterDataDao->filterDuplicateArray($allOrders, 'id_order');

    // for ($i = 0; $i < sizeof($orders); $i++) {
    //     if ($orders[$i]['status_mp'] == true) {
    //         if ($orders[$i]['original_quantity'] <= $orders[$i]['accumulated_quantity']) {
    //             $generalOrdersDao->changeStatus(
    //                 $orders[$i]['id_order'],
    //                 2
    //             );
    //             $accumulated_quantity = $orders[$i]['accumulated_quantity'] - $orders[$i]['original_quantity'];
    //         } else {
    //             $accumulated_quantity = $orders[$i]['accumulated_quantity'];
    //         }

    //         if ($orders[$i]['status'] != 2) {
    //             $date = date('Y-m-d');

    //             $generalOrdersDao->updateOfficeDate($orders[$i]['id_order'], $date);
    //         }

    //         $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
    //         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
    //         $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

    //         $arr = $generalMaterialsDao->findReservedMaterial($orders[$i]['id_product']);
    //         !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
    //         $generalMaterialsDao->updateReservedMaterial($orders[$i]['id_product'], $arr['reserved']);

    //         $generalMaterialsDao->updateQuantityMaterial($orders[$i]['id_product'], $accumulated_quantity, 1);
    //     }
    // }
    if ($resolution == null) {
        $cicle = true;

        while ($cicle == true) {
            $cicle = false;
            // Productos
            $arr = $generalExProductsDao->findAllCompositeConsolidated($id_company);
            $products = $generalExProductsDao->setDataEXComposite($arr);

            for ($i = 0; $i < sizeof($products); $i++) {
                $findEX = $generalExProductsDao->findEXProduct($products[$i]['id_child_product']);

                if (!$findEX)
                    $resolution = $explosionProductsDao->insertNewEXPByCompany($products[$i], $id_company);
                else {
                    $products[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                    $resolution = $explosionProductsDao->updateEXProduct($products[$i]);
                }

                if (intval($products[$i]['available']) < 0 && abs($products[$i]['available']) > $products[$i]['quantity_material']) {
                    $data = [];
                    $arr2 = $generalOrdersDao->findLastOrderByNumOrder($products[$i]['num_order']);

                    $seller = $generalSellersDao->findInternalSeller($id_company);

                    if ($seller) {
                        $client = $generalClientsDao->findInternalClient($id_company);

                        if (!$client) {
                            $company = $licenseDao->findLicenseCompany($id_company);
                            $dataClient = [];

                            $dataClient['nit'] = $company['nit'];
                            $dataClient['client'] = $company['company'];
                            $dataClient['address'] = $company['address'];
                            $dataClient['phone'] = $company['telephone'];
                            $dataClient['city'] = $company['city'];
                            $dataClient['type'] = 1;

                            $resolution = $clientsDao->insertClient($dataClient, $id_company);

                            $client = $lastDataDao->findLastInsertedClient();

                            $resolution = $generalClientsDao->changeStatusClient($client['id_client'], 1);
                        }

                        $data['order'] = $arr2['num_order'];
                        $data['dateOrder'] = date('Y-m-d');
                        $data['minDate'] = '';
                        $data['maxDate'] = '';
                        $data['idProduct'] = $products[$i]['id_child_product'];
                        $data['idClient'] = $client['id_client'];
                        $data['idSeller'] = $seller['id_seller'];
                        $data['route'] = 1;
                        $data['originalQuantity'] = abs($products[$i]['available']);
                        $data['typeOrder'] = 2;

                        $findOrder = $generalOrdersDao->findLastSameOrder($data);
                        if (!$findOrder) {
                            $cicle = true;
                            $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                            // $generalProductsDao->updateAccumulatedQuantity($products[$i]['id_child_product'], abs($products[$i]['available']), 2);

                            if (isset($resolution['info'])) break;
                            $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                            $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($products[$i]['id_child_product'], $lastOrder['id_order']);

                            if (!$programmingRoutes) {
                                $data['idOrder'] = $lastOrder['id_order'];
                                $data['route'] = 1;

                                $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                            }
                        } else {
                            $data['idOrder'] = $findOrder['id_order'];
                            $resolution = $ordersDao->updateOrder($data);
                        }
                        if (isset($resolution['info'])) break;
                    }
                }
            }

            $arr = $generalExProductsDao->findAllChildrenCompositeConsolidaded($id_company);
            $cProducts = $generalExProductsDao->setDataEXComposite($arr);

            for ($i = 0; $i < sizeof($cProducts); $i++) {
                $findEX = $generalExProductsDao->findEXProduct($cProducts[$i]['id_child_product']);

                if (!$findEX)
                    $resolution = $explosionProductsDao->insertNewEXPByCompany($cProducts[$i], $id_company);
                else {
                    $cProducts[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                    $resolution = $explosionProductsDao->updateEXProduct($cProducts[$i]);
                }

                if (intval($cProducts[$i]['available']) < 0 && abs($cProducts[$i]['available']) > $cProducts[$i]['quantity_material']) {
                    $data = [];
                    $arr2 = $generalOrdersDao->findLastOrderByNumOrder($cProducts[$i]['num_order']);

                    $seller = $generalSellersDao->findInternalSeller($id_company);

                    if ($seller) {
                        $client = $generalClientsDao->findInternalClient($id_company);

                        if (!$client) {
                            $company = $licenseDao->findLicenseCompany($id_company);
                            $dataClient = [];

                            $dataClient['nit'] = $company['nit'];
                            $dataClient['client'] = $company['company'];
                            $dataClient['address'] = $company['address'];
                            $dataClient['phone'] = $company['telephone'];
                            $dataClient['city'] = $company['city'];
                            $dataClient['type'] = 1;

                            $resolution = $clientsDao->insertClient($dataClient, $id_company);

                            $client = $lastDataDao->findLastInsertedClient();

                            $resolution = $generalClientsDao->changeStatusClient($client['id_client'], 1);
                        }

                        $data['order'] = $arr2['num_order'];
                        $data['dateOrder'] = date('Y-m-d');
                        $data['minDate'] = '';
                        $data['maxDate'] = '';
                        $data['idProduct'] = $cProducts[$i]['id_child_product'];
                        $data['idClient'] = $client['id_client'];
                        $data['idSeller'] = $seller['id_seller'];
                        $data['route'] = 1;
                        $data['originalQuantity'] = abs($cProducts[$i]['available']);
                        $data['typeOrder'] = 2;

                        $findOrder = $generalOrdersDao->findLastSameOrder($data);
                        if (!$findOrder) {
                            $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                            if (isset($resolution['info'])) break;
                            $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                            $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($cProducts[$i]['id_child_product'], $lastOrder['id_order']);

                            if (!$programmingRoutes) {
                                $data['idOrder'] = $lastOrder['id_order'];
                                $data['route'] = 1;

                                $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                            }
                        } else {
                            $data['idOrder'] = $findOrder['id_order'];
                            $resolution = $ordersDao->updateOrder($data);
                        }
                        if (isset($resolution['info'])) break;
                    }
                }
            }

            // Materiales
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

                    if ($provider) $id_provider = $provider['id_provider'];

                    $data['idProvider'] = $id_provider;
                    $data['numOrder'] = $materials[$i]['num_order'];
                    $data['applicationDate'] = '';
                    $data['deliveryDate'] = '';
                    $data['requiredQuantity'] = abs($materials[$i]['available']);
                    $data['purchaseOrder'] = '';
                    $data['requestedQuantity'] = 0;

                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                    if (!$requisition)
                        $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
                    else {
                        $data['idRequisition'] = $requisition['id_requisition'];
                        $generalRequisitionsDao->updateRequisitionAuto($data);
                    }
                }
            }

            $arr = $generalExMaterialsDao->findAllChildrenMaterialsConsolidaded($id_company);
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

                    if ($provider) $id_provider = $provider['id_provider'];

                    $data['idProvider'] = $id_provider;
                    $data['numOrder'] = $materials[$i]['num_order'];
                    $data['applicationDate'] = '';
                    $data['deliveryDate'] = '';
                    $data['requiredQuantity'] = abs($materials[$i]['available']);
                    $data['purchaseOrder'] = '';
                    $data['requestedQuantity'] = 0;

                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                    if (!$requisition)
                        $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
                    else {
                        $data['idRequisition'] = $requisition['id_requisition'];
                        $generalRequisitionsDao->updateRequisitionAuto($data);
                    }
                }
            }
        }
    }

    if ($resolution == null) {
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Checkear cantidades
            // $order = $generalOrdersDao->checkAccumulatedQuantityOrder($orders[$i]['id_order']);
            // Ficha tecnica
            $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
            $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
            $productsFTM = array_merge($productsMaterials, $compositeProducts);

            $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

            if ($orders[$i]['status'] != 'EN PRODUCCION' && /* $orders[$i]['status'] != 'PROGRAMADO' &&*/ $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO') {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
                    if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 5);
                        $status = false;
                    } else {
                        foreach ($planCicles as $arr) {
                            // Verificar Maquina Disponible
                            if ($arr['status'] == 0) {
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
                        // Verificar Material
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

                    $arr = $productsDao->findProductReserved($orders[$i]['id_product']);
                    !isset($arr['reserved']) ? $arr['reserved'] = 0 : $arr;
                    $productsDao->updateReservedByProduct($orders[$i]['id_product'], $arr['reserved']);

                    $productsDao->updateAccumulatedQuantity($orders[$i]['id_product'], $accumulated_quantity, 1);
                    $programming = $generalProgrammingDao->findProgrammingByOrder($orders[$i]['id_order']);
                    if (sizeof($programming) > 0) {
                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], 4);
                    }
                }
                foreach ($productsMaterials as $arr) {
                    $k = $generalMaterialsDao->findReservedMaterial($arr['id_material']);
                    !isset($k['reserved']) ? $k['reserved'] = 0 : $k;
                    $generalMaterialsDao->updateReservedMaterial($arr['id_material'], $k['reserved']);
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

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanProductsMaterials', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao,
    $conversionUnitsDao,
    $explosionMaterialsDao,
    $explosionProductsDao,
    $generalExMaterialsDao,
    $generalExProductsDao,
    $inventoryDaysDao,
    $generalRMStockDao,
    $licenseDao,
    $clientsDao,
    $compositeProductsDao,
    $generalRequisitionsDao,
    $requisitionsDao,
    $generalClientsDao,
    $generalSellersDao,
    $ordersDao,
    $generalOrdersDao,
    $generalProductsDao,
    $lastDataDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $generalProductsMaterialsDao,
    $generalMaterialsDao,
    $minimumStockDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();

    $productMaterials = $generalProductsMaterialsDao->findProductMaterial($dataProductMaterial, $id_company);
    !is_array($productMaterials) ? $data['id_productMaterial'] = 0 : $data = $productMaterials;

    if ($data['id_product_material'] == $dataProductMaterial['idProductMaterial'] || $data['id_productMaterial'] == 0) {
        // $dataProductMaterial = $convertDataDao->strReplaceProductsMaterials($dataProductMaterial);
        $resolution = $productsMaterialsDao->updateProductsMaterials($dataProductMaterial);

        if ($resolution == null) {
            // Consultar todos los datos del producto
            $products = $productsMaterialsDao->findAllProductsMaterials($dataProductMaterial['idProduct'], $id_company);

            foreach ($products as $arr) {
                // Calculo Dias Inventario Materiales  
                $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);
                if (isset($inventory['days']))
                    $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $inventory['days']);

                if (isset($resolution['info'])) break;
                // Obtener materia prima
                $material = $generalMaterialsDao->findMaterialAndUnits($arr['id_material'], $id_company);

                // Convertir unidades
                $quantity = $conversionUnitsDao->convertUnits($material, $arr, $arr['quantity']);

                // Guardar Unidad convertida
                $generalProductsMaterialsDao->saveQuantityConverted($arr['id_product_material'], $quantity);

                $k = $minimumStockDao->calcStockByMaterial($arr['id_material']);

                if (isset($k['stock']))
                    $resolution = $generalMaterialsDao->updateStockMaterial($arr['id_material'], $k['stock']);
            }

            // if ($resolution == null) {
            //     $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataProductMaterial['idProduct'], $id_company);

            //     foreach ($compositeProducts as $k) {
            //         $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

            //         $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

            //         if (isset($arr['stock']) && isset($product['stock'])) {
            //             $stock = $product['stock'] + $arr['stock'];

            //             $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
            //         }
            //     }
            // }
        }

        if ($resolution == null) {
            $cicle = true;

            while ($cicle == true) {
                $cicle = false;
                // Productos
                $arr = $generalExProductsDao->findAllCompositeConsolidated($id_company);
                $products = $generalExProductsDao->setDataEXComposite($arr);

                for ($i = 0; $i < sizeof($products); $i++) {
                    $findEX = $generalExProductsDao->findEXProduct($products[$i]['id_child_product']);

                    if (!$findEX)
                        $resolution = $explosionProductsDao->insertNewEXPByCompany($products[$i], $id_company);
                    else {
                        $products[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                        $resolution = $explosionProductsDao->updateEXProduct($products[$i]);
                    }

                    if (intval($products[$i]['available']) < 0 && abs($products[$i]['available']) > $products[$i]['quantity_material']) {
                        $data = [];
                        $arr2 = $generalOrdersDao->findLastOrderByNumOrder($products[$i]['num_order']);

                        $seller = $generalSellersDao->findInternalSeller($id_company);

                        if ($seller) {
                            $client = $generalClientsDao->findInternalClient($id_company);

                            if (!$client) {
                                $company = $licenseDao->findLicenseCompany($id_company);
                                $dataClient = [];

                                $dataClient['nit'] = $company['nit'];
                                $dataClient['client'] = $company['company'];
                                $dataClient['address'] = $company['address'];
                                $dataClient['phone'] = $company['telephone'];
                                $dataClient['city'] = $company['city'];
                                $dataClient['type'] = 1;

                                $resolution = $clientsDao->insertClient($dataClient, $id_company);

                                $client = $lastDataDao->findLastInsertedClient();

                                $resolution = $generalClientsDao->changeStatusClient($client['id_client'], 1);
                            }

                            $data['order'] = $arr2['num_order'];
                            $data['dateOrder'] = date('Y-m-d');
                            $data['minDate'] = '';
                            $data['maxDate'] = '';
                            $data['idProduct'] = $products[$i]['id_child_product'];
                            $data['idClient'] = $client['id_client'];
                            $data['idSeller'] = $seller['id_seller'];
                            $data['route'] = 1;
                            $data['originalQuantity'] = abs($products[$i]['available']);
                            $data['typeOrder'] = 2;

                            $findOrder = $generalOrdersDao->findLastSameOrder($data);
                            if (!$findOrder) {
                                $cicle = true;
                                $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                                // $generalProductsDao->updateAccumulatedQuantity($products[$i]['id_child_product'], abs($products[$i]['available']), 2);

                                if (isset($resolution['info'])) break;
                                $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                                $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($products[$i]['id_child_product'], $lastOrder['id_order']);

                                if (!$programmingRoutes) {
                                    $data['idOrder'] = $lastOrder['id_order'];
                                    $data['route'] = 1;

                                    $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                                }
                            } else {
                                $data['idOrder'] = $findOrder['id_order'];
                                $resolution = $ordersDao->updateOrder($data);
                            }
                            if (isset($resolution['info'])) break;
                        }
                    }
                }

                $arr = $generalExProductsDao->findAllChildrenCompositeConsolidaded($id_company);
                $cProducts = $generalExProductsDao->setDataEXComposite($arr);

                for ($i = 0; $i < sizeof($cProducts); $i++) {
                    $findEX = $generalExProductsDao->findEXProduct($cProducts[$i]['id_child_product']);

                    if (!$findEX)
                        $resolution = $explosionProductsDao->insertNewEXPByCompany($cProducts[$i], $id_company);
                    else {
                        $cProducts[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                        $resolution = $explosionProductsDao->updateEXProduct($cProducts[$i]);
                    }

                    if (intval($cProducts[$i]['available']) < 0 && abs($cProducts[$i]['available']) > $cProducts[$i]['quantity_material']) {
                        $data = [];
                        $arr2 = $generalOrdersDao->findLastOrderByNumOrder($cProducts[$i]['num_order']);

                        $seller = $generalSellersDao->findInternalSeller($id_company);

                        if ($seller) {
                            $client = $generalClientsDao->findInternalClient($id_company);

                            if (!$client) {
                                $company = $licenseDao->findLicenseCompany($id_company);
                                $dataClient = [];

                                $dataClient['nit'] = $company['nit'];
                                $dataClient['client'] = $company['company'];
                                $dataClient['address'] = $company['address'];
                                $dataClient['phone'] = $company['telephone'];
                                $dataClient['city'] = $company['city'];
                                $dataClient['type'] = 1;

                                $resolution = $clientsDao->insertClient($dataClient, $id_company);

                                $client = $lastDataDao->findLastInsertedClient();

                                $resolution = $generalClientsDao->changeStatusClient($client['id_client'], 1);
                            }

                            $data['order'] = $arr2['num_order'];
                            $data['dateOrder'] = date('Y-m-d');
                            $data['minDate'] = '';
                            $data['maxDate'] = '';
                            $data['idProduct'] = $cProducts[$i]['id_child_product'];
                            $data['idClient'] = $client['id_client'];
                            $data['idSeller'] = $seller['id_seller'];
                            $data['route'] = 1;
                            $data['originalQuantity'] = abs($cProducts[$i]['available']);
                            $data['typeOrder'] = 2;

                            $findOrder = $generalOrdersDao->findLastSameOrder($data);
                            if (!$findOrder) {
                                $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                                if (isset($resolution['info'])) break;
                                $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                                $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($cProducts[$i]['id_child_product'], $lastOrder['id_order']);

                                if (!$programmingRoutes) {
                                    $data['idOrder'] = $lastOrder['id_order'];
                                    $data['route'] = 1;

                                    $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                                }
                            } else {
                                $data['idOrder'] = $findOrder['id_order'];
                                $resolution = $ordersDao->updateOrder($data);
                            }
                            if (isset($resolution['info'])) break;
                        }
                    }
                }

                // Materiales
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

                        if ($provider) $id_provider = $provider['id_provider'];

                        $data['idProvider'] = $id_provider;
                        $data['numOrder'] = $materials[$i]['num_order'];
                        $data['applicationDate'] = '';
                        $data['deliveryDate'] = '';
                        $data['requiredQuantity'] = abs($materials[$i]['available']);
                        $data['purchaseOrder'] = '';
                        $data['requestedQuantity'] = 0;

                        $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                        if (!$requisition)
                            $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
                        else {
                            $data['idRequisition'] = $requisition['id_requisition'];
                            $generalRequisitionsDao->updateRequisitionAuto($data);
                        }
                    }
                }

                $arr = $generalExMaterialsDao->findAllChildrenMaterialsConsolidaded($id_company);
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

                        if ($provider) $id_provider = $provider['id_provider'];

                        $data['idProvider'] = $id_provider;
                        $data['numOrder'] = $materials[$i]['num_order'];
                        $data['applicationDate'] = '';
                        $data['deliveryDate'] = '';
                        $data['requiredQuantity'] = abs($materials[$i]['available']);
                        $data['purchaseOrder'] = '';
                        $data['requestedQuantity'] = 0;

                        $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                        if (!$requisition)
                            $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
                        else {
                            $data['idRequisition'] = $requisition['id_requisition'];
                            $generalRequisitionsDao->updateRequisitionAuto($data);
                        }
                    }
                }
            }
        }
        // if ($resolution == null) {
        //     $products = $generalProductsMaterialsDao->findAllProductByMaterial($dataProductMaterial['material']);

        //     foreach ($products as $arr) {
        //         $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
        //         if (isset($product['stock']))
        //             $resolution = $productsDao->updateStockByProduct($arr['id_product'], $product['stock']);

        //         if (isset($resolution['info'])) break;
        //     }
        // }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Materia prima actualizada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('info' => true, 'message' => 'El material ya existe. Ingrese nuevo material');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deletePlanProductMaterial', function (Request $request, Response $response, $args) use (
    $productsMaterialsDao,
    $generalProductsMaterialsDao,
    $inventoryDaysDao,
    $productsDao,
    $explosionMaterialsDao,
    $licenseDao,
    $clientsDao,
    $explosionProductsDao,
    $generalExMaterialsDao,
    $generalExProductsDao,
    $generalRMStockDao,
    $generalRequisitionsDao,
    $requisitionsDao,
    $generalClientsDao,
    $generalSellersDao,
    $ordersDao,
    $generalOrdersDao,
    $generalProductsDao,
    $lastDataDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $generalMaterialsDao,
    $minimumStockDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductMaterial = $request->getParsedBody();
    $resolution = $productsMaterialsDao->deleteProductMaterial($dataProductMaterial['idProductMaterial']);

    if ($resolution == null) {
        $product = $generalProductsDao->findProductStatus($dataProductMaterial['idProduct']);

        $resolution = $generalProductsDao->updateStatusByProduct($dataProductMaterial['idProduct'], $product['status']);
    }

    if ($resolution == null) {
        $arr = $minimumStockDao->calcStockByMaterial($dataProductMaterial['idMaterial']);

        if (isset($arr['stock']))
            $resolution = $generalMaterialsDao->updateStockMaterial($dataProductMaterial['idMaterial'], $arr['stock']);
    }

    if ($resolution == null) {
        $cicle = true;

        while ($cicle == true) {
            $cicle = false;
            // Productos
            $arr = $generalExProductsDao->findAllCompositeConsolidated($id_company);
            $products = $generalExProductsDao->setDataEXComposite($arr);

            for ($i = 0; $i < sizeof($products); $i++) {
                $findEX = $generalExProductsDao->findEXProduct($products[$i]['id_child_product']);

                if (!$findEX)
                    $resolution = $explosionProductsDao->insertNewEXPByCompany($products[$i], $id_company);
                else {
                    $products[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                    $resolution = $explosionProductsDao->updateEXProduct($products[$i]);
                }

                if (intval($products[$i]['available']) < 0 && abs($products[$i]['available']) > $products[$i]['quantity_material']) {
                    $data = [];
                    $arr2 = $generalOrdersDao->findLastOrderByNumOrder($products[$i]['num_order']);

                    $seller = $generalSellersDao->findInternalSeller($id_company);

                    if ($seller) {
                        $client = $generalClientsDao->findInternalClient($id_company);

                        if (!$client) {
                            $company = $licenseDao->findLicenseCompany($id_company);
                            $dataClient = [];

                            $dataClient['nit'] = $company['nit'];
                            $dataClient['client'] = $company['company'];
                            $dataClient['address'] = $company['address'];
                            $dataClient['phone'] = $company['telephone'];
                            $dataClient['city'] = $company['city'];
                            $dataClient['type'] = 1;

                            $resolution = $clientsDao->insertClient($dataClient, $id_company);

                            $client = $lastDataDao->findLastInsertedClient();

                            $resolution = $generalClientsDao->changeStatusClient($client['id_client'], 1);
                        }

                        $data['order'] = $arr2['num_order'];
                        $data['dateOrder'] = date('Y-m-d');
                        $data['minDate'] = '';
                        $data['maxDate'] = '';
                        $data['idProduct'] = $products[$i]['id_child_product'];
                        $data['idClient'] = $client['id_client'];
                        $data['idSeller'] = $seller['id_seller'];
                        $data['route'] = 1;
                        $data['originalQuantity'] = abs($products[$i]['available']);
                        $data['typeOrder'] = 2;

                        $findOrder = $generalOrdersDao->findLastSameOrder($data);
                        if (!$findOrder) {
                            $cicle = true;
                            $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                            // $generalProductsDao->updateAccumulatedQuantity($products[$i]['id_child_product'], abs($products[$i]['available']), 2);

                            if (isset($resolution['info'])) break;
                            $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                            $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($products[$i]['id_child_product'], $lastOrder['id_order']);

                            if (!$programmingRoutes) {
                                $data['idOrder'] = $lastOrder['id_order'];
                                $data['route'] = 1;

                                $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                            }
                        } else {
                            $data['idOrder'] = $findOrder['id_order'];
                            $resolution = $ordersDao->updateOrder($data);
                        }
                        if (isset($resolution['info'])) break;
                    }
                }
            }

            $arr = $generalExProductsDao->findAllChildrenCompositeConsolidaded($id_company);
            $cProducts = $generalExProductsDao->setDataEXComposite($arr);

            for ($i = 0; $i < sizeof($cProducts); $i++) {
                $findEX = $generalExProductsDao->findEXProduct($cProducts[$i]['id_child_product']);

                if (!$findEX)
                    $resolution = $explosionProductsDao->insertNewEXPByCompany($cProducts[$i], $id_company);
                else {
                    $cProducts[$i]['id_explosion_product'] = $findEX['id_explosion_product'];
                    $resolution = $explosionProductsDao->updateEXProduct($cProducts[$i]);
                }

                if (intval($cProducts[$i]['available']) < 0 && abs($cProducts[$i]['available']) > $cProducts[$i]['quantity_material']) {
                    $data = [];
                    $arr2 = $generalOrdersDao->findLastOrderByNumOrder($cProducts[$i]['num_order']);

                    $seller = $generalSellersDao->findInternalSeller($id_company);

                    if ($seller) {
                        $client = $generalClientsDao->findInternalClient($id_company);

                        if (!$client) {
                            $company = $licenseDao->findLicenseCompany($id_company);
                            $dataClient = [];

                            $dataClient['nit'] = $company['nit'];
                            $dataClient['client'] = $company['company'];
                            $dataClient['address'] = $company['address'];
                            $dataClient['phone'] = $company['telephone'];
                            $dataClient['city'] = $company['city'];
                            $dataClient['type'] = 1;

                            $resolution = $clientsDao->insertClient($dataClient, $id_company);

                            $client = $lastDataDao->findLastInsertedClient();

                            $resolution = $generalClientsDao->changeStatusClient($client['id_client'], 1);
                        }

                        $data['order'] = $arr2['num_order'];
                        $data['dateOrder'] = date('Y-m-d');
                        $data['minDate'] = '';
                        $data['maxDate'] = '';
                        $data['idProduct'] = $cProducts[$i]['id_child_product'];
                        $data['idClient'] = $client['id_client'];
                        $data['idSeller'] = $seller['id_seller'];
                        $data['route'] = 1;
                        $data['originalQuantity'] = abs($cProducts[$i]['available']);
                        $data['typeOrder'] = 2;

                        $findOrder = $generalOrdersDao->findLastSameOrder($data);
                        if (!$findOrder) {
                            $resolution = $ordersDao->insertOrderByCompany($data, $id_company);
                            if (isset($resolution['info'])) break;
                            $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                            $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($cProducts[$i]['id_child_product'], $lastOrder['id_order']);

                            if (!$programmingRoutes) {
                                $data['idOrder'] = $lastOrder['id_order'];
                                $data['route'] = 1;

                                $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                            }
                        } else {
                            $data['idOrder'] = $findOrder['id_order'];
                            $resolution = $ordersDao->updateOrder($data);
                        }
                        if (isset($resolution['info'])) break;
                    }
                }
            }

            // Materiales
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

                    if ($provider) $id_provider = $provider['id_provider'];

                    $data['idProvider'] = $id_provider;
                    $data['numOrder'] = $materials[$i]['num_order'];
                    $data['applicationDate'] = '';
                    $data['deliveryDate'] = '';
                    $data['requiredQuantity'] = abs($materials[$i]['available']);
                    $data['purchaseOrder'] = '';
                    $data['requestedQuantity'] = 0;

                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                    if (!$requisition)
                        $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
                    else {
                        $data['idRequisition'] = $requisition['id_requisition'];
                        $generalRequisitionsDao->updateRequisitionAuto($data);
                    }
                }
            }

            $arr = $generalExMaterialsDao->findAllChildrenMaterialsConsolidaded($id_company);
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

                    if ($provider) $id_provider = $provider['id_provider'];

                    $data['idProvider'] = $id_provider;
                    $data['numOrder'] = $materials[$i]['num_order'];
                    $data['applicationDate'] = '';
                    $data['deliveryDate'] = '';
                    $data['requiredQuantity'] = abs($materials[$i]['available']);
                    $data['purchaseOrder'] = '';
                    $data['requestedQuantity'] = 0;

                    $requisition = $generalRequisitionsDao->findRequisitionByApplicationDate($materials[$i]['id_material']);

                    if (!$requisition)
                        $generalRequisitionsDao->insertRequisitionAutoByCompany($data, $id_company);
                    else {
                        $data['idRequisition'] = $requisition['id_requisition'];
                        $generalRequisitionsDao->updateRequisitionAuto($data);
                    }
                }
            }
        }
    }

    // Calculo Dias Inventario Materiales  
    if ($resolution == null) {
        $products = $productsMaterialsDao->findAllProductsMaterials($dataProductMaterial['idProduct'], $id_company);

        foreach ($products as $arr) {
            $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);
            if (isset($inventory['days']))
                $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $inventory['days']);

            if (isset($resolution['info'])) break;
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Materia prima eliminada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la materia prima asignada, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/calcQuantityFTM', function (Request $request, Response $response, $args) use (
    $generalProductsDao,
    $generalMaterialsDao,
    $generalProductsMaterialsDao
) {
    // session_start();
    // $id_company = $_SESSION['id_company'];
    $arr = $request->getParsedBody();

    // $type = $arr['type'];
    $dataMaterial = $generalMaterialsDao->findMaterialById($arr['idMaterial']);
    $dataProduct = $generalProductsDao->findProductById($arr['idProduct']);
    $weight = 0;

    $dataProduct['window'] > 0 ? $window = floatval($dataProduct['window']) : $window = 1;

    $weight = ((floatval($dataMaterial['grammage']) * floatval($dataProduct['length']) * floatval($dataProduct['total_width'])) / 10000000) / $window;
    // switch ($type) {
    //     case '1': // Papel

    //         break;
    //     default:
    //         $quantity = floatval($arr['quantityCalc']);
    //         $quantityFTM = 0;

    //         $dataFTM = $generalProductsMaterialsDao->findProductsMaterialsByCompany($arr, $id_company);

    //         if ($dataFTM) $quantityFTM = $dataFTM['quantity'];

    //         $weight = $quantity * $quantityFTM;
    //         break;
    // }

    $resp = ['weight' => $weight];

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
