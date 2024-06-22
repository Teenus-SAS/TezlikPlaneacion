<?php

use TezlikPlaneacion\dao\CiclesMachineDao;
use TezlikPlaneacion\dao\GeneralMachinesDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralPlanningMachinesDao;
use TezlikPlaneacion\dao\Planning_machinesDao;
use TezlikPlaneacion\dao\TimeConvertDao;

$planningMachinesDao = new Planning_machinesDao();
$generalPlanningMachinesDao = new GeneralPlanningMachinesDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$ciclesMachinesDao = new CiclesMachineDao();
$machinesDao = new GeneralMachinesDao();
$timeConvertDao = new TimeConvertDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/planningMachines', function (Request $request, Response $response, $args) use ($planningMachinesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $planningMachines = $planningMachinesDao->findAllPlanMachines($id_company);
    $response->getBody()->write(json_encode($planningMachines, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/planningMachine/{id_machine}', function (Request $request, Response $response, $args) use ($generalPlanningMachinesDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $data['idMachine'] = $args['id_machine'];
    $planningMachines = $generalPlanningMachinesDao->findPlanMachines($data, $id_company);
    $response->getBody()->write(json_encode($planningMachines, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/planningMachinesDataValidation', function (Request $request, Response $response, $args) use (
    $planningMachinesDao,
    $generalPlanningMachinesDao,
    $machinesDao
) {
    $dataPMachines = $request->getParsedBody();

    if (isset($dataPMachines)) {
        session_start();
        $id_company = $_SESSION['id_company'];

        $insert = 0;
        $update = 0;

        $planningMachines = $dataPMachines['importPlanMachines'];

        for ($i = 0; $i < sizeof($planningMachines); $i++) {
            if (
                $planningMachines[$i]['january'] > 31 || $planningMachines[$i]['february'] > 28 || $planningMachines[$i]['march'] > 31 || $planningMachines[$i]['april'] > 30 ||
                $planningMachines[$i]['may'] > 31 || $planningMachines[$i]['june'] > 30 || $planningMachines[$i]['july'] > 31 || $planningMachines[$i]['august'] > 31 ||
                $planningMachines[$i]['september'] > 30 ||  $planningMachines[$i]['october'] > 31 ||  $planningMachines[$i]['november'] > 30 ||  $planningMachines[$i]['december'] > 31
            ) {
                $i = $i + 2;
                $dataImportPlanMachines = array('error' => true, 'message' => "El valor es mayor al ultimo dia del mes<br>Fila: {$i}");
                break;
            }

            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($planningMachines[$i], $id_company);
            if (!$findMachine) {
                $i = $i + 2;
                $dataImportPlanMachines = array('error' => true, 'message' => "Maquina no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $planningMachines[$i]['idMachine'] = $findMachine['id_machine'];

            if (
                empty($planningMachines[$i]['numberWorkers']) || empty($planningMachines[$i]['hoursDay']) || empty($planningMachines[$i]['hourStart']) || empty($planningMachines[$i]['january']) || empty($planningMachines[$i]['february']) ||
                empty($planningMachines[$i]['march']) || empty($planningMachines[$i]['april']) || empty($planningMachines[$i]['may']) || empty($planningMachines[$i]['june']) || empty($planningMachines[$i]['july']) ||
                empty($planningMachines[$i]['august']) || empty($planningMachines[$i]['september']) ||  empty($planningMachines[$i]['october']) ||  empty($planningMachines[$i]['november']) ||  empty($planningMachines[$i]['december'])
            ) {
                $i = $i + 2;
                $dataImportPlanMachines = array('error' => true, 'message' => "Columna vacia en la fila: {$i}");
                break;
            }

            $findPlanMachines = $generalPlanningMachinesDao->findPlanMachines($planningMachines[$i], $id_company);
            if (!$findPlanMachines) $insert = $insert + 1;
            else $update = $update + 1;
            $dataImportPlanMachines['insert'] = $insert;
            $dataImportPlanMachines['update'] = $update;
        }
    } else $dataImportPlanMachines = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');
    $response->getBody()->write(json_encode($dataImportPlanMachines, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPlanningMachines', function (Request $request, Response $response, $args) use (
    $planningMachinesDao,
    $generalPlanningMachinesDao,
    $generalPlanCiclesMachinesDao,
    $ciclesMachinesDao,
    $timeConvertDao,
    $machinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPMachines = $request->getParsedBody();

    $dataPMachine =  sizeof($dataPMachines);

    if ($dataPMachine > 1) {
        $findPlanMachines = $generalPlanningMachinesDao->findPlanMachines($dataPMachines, $id_company);

        if (!$findPlanMachines) {
            $dataPMachine = $timeConvertDao->timeConverter($dataPMachines);
            $planningMachines = $planningMachinesDao->insertPlanMachinesByCompany($dataPMachine, $id_company);

            $machines = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachine($dataPMachine['idMachine']);

            foreach ($machines as $k) {
                if (isset($planningMachines['info'])) break;
                $data = [];
                $data['idCiclesMachine'] = $k['id_cicles_machine'];

                $arr = $ciclesMachinesDao->calcUnitsTurn($k['id_cicles_machine']);
                $data['units_turn'] = $arr['units_turn'];

                $arr = $ciclesMachinesDao->calcUnitsMonth($data, 1);
                $data['units_month'] = $arr['units_month'];

                $planningMachines = $generalPlanCiclesMachinesDao->updateUnits($data);
            }

            if ($planningMachines == null)
                $resp = array('success' => true, 'message' => 'Planeación de maquina creada correctamente');
            else if (isset($planningMachines['info']))
                $resp = array('info' => true, 'message' => $planningMachines['message']);
            else
                $resp = array('error' => true, 'message' => 'Ocurrio un problema al crear la planeación, intente nuevamente');
        } else
            $resp = array('error' => true, 'message' => 'Planeación de maquina existente. Ingrese nueva');
    } else {
        $planningMachines = $dataPMachines['importPlanMachines'];

        for ($i = 0; $i < sizeof($planningMachines); $i++) {
            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($planningMachines[$i], $id_company);
            $planningMachines[$i]['idMachine'] = $findMachine['id_machine'];

            $findPlanMachines = $generalPlanningMachinesDao->findPlanMachines($planningMachines[$i], $id_company);

            $hourEnd = $timeConvertDao->calculateHourEnd($planningMachines[$i]['hourStart'], $planningMachines[$i]['hoursDay']);

            $planningMachines[$i]['year'] = date('Y');
            $planningMachines[$i]['hourStart'] = date("G.i", strtotime($planningMachines[$i]['hourStart']));
            $planningMachines[$i]['hourEnd'] = $hourEnd;

            if (!$findPlanMachines) $resolution = $planningMachinesDao->insertPlanMachinesByCompany($planningMachines[$i], $id_company);
            else {
                $planningMachines[$i]['idProgramMachine'] = $findPlanMachines['id_program_machine'];
                $resolution = $planningMachinesDao->updatePlanMachines($planningMachines[$i]);
            }

            $machines = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachine($planningMachines[$i]['idMachine']);

            foreach ($machines as $k) {
                if (isset($resolution['info'])) break;
                $data = [];
                $data['idCiclesMachine'] = $k['id_cicles_machine'];

                $arr = $ciclesMachinesDao->calcUnitsTurn($k['id_cicles_machine']);
                $data['units_turn'] = $arr['units_turn'];

                $arr = $ciclesMachinesDao->calcUnitsMonth($data, 2);
                $data['units_month'] = $arr['units_month'];

                $resolution = $generalPlanCiclesMachinesDao->updateUnits($data);
            }
        }
        if ($resolution == null) $resp = array('success' => true, 'message' => 'Planeacion de maquina importada correctamente');
        else $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updatePlanningMachines', function (Request $request, Response $response, $args) use (
    $planningMachinesDao,
    $generalPlanningMachinesDao,
    $generalPlanCiclesMachinesDao,
    $ciclesMachinesDao,
    $timeConvertDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPMachines = $request->getParsedBody();

    $machine = $generalPlanningMachinesDao->findPlanMachines($dataPMachines, $id_company);
    !is_array($machine) ? $data['id_program_machine'] = 0 : $data = $machine;

    if ($data['id_program_machine'] == $dataPMachines['idProgramMachine'] || $data['id_program_machine'] == 0) {
        $dataPMachine = $timeConvertDao->timeConverter($dataPMachines);
        $planningMachines = $planningMachinesDao->updatePlanMachines($dataPMachine);

        $machines = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachine($dataPMachine['idMachine']);

        foreach ($machines as $k) {
            if (isset($planningMachines['info'])) break;
            $data = [];
            $data['idCiclesMachine'] = $k['id_cicles_machine'];

            $arr = $ciclesMachinesDao->calcUnitsTurn($k['id_cicles_machine']);
            $data['units_turn'] = $arr['units_turn'];

            $arr = $ciclesMachinesDao->calcUnitsMonth($data, 1);
            $data['units_month'] = $arr['units_month'];

            $planningMachines = $generalPlanCiclesMachinesDao->updateUnits($data);
        }

        if ($planningMachines == null)
            $resp = array('success' => true, 'message' => 'Planeación de maquina actualizada correctamente');
        else if (isset($planningMachines['info']))
            $resp = array('info' => true, 'message' => $planningMachines['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un problema al actualizar la planeación, intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Planeación de maquina existente. Ingrese nueva');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanningMachines/{id_program_machine}/{id}', function (Request $request, Response $response, $args) use (
    $planningMachinesDao,
    $generalPlanCiclesMachinesDao,
    $ciclesMachinesDao
) {
    $planningMachines = $planningMachinesDao->deletePlanMachines($args['id_program_machine']);

    $machines = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachine($args['id_machine']);

    foreach ($machines as $k) {
        if (isset($resolution['info'])) break;
        $data = [];
        $data['idCiclesMachine'] = $k['id_cicles_machine'];

        $arr = $ciclesMachinesDao->calcUnitsTurn($k['id_cicles_machine']);
        $data['units_turn'] = $arr['units_turn'];

        $arr = $ciclesMachinesDao->calcUnitsMonth($data, 1);
        $data['units_month'] = $arr['units_month'];

        $resolution = $generalPlanCiclesMachinesDao->updateUnits($data);
    }

    if ($planningMachines == null) $resp = array('success' => true, 'message' => 'Planeación de maquina eliminada correctamente');
    else $resp = array('error' => true, 'message' => 'No se pudo eliminar la planeación, existe información asociada a ella');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
