<?php

use PHPMailer\Test\PHPMailer\IsValidHostTest;
use TezlikPlaneacion\dao\UnitSalesDao;
use TezlikPlaneacion\dao\ClassificationDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsMaterialsDao;
use TezlikPlaneacion\dao\GeneralUnitSalesDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\OrdersDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;

$unitSalesDao = new UnitSalesDao();
$generalProductsDao = new GeneralProductsDao();
$generalUnitSalesDao = new GeneralUnitSalesDao();
$productsDao = new GeneralProductsDao();
$classificationDao = new ClassificationDao();
$minimumStockDao = new MinimumStockDao();
$generalMaterialDao = new GeneralMaterialsDao();
$productMaterialsDao = new ProductsMaterialsDao();
$generalProductsMaterialsDao = new GeneralProductsMaterialsDao();
$ordersDao = new OrdersDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/unitSales', function (Request $request, Response $response, $args) use ($unitSalesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $unitSales = $unitSalesDao->findAllSalesByCompany($id_company);
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
    $productsDao
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
                empty($unitSales[$i]['january']) && empty($unitSales[$i]['february']) && empty($unitSales[$i]['march']) && empty($unitSales[$i]['april']) &&
                empty($unitSales[$i]['may']) && empty($unitSales[$i]['june']) && empty($unitSales[$i]['july']) && empty($unitSales[$i]['august']) &&
                empty($unitSales[$i]['september']) && empty($unitSales[$i]['october']) &&  empty($unitSales[$i]['november']) && empty($unitSales[$i]['december'])
            ) {
                $i = $i + 2;
                $dataImportUnitSales = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
                break;
            }

            // Obtener id producto
            $findProduct = $productsDao->findProduct($unitSales[$i], $id_company);
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
    $generalMaterialDao,
    $ordersDao,
    $productMaterialsDao,
    $generalProductsMaterialsDao,
    $classificationDao,
    $minimumStockDao,
    $inventoryDaysDao
) {
    session_start();
    $dataSale = $request->getParsedBody();
    $id_company = $_SESSION['id_company'];

    $dataSales = sizeof($dataSale);

    if ($dataSales > 1) {
        $resolution = $unitSalesDao->insertSalesByCompany($dataSale, $id_company);

        if ($resolution == null) {
            // Calcular stock material
            $materials = $productMaterialsDao->findAllProductsmaterials($dataSale['idProduct'], $id_company);

            for ($i = 0; $i < sizeof($materials); $i++) {
                if (isset($resolution['info'])) break;

                // Calcular stock material
                $arr = $minimumStockDao->calcStockByMaterial($materials[$i]['id_material']);
                if (isset($arr['stock']))
                    $resolution = $generalMaterialDao->updateStockMaterial($materials[$i]['id_material'], $arr['stock']);

                if (isset($resolution['info'])) break;
                // Calcular stock producto
                $products = $generalProductsMaterialsDao->findAllProductByMaterial($materials[$i]['id_material']);

                foreach ($products as $arr) {
                    $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
                    if (isset($product['stock']))
                        $resolution = $productsDao->updateStockByProduct($arr['id_product'], $product['stock']);

                    if (isset($resolution['info'])) break;
                }
            }
        }
        if ($resolution == null) {
            // Calcular Dias inventario 
            $inventory = $inventoryDaysDao->calcInventoryDays($dataSale['idProduct']);

            !$inventory['days'] ? $days = 0 : $days = $inventory['days'];

            $resolution = $inventoryDaysDao->updateInventoryDays($dataSale['idProduct'], $days);
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
            $findProduct = $productsDao->findProduct($unitSales[$i], $id_company);
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
            $materials = $productMaterialsDao->findAllProductsmaterials($unitSales[$i]['idProduct'], $id_company);

            for ($i = 0; $i < sizeof($materials); $i++) {
                if (isset($resolution['info'])) break;

                // Calcular stock material
                $arr = $minimumStockDao->calcStockByMaterial($materials[$i]['id_material']);
                if (isset($arr['stock']))
                    $resolution = $generalMaterialDao->updateStockMaterial($materials[$i]['id_material'], $arr['stock']);

                if (isset($resolution['info'])) break;
                // Calcular stock producto
                $products = $generalProductsMaterialsDao->findAllProductByMaterial($materials[$i]['id_material']);

                foreach ($products as $arr) {
                    $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
                    if (isset($product['stock']))
                        $resolution = $productsDao->updateStockByProduct($arr['id_product'], $product['stock']);

                    if (isset($resolution['info'])) break;
                }
            }

            if (isset($resolution['info'])) break;
            // Calcular Dias inventario
            $inventory = $inventoryDaysDao->calcInventoryDays($unitSales[$i]['idProduct']);

            !$inventory['days'] ? $days = 0 : $days = $inventory['days'];

            $resolution = $inventoryDaysDao->updateInventoryDays($unitSales[$i]['idProduct'], $days);



            // $unitSales[$i]['cantMonths'] = 3;
            // $classification = $classificationDao->calcClassificationByProduct($unitSales[$i], $id_company);

            // Calcular Stock minimo
            // $minimumStock = $minimumStockDao->calcMinimumStock($unitSales[$i], $id_company);
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Venta importada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateUnitSale', function (Request $request, Response $response, $args) use (
    $unitSalesDao,
    $minimumStockDao,
    $productsDao,
    $generalMaterialDao,
    $productMaterialsDao,
    $generalProductsMaterialsDao,
    $inventoryDaysDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataSale = $request->getParsedBody();

    if (empty($dataSale['idSale']) || empty($dataSale['referenceProduct']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    else {
        $resolution = $unitSalesDao->updateSales($dataSale);

        if ($resolution == null) {
            // Calcular stock material
            $materials = $productMaterialsDao->findAllProductsmaterials($dataSale['idProduct'], $id_company);

            for ($i = 0; $i < sizeof($materials); $i++) {
                if (isset($resolution['info'])) break;

                $arr = $minimumStockDao->calcStockByMaterial($materials[$i]['id_material']);
                if (isset($arr['stock']))
                    $resolution = $generalMaterialDao->updateStockMaterial($materials[$i]['id_material'], $arr['stock']);

                // Calcular stock producto
                if (isset($resolution['info'])) break;
                $products = $generalProductsMaterialsDao->findAllProductByMaterial($materials[$i]['id_material']);

                foreach ($products as $arr) {
                    $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
                    if (isset($product['stock']))
                        $resolution = $productsDao->updateStockByProduct($arr['id_product'], $product['stock']);

                    if (isset($resolution['info'])) break;
                }
            }
        }

        if ($resolution == null) {
            // Calcular Dias inventario 
            $inventory = $inventoryDaysDao->calcInventoryDays($dataSale['idProduct']);

            !$inventory['days'] ? $days = 0 : $days = $inventory['days'];

            $resolution = $inventoryDaysDao->updateInventoryDays($dataSale['idProduct'], $days);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Venta actualizada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteUnitSale', function (Request $request, Response $response, $args) use (
    $unitSalesDao,
    $minimumStockDao,
    $generalMaterialDao,
    $productsDao,
    $productMaterialsDao,
    $generalProductsMaterialsDao,
    $inventoryDaysDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataSale = $request->getParsedBody();
    $resolution = $unitSalesDao->deleteSale($dataSale['idUnitSales']);

    if ($resolution == null) {
        $materials = $productMaterialsDao->findAllProductsmaterials($dataSale['idProduct'], $id_company);

        for ($i = 0; $i < sizeof($materials); $i++) {
            if (isset($resolution['info'])) break;

            // Calcular stock material
            $arr = $minimumStockDao->calcStockByMaterial($materials[$i]['id_material']);
            if (isset($arr['stock']))
                $resolution = $generalMaterialDao->updateStockMaterial($materials[$i]['id_material'], $arr['stock']);

            if (isset($resolution['info'])) break;
            // Calcular stock producto
            $products = $generalProductsMaterialsDao->findAllProductByMaterial($materials[$i]['id_material']);

            foreach ($products as $arr) {
                $product = $minimumStockDao->calcStockByProduct($arr['id_product']);
                if (isset($product['stock']))
                    $resolution = $productsDao->updateStockByProduct($arr['id_product'], $product['stock']);

                if (isset($resolution['info'])) break;
            }
        }
    }

    if ($resolution == null) {
        // Calcular Dias inventario 
        $inventory = $inventoryDaysDao->calcInventoryDays($dataSale['idProduct']);

        !$inventory['days'] ? $days = 0 : $days = $inventory['days'];

        $resolution = $inventoryDaysDao->updateInventoryDays($dataSale['idProduct'], $days);
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

    $response->getBody()->write(json_encode($saleDays, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/saveSaleDays', function (Request $request, Response $response, $args) use ($generalUnitSalesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataSales = $request->getParsedBody();

    $saleDay = $generalUnitSalesDao->findSaleDays($dataSales, $id_company);

    if (!$saleDay)
        $resolution = $generalUnitSalesDao->insertSaleDaysByCompany($dataSales, $id_company);
    else {
        $dataSales['idSaleDay'] = $saleDay['id_sale_day'];
        $resolution = $generalUnitSalesDao->updateSaleDays($dataSales);
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Dias de venta almacenada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible Guardar la información. intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
