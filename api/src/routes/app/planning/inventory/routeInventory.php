<?php

use TezlikPlaneacion\dao\InventoryDao;
use TezlikPlaneacion\dao\InvMoldsDao;
use TezlikPlaneacion\dao\ClassificationDao;
use TezlikPlaneacion\dao\GeneralCategoriesDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralUnitSalesDao;
use TezlikPlaneacion\Dao\MagnitudesDao;
use TezlikPlaneacion\dao\MaterialsDao;
use TezlikPlaneacion\dao\ProductsDao;
use TezlikPlaneacion\dao\UnitsDao;

$inventoryDao = new InventoryDao();
$categoriesDao = new GeneralCategoriesDao();
$moldsDao = new InvMoldsDao();
$magnitudesDao = new MagnitudesDao();
$unitsDao = new UnitsDao();
$productsDao = new ProductsDao();
$generalProductsDao = new GeneralProductsDao();
$unitSalesDao = new GeneralUnitSalesDao();
$materialsDao = new MaterialsDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$classificationDao = new ClassificationDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/inventory', function (Request $request, Response $response, $args) use (
    $productsDao,
    $materialsDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // Productos
    $products = $productsDao->findAllProductsByCompany($id_company);
    // Materiales
    $rawMaterials = $materialsDao->findAllMaterialsByCompany($id_company);

    $inventory['products'] = $products;
    $inventory['rawMaterials'] = $rawMaterials;

    $response->getBody()->write(json_encode($inventory));
    return $response->withHeader('Content-Type', 'application/json');
});

// $app->post('/inventoryDataValidation', function (Request $request, Response $response, $args) use (
//     $generalProductsDao,
//     $generalMaterialsDao,
//     $magnitudesDao,
//     $unitsDao
// ) {
//     $dataInventory = $request->getParsedBody();

//     if (isset($dataInventory)) {
//         session_start();
//         $id_company = $_SESSION['id_company'];

//         $update = 0;

//         $inventory = $dataInventory['importInventory'];

//         for ($i = 0; $i < sizeof($inventory); $i++) {
//             if (
//                 empty($inventory[$i]['reference']) || empty($inventory[$i]['nameInventory']) ||
//                 empty($inventory[$i]['quantity']) || empty($inventory[$i]['category'])
//             ) {
//                 $i = $i + 2;
//                 $dataImportinventory = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
//                 break;
//             }
//             if (
//                 trim(empty($inventory[$i]['reference'])) || trim(empty($inventory[$i]['nameInventory'])) ||
//                 trim(empty($inventory[$i]['quantity'])) || trim(empty($inventory[$i]['category']))
//             ) {
//                 $i = $i + 2;
//                 $dataImportinventory = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
//                 break;
//             }

//             $category = $inventory[$i]['category'];

//             if ($category == 'Materiales') {
//                 if (
//                     empty($inventory[$i]['magnitude']) || empty($inventory[$i]['unit'])
//                 ) {
//                     $i = $i + 2;
//                     $dataImportinventory = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
//                     break;
//                 }

//                 if (
//                     trim(empty($inventory[$i]['magnitude'])) || trim(empty($inventory[$i]['unit']))
//                 ) {
//                     $i = $i + 2;
//                     $dataImportinventory = array('error' => true, 'message' => "Campos vacios en la fila: {$i}");
//                     break;
//                 }

//                 // Consultar magnitud
//                 $magnitude = $magnitudesDao->findMagnitude($inventory[$i]);

//                 if (!$magnitude) {
//                     $i = $i + 2;
//                     $dataImportinventory = array('error' => true, 'message' => "Magnitud no existe en la base de datos. Fila: $i");
//                     break;
//                 }
//                 // Consultar unidad
//                 $unit = $unitsDao->findUnit($inventory[$i]);

//                 if (!$unit) {
//                     $i = $i + 2;
//                     $dataImportinventory = array('error' => true, 'message' => "Unidad no existe en la base de datos. Fila: $i");
//                     break;
//                 }

//                 $findMaterial = $generalMaterialsDao->findMaterial($inventory[$i], $id_company);
//                 if (!$findMaterial) $insert = $insert + 1;
//                 else $update = $update + 1;
//             }
//         }
//         $dataImportinventory['update'] = $update;
//         // Almacenar inventarios existentes
//         $_SESSION['dataImportInventory'] = $inventory;
//     } else
//         $dataImportinventory = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

//     $response->getBody()->write(json_encode($dataImportinventory, JSON_NUMERIC_CHECK));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/addInventory', function (Request $request, Response $response, $args) use (
//     $productsDao,
//     $generalProductsDao,
//     $materialsDao,
//     $generalMaterialsDao,
//     $classificationDao
// ) {
//     session_start();
//     $id_company = $_SESSION['id_company'];

//     $inventory = $_SESSION['dataImportInventory'];

//     for ($i = 0; $i < sizeof($inventory); $i++) {
//         $category = $inventory[$i]['category'];
//         // Producto
//         if ($category == 'Productos') {
//             $findProduct = $generalProductsDao->findProduct($inventory[$i], $id_company);

//             $resolution = $productsDao->updateProductByCompany($inventory[$i], $id_company);

//             // Calcular clasificación
//             $inventory[$i]['cantMonths'] = 3;
//             $classification = $classificationDao->calcClassificationByProduct($inventory[$i], $id_company);
//         }

//         // Materia prima y Insumos
//         if ($category == 'Materiales') {
//             $findMaterial = $generalMaterialsDao->findMaterial($inventory[$i], $id_company);
//             $inventory[$i]['idMaterial'] = $findMaterial['id_material'];

//             $resolution = $materialsDao->updateMaterialsByCompany($inventory[$i]);
//         }
//     }
//     if ($resolution == null)
//         $resp = array('success' => true, 'message' => 'Inventario importado correctamente');
//     else
//         $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });

$app->get('/deleteInventorySession', function (Request $request, Response $response, $args) {
    //Eliminar variable session
    session_start();
    unset($_SESSION['dataImportInventory']);
    $response->getBody()->write(json_encode(JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
