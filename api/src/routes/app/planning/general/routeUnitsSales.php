<?php

use TezlikPlaneacion\dao\UnitSalesDao;
use TezlikPlaneacion\dao\ClassificationDao;
use TezlikPlaneacion\dao\ClientsDao;
use TezlikPlaneacion\dao\CompaniesLicenseStatusDao;
use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\Dao\ConversionUnitsDao;
use TezlikPlaneacion\dao\ExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\Dao\GeneralCompositeProductsDao;
use TezlikPlaneacion\dao\GeneralExplosionMaterialsDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsMaterialsDao;
use TezlikPlaneacion\dao\GeneralProgrammingRoutesDao;
use TezlikPlaneacion\dao\GeneralRequisitionsDao;
use TezlikPlaneacion\dao\GeneralRMStockDao;
use TezlikPlaneacion\dao\GeneralSellersDao;
use TezlikPlaneacion\dao\GeneralUnitSalesDao;
use TezlikPlaneacion\dao\InventoryDaysDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\LicenseCompanyDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\ProgrammingRoutesDao;
use TezlikPlaneacion\dao\RequisitionsDao;

$unitSalesDao = new UnitSalesDao();
$licenseDao = new LicenseCompanyDao();
$clientsDao = new ClientsDao();
$lastDataDao = new LastDataDao();
$programmingRoutesDao = new ProgrammingRoutesDao();
$generalProgrammingRoutesDao = new GeneralProgrammingRoutesDao();
$generalProductsDao = new GeneralProductsDao();
$generalUnitSalesDao = new GeneralUnitSalesDao();
$productsDao = new ProductsDao();
$classificationDao = new ClassificationDao();
$minimumStockDao = new MinimumStockDao();
$generalMaterialDao = new GeneralMaterialsDao();
$productMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalProductsMaterialsDao = new GeneralProductsMaterialsDao();
$generalCompositeProductsDao = new GeneralCompositeProductsDao();
$inventoryDaysDao = new InventoryDaysDao();
$ordersDao = new OrdersDao();
$generalOrdersDao = new GeneralOrdersDao();
$generalClientsDao = new GeneralClientsDao();
$generalSellersDao = new GeneralSellersDao();
$companiesLicenseDao = new CompaniesLicenseStatusDao();
$requisitionsDao = new RequisitionsDao();
$generalRequisitionsDao = new GeneralRequisitionsDao();
$conversionUnitsDao = new ConversionUnitsDao();
$generalRMStockDao = new GeneralRMStockDao();
$explosionMaterialsDao = new ExplosionMaterialsDao();
$generalExMaterialsDao = new GeneralExplosionMaterialsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/unitSales', function (Request $request, Response $response, $args) use (
    $unitSalesDao,
    $generalUnitSalesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $unitSales = $unitSalesDao->findAllSalesByCompany($id_company);

    $saleDays = $generalUnitSalesDao->findSaleDaysByCompany($id_company);
    $allSD = $generalUnitSalesDao->findAllSaleDays();

    $total_days = 0;
    $saleDaysCount = count($saleDays);
    $allSDCount = count($allSD);
    $unitSalesCount = count($unitSales);

    for ($i = 0; $i < $saleDaysCount; $i++) {
        for ($j = 0; $j < $allSDCount; $j++) {
            if ($saleDays[$i]['month'] == $allSD[$j]['month']) {
                $allSD[$j]['id_sale_day'] = $saleDays[$i]['id_sale_day'];
                $allSD[$j]['days'] = $saleDays[$i]['days'];
                $allSD[$j]['new'] = 'false';
            }
        }
        $total_days += $saleDays[$i]['days'];
    }

    for ($i = 0; $i < $unitSalesCount; $i++) {
        if ($total_days > 0) {
            $unitSales[$i]['average_day'] = $unitSales[$i]['average_month'] / ($total_days / 12);
        } else {
            $unitSales[$i]['average_day'] = 0;
        }
    }

    // Asignar el total de días al primer elemento de $allSD, si es necesario
    // if ($allSDCount > 0) {
    //     $allSD[0]['total_days'] = $total_days;
    // }

    $response->getBody()->write(json_encode($unitSales, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/productUnitSales', function (Request $request, Response $response, $args) use ($generalProductsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $products = $generalProductsDao->findAllProductsUnitSalesByCompany($id_company);
    $response->getBody()->write(json_encode($products, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/unitSalesDataValidation', function (Request $request, Response $response, $args) use (
    $generalUnitSalesDao,
    $generalProductsDao
) {
    $dataSale = $request->getParsedBody();

    if (isset($dataSale)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $unitSales = $dataSale['importUnitSales'];

        for ($i = 0; $i < sizeof($unitSales); $i++) {
            if (
                empty($unitSales[$i]['referenceProduct']) == '' && empty($unitSales[$i]['product']) == '' &&
                $unitSales[$i]['january'] == '' && $unitSales[$i]['february'] == '' && $unitSales[$i]['march'] == '' && $unitSales[$i]['april'] == '' &&
                $unitSales[$i]['may'] == '' && $unitSales[$i]['june'] == '' && $unitSales[$i]['july'] == '' && $unitSales[$i]['august'] == '' &&
                $unitSales[$i]['september'] == '' && $unitSales[$i]['october'] == '' &&  $unitSales[$i]['november'] == '' && $unitSales[$i]['december'] == ''
            ) {
                $i = $i + 2;
                $dataImportUnitSales = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }

            if (
                empty(trim($unitSales[$i]['referenceProduct'])) == '' && empty(trim($unitSales[$i]['product'])) == '' &&
                trim($unitSales[$i]['january']) == '' && trim($unitSales[$i]['february']) == '' && trim($unitSales[$i]['march']) == '' && trim($unitSales[$i]['april']) == '' &&
                trim($unitSales[$i]['may']) == '' && trim($unitSales[$i]['june']) == '' && trim($unitSales[$i]['july']) == '' && trim($unitSales[$i]['august']) == '' &&
                trim($unitSales[$i]['september']) == '' && trim($unitSales[$i]['october']) == '' &&  trim($unitSales[$i]['november']) == '' && trim($unitSales[$i]['december']) == ''
            ) {
                $i = $i + 2;
                $dataImportUnitSales = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $generalProductsDao->findProduct($unitSales[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportUnitSales = array('error' => true, 'message' => "Producto no existe en la base de datos.<br>Fila: {$i}");
                break;
            } else $unitSales[$i]['idProduct'] = $findProduct['id_product'];

            $findUnitSales = $generalUnitSalesDao->findSales($unitSales[$i], $id_company);
            !$findUnitSales ? $insert = $insert + 1 : $update = $update + 1;

            $dataImportUnitSales['insert'] = $insert;
            $dataImportUnitSales['update'] = $update;
        }
    } else
        $dataImportUnitSales = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportUnitSales, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addUnitSales', function (Request $request, Response $response, $args) use (
    $unitSalesDao,
    $generalUnitSalesDao,
    $productsDao,
    $generalProductsDao,
    $generalMaterialDao,
    $licenseDao,
    $clientsDao,
    $lastDataDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $ordersDao,
    $productMaterialsDao,
    $explosionMaterialsDao,
    $generalCompositeProductsDao,
    $generalExMaterialsDao,
    $generalProductsMaterialsDao,
    $compositeProductsDao,
    $generalRMStockDao,
    $classificationDao,
    $minimumStockDao,
    $inventoryDaysDao,
    $companiesLicenseDao,
    $generalOrdersDao,
    $generalClientsDao,
    $generalSellersDao,
    $requisitionsDao,
    $generalRequisitionsDao,
    $conversionUnitsDao
) {
    session_start();
    $dataSale = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $dataSales = sizeof($dataSale);

    if ($dataSales > 1) {
        $resolution = $unitSalesDao->insertSalesByCompany($dataSale, $id_company);

        if ($resolution == null) {
            // Calcular stock material
            $materials = $productMaterialsDao->findAllProductsMaterials($dataSale['idProduct'], $id_company);

            for ($i = 0; $i < sizeof($materials); $i++) {
                if (isset($resolution['info'])) break;
                // Calculo Dias Inventario Materiales  
                $inventory = $inventoryDaysDao->calcInventoryMaterialDays($materials[$i]['id_material']);
                if (isset($inventory['days']))
                    $resolution = $inventoryDaysDao->updateInventoryMaterialDays($materials[$i]['id_material'], $inventory['days']);

                if (isset($resolution['info'])) break;
                $arr = $minimumStockDao->calcStockByMaterial($materials[$i]['id_material']);

                if (isset($arr['stock']))
                    $resolution = $generalMaterialDao->updateStockMaterial($materials[$i]['id_material'], $arr['stock']);

                if (isset($resolution['info'])) break;
            }

            // if ($resolution == null) {
            //     $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataSale['idProduct'], $id_company);

            //     foreach ($compositeProducts as $k) {
            //         $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

            //         $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

            //         if (isset($arr['stock']) && isset($product['stock'])) {
            //             $stock = $product['stock'] + $arr['stock'];

            //             $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
            //         }
            //     }
            // }

            // Calcular stock producto
            if ($resolution == null) {
                $product = $minimumStockDao->calcStockByProduct($dataSale['idProduct']);
                if (isset($product['stock']))
                    $resolution = $generalProductsDao->updateStockByProduct($dataSale['idProduct'], $product['stock']);
            }

            if (
                $product['quantity'] > 0 && $product['quantity'] < isset($product['stock'])
            ) {
                $data = [];
                $arr2 = $generalOrdersDao->findLastNumOrderByCompany($id_company);

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
                    $data['idProduct'] = $dataSale['idProduct'];
                    $data['idClient'] = $client['id_client'];
                    $data['idSeller'] = $seller['id_seller'];
                    $data['route'] = 1;
                    $data['originalQuantity'] = abs($product['stock']);
                    $data['typeOrder'] = 2;

                    $findOrder = $generalOrdersDao->findLastSameOrder($data);
                    if (!$findOrder) {
                        $resolution = $ordersDao->insertOrderByCompany($data, $id_company);

                        $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                        $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($dataSale['idProduct'], $lastOrder['id_order']);

                        if (!$programmingRoutes) {
                            $data['idOrder'] = $lastOrder['id_order'];
                            $data['route'] = 1;

                            $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                        }
                    } else {
                        $data['idOrder'] = $findOrder['id_order'];
                        $resolution = $ordersDao->updateOrder($data);
                    }
                }
            }
        }

        if ($resolution == null) {
            $arr = $generalExMaterialsDao->findAllMaterialsConsolidated($id_company);

            $materials = $generalExMaterialsDao->setDataEXMaterials($arr);

            for (
                $i = 0;
                $i < sizeof($materials);
                $i++
            ) {
                $findEX = $generalExMaterialsDao->findEXMaterial($materials[$i]['id_material']);

                if (!$findEX)
                    $resolution = $explosionMaterialsDao->insertNewEXMByCompany($materials[$i], $id_company);
                else {
                    $materials[$i]['id_explosion_material'] = $findEX['id_explosion_material'];
                    $resolution = $explosionMaterialsDao->updateEXMaterials($materials[$i]);
                }

                if (
                    intval($materials[$i]['available']) < 0
                ) {
                    $data = [];
                    $data['idMaterial'] = $materials[$i]['id_material'];

                    $provider = $generalRMStockDao->findProviderByStock($materials[$i]['id_material']);

                    $id_provider = 0;

                    if ($provider) $id_provider = $provider['id_provider'];

                    $data['numOrder'] = $materials[$i]['num_order'];
                    $data['idProvider'] = $id_provider;
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

        // Calcular Dias inventario Producto
        if ($resolution == null) {
            $inventory = $inventoryDaysDao->calcInventoryProductDays($dataSale['idProduct']);

            !isset($inventory['days']) ? $days = 0 : $days = $inventory['days'];

            $resolution = $inventoryDaysDao->updateInventoryProductDays($dataSale['idProduct'], $days);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Venta asociada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
    } else {
        $unitSales = $dataSale['importUnitSales'];

        $resolution = 1;
        for ($i = 0; $i < sizeof($unitSales); $i++) {
            if (isset($resolution['info'])) break;
            // Obtener id producto
            $findProduct = $generalProductsDao->findProduct($unitSales[$i], $id_company);
            $unitSales[$i]['idProduct'] = $findProduct['id_product'];

            $findUnitSales = $generalUnitSalesDao->findSales($unitSales[$i], $id_company);
            if (!$findUnitSales)
                $resolution = $unitSalesDao->insertSalesByCompany($unitSales[$i], $id_company);
            else {
                $unitSales[$i]['idSale'] = $findUnitSales['id_unit_sales'];
                $resolution = $unitSalesDao->updateSales($unitSales[$i]);
            }

            if (isset($resolution['info'])) break;
            // Calcular stock material
            $materials = $productMaterialsDao->findAllProductsMaterials($unitSales[$i]['idProduct'], $id_company);

            for ($j = 0; $j < sizeof($materials); $j++) {
                if (isset($resolution['info'])) break;
                // Calculo Dias Inventario Materiales  
                $inventory = $inventoryDaysDao->calcInventoryMaterialDays($materials[$j]['id_material']);
                if (isset($inventory['days']))
                    $resolution = $inventoryDaysDao->updateInventoryMaterialDays($materials[$j]['id_material'], $inventory['days']);

                if (isset($resolution['info'])) break;
                $arr = $minimumStockDao->calcStockByMaterial($materials[$j]['id_material']);

                if (isset($arr['stock']))
                    $resolution = $generalMaterialDao->updateStockMaterial($materials[$j]['id_material'], $arr['stock']);
            }

            if (isset($resolution['info'])) break;

            // $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($unitSales[$i]['idProduct'], $id_company);

            // foreach ($compositeProducts as $k) {
            //     $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

            //     $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

            //     if (isset($arr['stock']) && isset($product['stock'])) {
            //         $stock = $product['stock'] + $arr['stock'];

            //         $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
            //     }
            // }

            // Calcular stock producto
            if ($resolution == null) {
                $product = $minimumStockDao->calcStockByProduct($unitSales[$i]['idProduct']);
                if (isset($product['stock']))
                    $resolution = $generalProductsDao->updateStockByProduct($unitSales[$i]['idProduct'], $product['stock']);
            }

            if (isset($resolution['info'])) break;
            // Calcular Dias inventario Producto
            $inventory = $inventoryDaysDao->calcInventoryProductDays($unitSales[$i]['idProduct']);

            !isset($inventory['days']) ? $days = 0 : $days = $inventory['days'];

            $resolution = $inventoryDaysDao->updateInventoryProductDays($unitSales[$i]['idProduct'], $days);
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Venta importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    if ($resolution == null) {
        $license = $companiesLicenseDao->status($id_company);

        if ($license['months'] > 0) {
            $products = $productsDao->findAllProductsByCompany($id_company);

            $resolution = $generalProductsDao->updateGeneralClassification($id_company);

            for ($j = 0; $j < sizeof($products); $j++) {
                if (isset($resolution['info'])) break;
                // $inventory = $classificationDao->calcInventoryABCBYProduct($products[$j]['id_product'], $license['months']);

                // $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);

                $composite = $generalCompositeProductsDao->findCompositeProductByChild($products[$j]['id_product']);
                $classification = '';

                if (sizeof($composite) > 0) {
                    // $inventory = $generalProductsDao->findProductById($composite[0]['id_product']);
                    $inventory = $classificationDao->calcInventoryABCBYProduct($composite[0]['id_product'], $license['months']);
                    $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);
                    $classification = $inventory['classification'];
                } else {
                    $inventory = $classificationDao->calcInventoryABCBYProduct($products[$j]['id_product'], $license['months']);
                    $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);
                    $classification = $inventory['classification'];
                }

                $resolution = $classificationDao->updateProductClassification($products[$j]['id_product'], $classification);
            }
        }
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateUnitSale', function (Request $request, Response $response, $args) use (
    $unitSalesDao,
    $minimumStockDao,
    $productsDao,
    $generalProductsDao,
    $generalMaterialDao,
    $productMaterialsDao,
    $licenseDao,
    $clientsDao,
    $lastDataDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $generalCompositeProductsDao,
    $conversionUnitsDao,
    $inventoryDaysDao,
    $compositeProductsDao,
    $explosionMaterialsDao,
    $generalExMaterialsDao,
    $generalRMStockDao,
    $companiesLicenseDao,
    $classificationDao,
    $generalOrdersDao,
    $generalClientsDao,
    $generalSellersDao,
    $ordersDao,
    $requisitionsDao,
    $generalRequisitionsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataSale = $request->getParsedBody();

    $resolution = $unitSalesDao->updateSales($dataSale);

    if ($resolution == null) {
        // Calcular stock material
        $materials = $productMaterialsDao->findAllProductsMaterials($dataSale['idProduct'], $id_company);

        for ($i = 0; $i < sizeof($materials); $i++) {
            if (isset($resolution['info'])) break;

            // Calculo Dias Inventario Materiales  
            $inventory = $inventoryDaysDao->calcInventoryMaterialDays($materials[$i]['id_material']);
            if (isset($inventory['days']))
                $resolution = $inventoryDaysDao->updateInventoryMaterialDays($materials[$i]['id_material'], $inventory['days']);

            if (isset($resolution['info'])) break;

            $arr = $minimumStockDao->calcStockByMaterial($materials[$i]['id_material']);

            if (isset($arr['stock']))
                $resolution = $generalMaterialDao->updateStockMaterial($materials[$i]['id_material'], $arr['stock']);
        }

        // if ($resolution == null) {
        //     $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataSale['idProduct'], $id_company);

        //     foreach ($compositeProducts as $k) {
        //         $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

        //         $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

        //         if (isset($arr['stock']) && isset($product['stock'])) {
        //             $stock = $product['stock'] + $arr['stock'];

        //             $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
        //         }
        //     }
        // }

        // Calcular stock producto
        if ($resolution == null) {
            $product = $minimumStockDao->calcStockByProduct($dataSale['idProduct']);
            if (isset($product['stock']))
                $resolution = $generalProductsDao->updateStockByProduct($dataSale['idProduct'], $product['stock']);
        }

        if ($product['quantity'] > 0 && $product['quantity'] < isset($product['stock'])) {
            $data = [];
            $arr2 = $generalOrdersDao->findLastNumOrderByCompany($id_company);

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
                $data['idProduct'] = $dataSale['idProduct'];
                $data['idClient'] = $client['id_client'];
                $data['idSeller'] = $seller['id_seller'];
                $data['route'] = 1;
                $data['originalQuantity'] = abs($product['stock']);
                $data['typeOrder'] = 2;

                $findOrder = $generalOrdersDao->findLastSameOrder($data);
                if (!$findOrder) {
                    $resolution = $ordersDao->insertOrderByCompany($data, $id_company);

                    $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                    $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($dataSale['idProduct'], $lastOrder['id_order']);

                    if (!$programmingRoutes) {
                        $data['idOrder'] = $lastOrder['id_order'];
                        $data['route'] = 1;

                        $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                    }
                } else {
                    $data['idOrder'] = $findOrder['id_order'];
                    $resolution = $ordersDao->updateOrder($data);
                }
            }
        }
    }

    if ($resolution == null) {
        $arr = $generalExMaterialsDao->findAllMaterialsConsolidated($id_company);

        $materials = $generalExMaterialsDao->setDataEXMaterials($arr);

        for (
            $i = 0;
            $i < sizeof($materials);
            $i++
        ) {
            $findEX = $generalExMaterialsDao->findEXMaterial($materials[$i]['id_material']);

            if (!$findEX)
                $resolution = $explosionMaterialsDao->insertNewEXMByCompany($materials[$i], $id_company);
            else {
                $materials[$i]['id_explosion_material'] = $findEX['id_explosion_material'];
                $resolution = $explosionMaterialsDao->updateEXMaterials($materials[$i]);
            }

            if (
                intval($materials[$i]['available']) < 0
            ) {
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
    // Calcular Dias inventario Producto
    if ($resolution == null) {
        $inventory = $inventoryDaysDao->calcInventoryProductDays($dataSale['idProduct']);

        !isset($inventory['days']) ? $days = 0 : $days = $inventory['days'];

        $resolution = $inventoryDaysDao->updateInventoryProductDays($dataSale['idProduct'], $days);
    }

    if ($resolution == null) {
        $license = $companiesLicenseDao->status($id_company);

        if ($license['months'] > 0) {
            $products = $productsDao->findAllProductsByCompany($id_company);

            $resolution = $generalProductsDao->updateGeneralClassification($id_company);

            for ($i = 0; $i < sizeof($products); $i++) {
                if (isset($resolution['info'])) break;

                $composite = $generalCompositeProductsDao->findCompositeProductByChild($products[$i]['id_product']);
                $classification = '';

                if (sizeof($composite) > 0) {
                    $inventory = $classificationDao->calcInventoryABCBYProduct($composite[0]['id_product'], $license['months']);
                    $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);
                    $classification = $inventory['classification'];
                } else {
                    $inventory = $classificationDao->calcInventoryABCBYProduct($products[$i]['id_product'], $license['months']);
                    $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);
                    $classification = $inventory['classification'];
                }

                $resolution = $classificationDao->updateProductClassification($products[$i]['id_product'], $classification);
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Venta actualizada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteUnitSale', function (Request $request, Response $response, $args) use (
    $unitSalesDao,
    $minimumStockDao,
    $generalMaterialDao,
    $productsDao,
    $generalProductsDao,
    $licenseDao,
    $clientsDao,
    $generalRMStockDao,
    $generalCompositeProductsDao,
    $productMaterialsDao,
    $lastDataDao,
    $programmingRoutesDao,
    $generalProgrammingRoutesDao,
    $generalProductsMaterialsDao,
    $conversionUnitsDao,
    $inventoryDaysDao,
    $companiesLicenseDao,
    $classificationDao,
    $ordersDao,
    $generalOrdersDao,
    $generalClientsDao,
    $generalSellersDao,
    $requisitionsDao,
    $generalRequisitionsDao,
    $compositeProductsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataSale = $request->getParsedBody();
    $resolution = $unitSalesDao->deleteSale($dataSale['idUnitSales']);

    if ($resolution == null) {
        $materials = $productMaterialsDao->findAllProductsMaterials($dataSale['idProduct'], $id_company);
        // $orders = $generalOrdersDao->findAllOrdersByProduct($dataSale['idProduct']);
        // $stock = 0;

        for ($i = 0; $i < sizeof($materials); $i++) {
            if (isset($resolution['info'])) break;
            // Calculo Dias Inventario Materiales  
            $inventory = $inventoryDaysDao->calcInventoryMaterialDays($materials[$i]['id_material']);
            if (isset($inventory['days']))
                $resolution = $inventoryDaysDao->updateInventoryMaterialDays($materials[$i]['id_material'], $inventory['days']);

            if (isset($resolution['info'])) break;
            $arr = $minimumStockDao->calcStockByMaterial($materials[$i]['id_material']);

            if (isset($arr['stock']))
                $resolution = $generalMaterialDao->updateStockMaterial($materials[$i]['id_material'], $arr['stock']);

            if (isset($resolution['info'])) break;
        }
        // if ($resolution == null) {
        //     $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($dataSale['idProduct'], $id_company);

        //     foreach ($compositeProducts as $k) {
        //         $product = $minimumStockDao->calcStockByProduct($k['id_child_product']);

        //         $arr = $minimumStockDao->calcStockByComposite($k['id_child_product']);

        //         if (isset($arr['stock']) && isset($product['stock'])) {
        //             $stock = $product['stock'] + $arr['stock'];

        //             $resolution = $generalProductsDao->updateStockByProduct($k['id_child_product'], $stock);
        //         }
        //     }
        // }
        // Calcular stock producto
        if ($resolution == null) {
            $product = $minimumStockDao->calcStockByProduct($dataSale['idProduct']);
            if (isset($product['stock']))
                $resolution = $generalProductsDao->updateStockByProduct($dataSale['idProduct'], $product['stock']);
        }
        if ($product['quantity'] > 0 && $product['quantity'] < isset($product['stock'])) {
            $data = [];
            $arr2 = $generalOrdersDao->findLastNumOrderByCompany($id_company);

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
                $data['idProduct'] = $dataSale['idProduct'];
                $data['idClient'] = $client['id_client'];
                $data['idSeller'] = $seller['id_seller'];
                $data['route'] = 1;
                $data['originalQuantity'] = abs($product['stock']);
                $data['typeOrder'] = 2;

                $findOrder = $generalOrdersDao->findLastSameOrder($data);
                if (!$findOrder) {
                    $resolution = $ordersDao->insertOrderByCompany($data, $id_company);

                    $lastOrder = $lastDataDao->findLastInsertedOrder($id_company);

                    $programmingRoutes = $generalProgrammingRoutesDao->findProgrammingRoutes($dataSale['idProduct'], $lastOrder['id_order']);

                    if (!$programmingRoutes) {
                        $data['idOrder'] = $lastOrder['id_order'];
                        $data['route'] = 1;

                        $resolution = $programmingRoutesDao->insertProgrammingRoutes($data, $id_company);
                    }
                } else {
                    $data['idOrder'] = $findOrder['id_order'];
                    $resolution = $ordersDao->updateOrder($data);
                }
            }
        }
    }

    // Calcular Dias inventario Producto
    if ($resolution == null) {
        $inventory = $inventoryDaysDao->calcInventoryProductDays($dataSale['idProduct']);

        !isset($inventory['days']) ? $days = 0 : $days = $inventory['days'];

        $resolution = $inventoryDaysDao->updateInventoryProductDays($dataSale['idProduct'], $days);
    }

    if ($resolution == null) {
        $license = $companiesLicenseDao->status($id_company);

        if ($license['months'] > 0) {
            $products = $productsDao->findAllProductsByCompany($id_company);

            $resolution = $generalProductsDao->updateGeneralClassification($id_company);

            for ($i = 0; $i < sizeof($products); $i++) {
                if (isset($resolution['info'])) break;
                // $inventory = $classificationDao->calcInventoryABCBYProduct($products[$i]['id_product'], $license['months']);

                // $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);

                $composite = $generalCompositeProductsDao->findCompositeProductByChild($products[$i]['id_product']);
                $classification = '';

                if (sizeof($composite) > 0) {
                    // $inventory = $generalProductsDao->findProductById($composite[0]['id_product']);
                    $inventory = $classificationDao->calcInventoryABCBYProduct($composite[0]['id_product'], $license['months']);
                    $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);
                    $classification = $inventory['classification'];
                } else {
                    $inventory = $classificationDao->calcInventoryABCBYProduct($products[$i]['id_product'], $license['months']);
                    $inventory = $classificationDao->calcClassificationByProduct($inventory['year_sales'], $id_company);
                    $classification = $inventory['classification'];
                }

                $resolution = $classificationDao->updateProductClassification($products[$i]['id_product'], $classification);
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Venta eliminada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la Venta, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/saleDays', function (Request $request, Response $response, $args) use ($generalUnitSalesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $saleDays = $generalUnitSalesDao->findSaleDaysByCompany($id_company);
    $allSD = $generalUnitSalesDao->findAllSaleDays();

    for ($i = 0; $i < sizeof($saleDays); $i++) {
        for ($j = 0; $j < sizeof($allSD); $j++) {
            if ($saleDays[$i]['month'] == $allSD[$j]['month']) {
                $allSD[$j]['id_sale_day'] = $saleDays[$i]['id_sale_day'];
                $allSD[$j]['days'] = $saleDays[$i]['days'];
                $allSD[$j]['new'] = 'false';
            }
        }
    }

    $response->getBody()->write(json_encode($allSD, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addSaleDays', function (Request $request, Response $response, $args) use (
    $generalUnitSalesDao,
    $unitSalesDao,
    $generalProductsMaterialsDao,
    $inventoryDaysDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataSales = $request->getParsedBody();

    $saleDay = $generalUnitSalesDao->findSaleDays($dataSales, $id_company);

    if (!$saleDay) {
        $resolution = $generalUnitSalesDao->insertSaleDaysByCompany($dataSales, $id_company);

        // Calcular Dias Inventario
        if ($resolution == null) {
            $month = date('m');
            $year = date('Y');

            if ($dataSales['month'] == $month && $dataSales['year'] == $year) {

                // Materiales
                $materials = $generalProductsMaterialsDao->findAllDistinctMaterials($id_company);

                foreach ($materials as $arr) {
                    $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);

                    !$inventory['days'] ? $days = 0 : $days = $inventory['days'];

                    $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $days);

                    if (isset($resolution['info'])) break;
                }

                // Productos
                $products = $unitSalesDao->findAllSalesByCompany($id_company);

                foreach ($products as $arr) {
                    $inventory = $inventoryDaysDao->calcInventoryProductDays($arr['id_product']);

                    !isset($inventory['days']) ? $days = 0 : $days = $inventory['days'];

                    $resolution = $inventoryDaysDao->updateInventoryProductDays($arr['id_product'], $days);

                    if (isset($resolution['info'])) break;
                }
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Dias de venta almacenada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'No es posible Guardar la información. intente nuevamente');
    } else {
        $resp = array('error' => true, 'message' => 'Dia de venta de ese mes ya existe. Ingrese un mes nuevo');
    }


    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/updateSaleDays', function (Request $request, Response $response, $args) use (
    $generalUnitSalesDao,
    $unitSalesDao,
    $generalProductsMaterialsDao,
    $inventoryDaysDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataSales = $request->getParsedBody();

    $saleDay = $generalUnitSalesDao->findSaleDays($dataSales, $id_company);

    !is_array($saleDay) ? $data['id_sale_day'] = 0 : $data = $saleDay;
    if ($data['id_sale_day'] == $dataSales['idSaleDay'] || $data['id_sale_day'] == 0) {
        $resolution = $generalUnitSalesDao->updateSaleDays($dataSales);

        // Calcular Dias Inventario
        if ($resolution == null) {
            $month = intval(date('m'));
            $year = intval(date('Y'));

            if ($dataSales['month'] == $month && $dataSales['year'] == $year) {
                // Materiales
                $materials = $generalProductsMaterialsDao->findAllDistinctMaterials($id_company);

                foreach ($materials as $arr) {
                    $inventory = $inventoryDaysDao->calcInventoryMaterialDays($arr['id_material']);

                    !$inventory['days'] ? $days = 0 : $days = $inventory['days'];

                    $resolution = $inventoryDaysDao->updateInventoryMaterialDays($arr['id_material'], $days);

                    if (isset($resolution['info'])) break;
                }

                // Productos
                $products = $unitSalesDao->findAllSalesByCompany($id_company);

                foreach ($products as $arr) {
                    $inventory = $inventoryDaysDao->calcInventoryProductDays($arr['id_product']);

                    !isset($inventory['days']) ? $days = 0 : $days = $inventory['days'];

                    $resolution = $inventoryDaysDao->updateInventoryProductDays($arr['id_product'], $days);

                    if (isset($resolution['info'])) break;
                }
            }
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Dias de venta almacenada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'No es posible Guardar la información. intente nuevamente');
    } else {
        $resp = array('error' => true, 'message' => 'Dia de venta de ese mes ya existe. Ingrese un mes nuevo');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
