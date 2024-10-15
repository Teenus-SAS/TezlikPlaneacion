<?php

use TezlikPlaneacion\dao\GeneralMachinesDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\MachinesDao;
use TezlikPlaneacion\dao\MinuteDepreciationDao;

$machinesDao = new MachinesDao();
$generalMachinesDao = new GeneralMachinesDao();
$minuteDepreciationDao = new MinuteDepreciationDao();
$lastDataDao = new LastDataDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/machines', function (Request $request, Response $response, $args) use ($machinesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $machines = $machinesDao->findAllMachinesByCompany($id_company);
    $response->getBody()->write(json_encode($machines, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Consultar Maquinas importadas */
$app->post('/machinesDataValidation', function (Request $request, Response $response, $args) use (
    $generalMachinesDao
) {
    $dataMachine = $request->getParsedBody();

    if (isset($dataMachine)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $machines = $dataMachine['importMachines'];
        $debugg = [];
        $dataImportMachine = [];

        for ($i = 0; $i < sizeof($machines); $i++) {
            if (
                empty(trim($machines[$i]['machine'])) ||
                empty(trim($machines[$i]['cost'])) ||
                empty(trim($machines[$i]['depreciationYears'])) ||
                empty(trim($machines[$i]['hoursMachine'])) ||
                empty(trim($machines[$i]['daysMachine']))
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila-$row"));
            }

            $machines[$i]['cost'] = str_replace(',', '.', $machines[$i]['cost']);
            $machines[$i]['depreciationYears'] = str_replace(',', '.', $machines[$i]['depreciationYears']);
            $machines[$i]['hoursMachine'] = str_replace(',', '.', $machines[$i]['hoursMachine']);
            $machines[$i]['daysMachine'] = str_replace(',', '.', $machines[$i]['daysMachine']);

            $data = floatval($machines[$i]['cost']) * floatval($machines[$i]['depreciationYears']) * floatval($machines[$i]['hoursMachine']) * floatval($machines[$i]['daysMachine']);

            if ($data <= 0 || is_nan($data)) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Campos vacios, fila-$row"));
            }

            if ($machines[$i]['hoursMachine'] > 24) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Las horas de trabajo no pueden ser mayor a 24, fila-$row"));
            }

            if ($machines[$i]['daysMachine'] > 31) {
                $row = $i + 2;
                $dataImportMachine = array('error' => true, 'message' => "Los dias de trabajo no pueden ser mayor a 31, fila-$row");
            }

            if (sizeof($debugg) > 0) {
                $findMachine = $generalMachinesDao->findMachine($machines[$i], $id_company);
                if (!$findMachine) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportMachine['insert'] = $insert;
                $dataImportMachine['update'] = $update;
            }
        }
    } else
        $dataImportMachine = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportMachine;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Agregar Maquinas */
$app->post('/addPlanMachines', function (Request $request, Response $response, $args) use (
    $machinesDao,
    $generalMachinesDao,
    $minuteDepreciationDao,
    $lastDataDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    if (empty($dataMachine['importMachines'])) {
        $findMachine = $generalMachinesDao->findMachine($dataMachine, $id_company);

        if (!$findMachine) {
            $machines = $machinesDao->insertMachinesByCompany($dataMachine, $id_company);

            if ($machines == null) {
                $lastMachine = $lastDataDao->lastInsertedMachineId($id_company);

                // Calcular depreciacion por minuto
                $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationByMachine($lastMachine['id_machine']);

                // Modificar depreciacion x minuto
                $dataMachine['idMachine'] = $lastMachine['id_machine'];
                $dataMachine['minuteDepreciation'] = $minuteDepreciation;
                $machines = $minuteDepreciationDao->updateMinuteDepreciation($dataMachine, $id_company);
            }

            if ($machines == null)
                $resp = array('success' => true, 'message' => 'Maquina creada correctamente');
            else if (isset($machines['info']))
                $resp = array('info' => true, 'message' => $machines['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Maquina ya existe. Ingrese una nueva');
    } else {
        $machines = $dataMachine['importMachines'];

        for ($i = 0; $i < sizeof($machines); $i++) {
            $machine = $generalMachinesDao->findMachine($machines[$i], $id_company);

            $machines[$i]['cost'] = str_replace(',', '.', $machines[$i]['cost']);
            $machines[$i]['depreciationYears'] = str_replace(',', '.', $machines[$i]['depreciationYears']);
            $machines[$i]['hoursMachine'] = str_replace(',', '.', $machines[$i]['hoursMachine']);
            $machines[$i]['daysMachine'] = str_replace(',', '.', $machines[$i]['daysMachine']);

            if (!$machine) {
                $resolution = $machinesDao->insertMachinesByCompany($machines[$i], $id_company);
                $lastMachine = $lastDataDao->lastInsertedMachineId($id_company);
                $machines[$i]['idMachine'] = $lastMachine['id_machine'];
            } else {
                $machines[$i]['idMachine'] = $machine['id_machine'];
                $resolution = $machinesDao->updateMachine($machines[$i]);
            }
            if (isset($resolution['info'])) break;

            // Calcular depreciacion por minuto
            $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationByMachine($machines[$i]['idMachine']);

            // Modificar depreciacion x minuto 
            $machines[$i]['minuteDepreciation'] = $minuteDepreciation;
            $resolution = $minuteDepreciationDao->updateMinuteDepreciation($machines[$i], $id_company);

            if (isset($resolution['info'])) break;
        }
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Maquina Importada correctamente');
        else if ($resolution['info'] == 'true')
            $resp = $resp = array('info' => true, 'message' => 'No pueden existir máquinas con el mismo nombre. Modifiquelas y vuelva a intentarlo');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Actualizar Maquina */
$app->post('/updatePlanMachines', function (Request $request, Response $response, $args) use (
    $machinesDao,
    $generalMachinesDao,
    $minuteDepreciationDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataMachine = $request->getParsedBody();

    $machine = $generalMachinesDao->findMachine($dataMachine, $id_company);
    !is_array($machine) ? $data['id_machine'] = 0 : $data = $machine;

    if ($data['id_machine'] == $dataMachine['idMachine'] || $data['id_machine'] == 0) {
        $machines = $machinesDao->updateMachine($dataMachine);

        if ($machines == null) {
            // Calcular depreciacion por minuto
            $minuteDepreciation = $minuteDepreciationDao->calcMinuteDepreciationByMachine($dataMachine['idMachine']);

            // Modificar depreciacion x minuto 
            $dataMachine['minuteDepreciation'] = $minuteDepreciation;
            $machines = $minuteDepreciationDao->updateMinuteDepreciation($dataMachine, $id_company);
        }

        if ($machines == null)
            $resp = array('success' => true, 'message' => 'Maquina actualizada correctamente');
        else if (isset($machines['info']))
            $resp = array('info' => true, 'message' => $machines['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Maquina ya existe. Ingrese una nueva');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


/* Eliminar Maquina */
$app->get('/deletePlanMachine/{id_machine}', function (Request $request, Response $response, $args) use ($machinesDao) {
    $machines = $machinesDao->deleteMachine($args['id_machine']);

    if ($machines == null)
        $resp = array('success' => true, 'message' => 'Maquina eliminada correctamente');
    else if (isset($machines['info']))
        $resp = array('info' => true, 'message' => $machines['message']);
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la maquina, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
