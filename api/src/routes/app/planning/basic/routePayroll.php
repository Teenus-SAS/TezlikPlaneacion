<?php

use TezlikPlaneacion\dao\GeneralAreaDao;
use TezlikPlaneacion\dao\GeneralPayrollDao;
use TezlikPlaneacion\dao\GeneralProcessDao;
use TezlikPlaneacion\dao\PayrollDao;

$payrollDao = new PayrollDao();
$generalPayrollDao = new GeneralPayrollDao();
$generalProcessDao = new GeneralProcessDao();
$generalAreaDao = new GeneralAreaDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/planPayroll', function (Request $request, Response $response, $args) use ($payrollDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $payroll = $payrollDao->findAllPayrollByCompany($id_company);
    $response->getBody()->write(json_encode($payroll));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar productos importados */
$app->post('/payrollDataValidation', function (Request $request, Response $response, $args) use (
    $generalPayrollDao,
    $generalProcessDao,
    $generalAreaDao
) {
    $dataPayroll = $request->getParsedBody();

    if (isset($dataPayroll)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $payroll = $dataPayroll['importPayroll'];

        $dataImportPayroll = [];

        for ($i = 0; $i < count($payroll); $i++) {
            if (
                empty($payroll[$i]['firstname']) || empty($payroll[$i]['lastname']) ||
                empty($payroll[$i]['position']) || empty($payroll[$i]['process']) ||
                empty($payroll[$i]['area'])
            ) {
                $i = $i + 2;
                $dataImportPayroll = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }
            if (
                empty(trim($payroll[$i]['firstname'])) || empty(trim($payroll[$i]['lastname'])) ||
                empty(trim($payroll[$i]['position'])) || empty(trim($payroll[$i]['process'])) ||
                empty(trim($payroll[$i]['area']))
            ) {
                $i = $i + 2;
                $dataImportPayroll = array('error' => true, 'message' => "Campos vacios, fila: $i");
                break;
            }
        }

        $insert = 0;
        $update = 0;

        if (sizeof($dataImportPayroll) == 0) {
            for ($i = 0; $i < count($payroll); $i++) {
                // Obtener proceso
                $findProcess = $generalProcessDao->findProcess($payroll[$i], $id_company);

                if (!$findProcess) {
                    $i = $i + 2;
                    $dataImportPayroll =  array('error' => true, 'message' => "Proceso no existe en la base de datos. Fila: $i");
                    break;
                }
                $payroll[$i]['idProcess'] = $findProcess['id_process'];
                // Obtener area
                $findArea = $generalAreaDao->findArea($payroll[$i], $id_company);

                if (!$findArea) {
                    $i = $i + 2;
                    $dataImportPayroll =  array('error' => true, 'message' => "Area no existe en la base de datos. Fila: $i");
                    break;
                }
                $payroll[$i]['idArea'] = $findArea['id_plan_area'];

                $findPayroll = $generalPayrollDao->findPayroll($payroll[$i], $id_company);

                if (!$findPayroll)
                    $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportPayroll['insert'] = $insert;
                $dataImportPayroll['update'] = $update;
            }
        }
    } else
        $dataImportPayroll = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportPayroll, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPayroll', function (Request $request, Response $response, $args) use (
    $payrollDao,
    $generalPayrollDao,
    $generalProcessDao,
    $generalAreaDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPayroll = $request->getParsedBody();

    /* Inserta datos */
    $dataPayrolls = sizeof($dataPayroll);

    if ($dataPayrolls > 1) {
        $findPayroll = $generalPayrollDao->findPayroll($dataPayroll, $id_company);

        if (!$findPayroll) {
            $resolution = $payrollDao->insertPayrollByCompany($dataPayroll, $id_company);

            if ($resolution == null)
                $resp = array('success' => true, 'message' => 'Nomina creada correctamente');
            else if (isset($resolution['info']))
                $resp = array('info' => true, 'message' => $resolution['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrió un error mientras ingresaba la información. Intente nuevamente');
        } else {
            $resp = array('info' => true, 'message' => 'Nomina ya existente en la base de datos. Ingrese uno nuevo');
        }
    } else {
        $payroll = $dataPayroll['importPayroll'];

        for ($i = 0; $i < sizeof($payroll); $i++) {
            // Obtener proceso
            $findProcess = $generalProcessDao->findProcess($payroll[$i], $id_company);
            $payroll[$i]['idProcess'] = $findProcess['id_process'];

            // Obtener area
            $findArea = $generalAreaDao->findArea($payroll[$i], $id_company);
            $payroll[$i]['idArea'] = $findArea['id_plan_area'];

            $findPayroll = $generalPayrollDao->findPayroll($payroll[$i], $id_company);
            if (!$findPayroll) {
                $resolution = $payrollDao->insertPayrollByCompany($payroll[$i], $id_company);
            } else {
                $payroll[$i]['idPayroll'] = $findPayroll['id_plan_payroll'];
                $resolution = $payrollDao->updatePayroll($payroll[$i]);
            }

            if (isset($resolution['info'])) break;
        }

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Nomina de produccion importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras importaba los datos. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePayroll', function (Request $request, Response $response, $args) use (
    $payrollDao,
    $generalPayrollDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPayroll = $request->getParsedBody();

    $payroll = $generalPayrollDao->findPayroll($dataPayroll, $id_company);
    !is_array($payroll) ? $data['id_plan_payroll'] = 0 : $data = $payroll;

    if ($data['id_plan_payroll'] == $dataPayroll['idPayroll'] || $data['id_plan_payroll'] == 0) {
        $resolution = $payrollDao->updatePayroll($dataPayroll);

        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Nomina modificada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrió un error mientras guardaba los datos. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Nomina ya existente. Ingrese una nueva');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePayroll/{id_plan_payroll}', function (Request $request, Response $response, $args) use ($payrollDao) {
    $resolution = $payrollDao->deletePayroll($args['id_plan_payroll']);

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Produccion eliminada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras eliminaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});