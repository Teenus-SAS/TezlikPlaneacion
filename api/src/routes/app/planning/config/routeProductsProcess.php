<?php

use TezlikPlaneacion\dao\ConvertDataDao;
use TezlikPlaneacion\dao\GeneralMachinesDao;
use TezlikPlaneacion\dao\GeneralProcessDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProductsProcessDao;
use TezlikPlaneacion\dao\MinimumStockDao;
use TezlikPlaneacion\dao\ProductsProcessDao;

$productsProcessDao = new ProductsProcessDao();
$generalProductsProcessDao = new GeneralProductsProcessDao();
$productsDao = new GeneralProductsDao();
$convertDataDao = new ConvertDataDao();
$processDao = new GeneralProcessDao();
$machinesDao = new GeneralMachinesDao();
$minimumStockDao = new MinimumStockDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Productos procesos
$app->get('/productsProcess/{idProduct}', function (Request $request, Response $response, $args) use (
    $productsProcessDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $productProcess = $productsProcessDao->findAllProductsprocess($args['idProduct'], $id_company);
    $response->getBody()->write(json_encode($productProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

// Consultar productos procesos importados
$app->post('/productsProcessDataValidation', function (Request $request, Response $response, $args) use (
    $generalProductsProcessDao,
    $productsDao,
    $processDao,
    $machinesDao
) {
    $dataProductProcess = $request->getParsedBody();

    if (isset($dataProductProcess)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $productProcess = $dataProductProcess['importProductsProcess'];

        for ($i = 0; $i < sizeof($productProcess); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($productProcess[$i], $id_company);
            if (!$findProduct) {
                $i = $i + 2;
                $dataImportProductProcess = array('error' => true, 'message' => "Producto no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $productProcess[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id proceso $processPayrollDao->findProcessByPayroll($productProcess[$i], $id_company);
            $findProcess = $processDao->findProcess($productProcess[$i], $id_company);
            if (!$findProcess) {
                $i = $i + 2;
                $dataImportProductProcess = array('error' => true, 'message' => "Proceso no existe en la base de datos<br>Fila: {$i}");
                break;
            } else
                $productProcess[$i]['idProcess'] = $findProcess['id_process'];

            // Obtener id maquina
            // Si no está definida agrega 0 a 'idMachine'
            if (!isset($productProcess[$i]['machine'])) {
                $productProcess[$i]['idMachine'] = 0;
            } else {
                $findMachine = $machinesDao->findMachine($productProcess[$i], $id_company);
                if (!$findMachine) {
                    $i = $i + 2;
                    $dataImportProductProcess = array('error' => true, 'message' => "Maquina no existe en la base de datos <br>Fila: {$i}");
                    break;
                } else $productProcess[$i]['idMachine'] = $findMachine['id_machine'];
            }

            //tiempo de alistamiento = 0 si no está definido
            if (!isset($productProcess[$i]['enlistmentTime'])) {
                $productProcess[$i]['enlistmentTime'] = 0;
            }

            //Tiempo de operación = 0 si no está definido
            if (!isset($productProcess[$i]['operationTime'])) {
                $productProcess[$i]['operationTime'] = 0;
            }

            $findProductProcess = $generalProductsProcessDao->findProductProcess($productProcess[$i], $id_company);

            if (!$findProductProcess) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportProductProcess['insert'] = $insert;
            $dataImportProductProcess['update'] = $update;
        }
    } else
        $dataImportProductProcess = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportProductProcess, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addProductsProcess', function (Request $request, Response $response, $args) use (
    $productsProcessDao,
    $generalProductsProcessDao,
    $convertDataDao,
    $productsDao,
    $processDao,
    $machinesDao,
    $minimumStockDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataProductProcess = $request->getParsedBody();

    $dataProductsProcess = sizeof($dataProductProcess);

    if ($dataProductsProcess > 1) {
        $dataProductProcess = $convertDataDao->strReplaceProductsProcess($dataProductProcess);
        $productProcess = $productsProcessDao->insertProductsProcessByCompany($dataProductProcess, $id_company);

        if ($productProcess == null)
            $resp = array('success' => true, 'message' => 'Proceso asignado correctamente');
        elseif ($productProcess == 1)
            $resp = array('error' => true, 'message' => 'El Proceso ya se encuentra en la Base de Datos');
        else if (isset($productProcess['info']))
            $resp = array('info' => true, 'message' => $productProcess['message']);
        else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras asignaba la información. Intente nuevamente');
        }
    } else {
        $productProcess = $dataProductProcess['importProductsProcess'];

        for ($i = 0; $i < sizeof($productProcess); $i++) {
            // Obtener id producto
            $findProduct = $productsDao->findProduct($productProcess[$i], $id_company);
            $productProcess[$i]['idProduct'] = $findProduct['id_product'];

            // Obtener id proceso
            $findProcess = $processDao->findProcess($productProcess[$i], $id_company);
            $productProcess[$i]['idProcess'] = $findProcess['id_process'];

            // Obtener id maquina
            // Si no está definida agrega 0 a 'idMachine'
            if (!isset($productProcess[$i]['machine'])) {
                $productProcess[$i]['idMachine'] = 0;
            } else {
                // Obtener id maquina
                $findMachine = $machinesDao->findMachine($productProcess[$i], $id_company);
                $productProcess[$i]['idMachine'] = $findMachine['id_machine'];
            }

            //consultar si existe producto_proceso en bd
            //false = no, id_product_process = si
            $findProductProcess = $generalProductsProcessDao->findProductProcess($productProcess[$i], $id_company);

            $productProcess[$i] = $convertDataDao->strReplaceProductsProcess($productProcess[$i]);

            if (!$findProductProcess) {
                //si no se encuentra, inserta y retorna null, si se encuentra retorna 1
                $resolution = $productsProcessDao->insertProductsProcessByCompany($productProcess[$i], $id_company);

                if ($resolution == 1) {
                    $i = $i + 2;
                    $resp = array('error' => true, 'message' => "El Proceso ya se encuentra en la Base de Datos<br>Fila: {$i}");
                    break;
                } else $productProcess[$i]['idProduct'] = $findProduct['id_product'];
            } else {
                $productProcess[$i]['idProductProcess'] = $findProductProcess['id_product_process'];
                $resolution = $productsProcessDao->updateProductsProcess($productProcess[$i]);
            }

            // Calcular lote economico
            // $minimumStock = $minimumStockDao->calcMinimumStock($productProcess[$i], $id_company);
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Proceso importado correctamente');
        else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
        }
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanProductsProcess', function (Request $request, Response $response, $args) use (
    $productsProcessDao,
    $convertDataDao
) {
    $dataProductProcess = $request->getParsedBody();

    if (empty($dataProductProcess['idProduct'] || empty($dataProductProcess['idProcess']) || empty($dataProductProcess['idMachine']) || empty($dataProductProcess['enlistmentTime']) || empty($dataProductProcess['operationTime'])))
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos');
    else {
        $dataProductProcess = $convertDataDao->strReplaceProductsProcess($dataProductProcess);

        $productProcess = $productsProcessDao->updateProductsProcess($dataProductProcess);

        if ($productProcess == null)
            $resp = array('success' => true, 'message' => 'Proceso actualizado correctamente');
        else if (isset($productProcess['info']))
            $resp = array('info' => true, 'message' => $productProcess['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanProductProcess/{id_product_process}', function (Request $request, Response $response, $args) use ($productsProcessDao) {
    session_start();

    $product = $productsProcessDao->deleteProductProcess($args['id_product_process']);

    if ($product == null)
        $resp = array('success' => true, 'message' => 'Proceso eliminado correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar el proceso asignado, existe información asociada a él');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
