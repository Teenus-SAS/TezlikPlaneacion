<?php

use TezlikPlaneacion\dao\GeneralMachinesDao;
use TezlikPlaneacion\dao\GeneralPlanningMachinesDao;
use TezlikPlaneacion\dao\Planning_machinesDao;
use TezlikPlaneacion\dao\TimeConvertDao;

$planningMachinesDao = new Planning_machinesDao();
$generalPlanningMachinesDao = new GeneralPlanningMachinesDao();
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
                $i = $i + 1;
                $dataImportPlanMachines = array('error' => true, 'message' => "El valor es mayor al ultimo dia del mes<br>Fila: {$i}");
                break;
            }

            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($planningMachines[$i], $id_company);
            if (!$findMachine) {
                $i = $i + 1;
                $dataImportPlanMachines = array('error' => true, 'message' => "Maquina no existe en la base de datos<br>Fila: {$i}");
                break;
            } else $planningMachines[$i]['idMachine'] = $findMachine['id_machine'];

            if (
                empty($planningMachines[$i]['numberWorkers']) || empty($planningMachines[$i]['hoursDay']) || empty($planningMachines[$i]['hourStart']) || empty($planningMachines[$i]['hourEnd']) || empty($planningMachines[$i]['january']) || empty($planningMachines[$i]['february']) ||
                empty($planningMachines[$i]['march']) || empty($planningMachines[$i]['april']) || empty($planningMachines[$i]['may']) || empty($planningMachines[$i]['june']) || empty($planningMachines[$i]['july']) ||
                empty($planningMachines[$i]['august']) || empty($planningMachines[$i]['september']) ||  empty($planningMachines[$i]['october']) ||  empty($planningMachines[$i]['november']) ||  empty($planningMachines[$i]['december'])
            ) {
                $i = $i + 1;
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
    $timeConvertDao,
    $machinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPMachines = $request->getParsedBody();

    $dataPMachine =  sizeof($dataPMachines);

    if ($dataPMachine > 1) {
        $dataPMachine = $timeConvertDao->timeConverter($dataPMachines);
        $planningMachines = $planningMachinesDao->insertPlanMachinesByCompany($dataPMachine, $id_company);

        if ($planningMachines == null)
            $resp = array('success' => true, 'message' => 'Planeación de maquina creada correctamente');
        else if (isset($planningMachines['info']))
            $resp = array('info' => true, 'message' => $planningMachines['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un problema al crear la planeación, intente nuevamente');
    } else {
        $planningMachines = $dataPMachines['importPlanMachines'];

        for ($i = 0; $i < sizeof($planningMachines); $i++) {
            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($planningMachines[$i], $id_company);
            $planningMachines[$i]['idMachine'] = $findMachine['id_machine'];

            $findPlanMachines = $generalPlanningMachinesDao->findPlanMachines($planningMachines[$i], $id_company);

            $planningMachines[$i] = $timeConvertDao->timeConverter($planningMachines[$i]);

            if (!$findPlanMachines) $resolution = $planningMachinesDao->insertPlanMachinesByCompany($planningMachines[$i], $id_company);
            else {
                $planningMachines[$i]['idProgramMachine'] = $findPlanMachines['id_program_machine'];
                $resolution = $planningMachinesDao->updatePlanMachines($planningMachines[$i]);
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
    $timeConvertDao
) {
    $dataPMachines = $request->getParsedBody();

    if (
        empty($dataPMachines['idProgramMachine']) || empty($dataPMachines['idMachine']) || empty($dataPMachines['numberWorkers']) ||
        empty($dataPMachines['hoursDay']) || empty($dataPMachines['hourStart']) || empty($dataPMachines['hourEnd'])
    )
        $resp = array('error' => true, 'message' => 'No hubo ningún cambio');
    else {
        $dataPMachine = $timeConvertDao->timeConverter($dataPMachines);
        $planningMachines = $planningMachinesDao->updatePlanMachines($dataPMachine);

        if ($planningMachines == null)
            $resp = array('success' => true, 'message' => 'Planeación de maquina actualizada correctamente');
        else if (isset($planningMachines['info']))
            $resp = array('info' => true, 'message' => $planningMachines['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un problema al actualizar la planeación, intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deletePlanningMachines/{id_program_machine}', function (Request $request, Response $response, $args) use (
    $planningMachinesDao
) {
    $planningMachines = $planningMachinesDao->deletePlanMachines($args['id_program_machine']);

    if ($planningMachines == null) $resp = array('success' => true, 'message' => 'Planeación de maquina eliminada correctamente');
    else $resp = array('error' => true, 'message' => 'No se pudo eliminar la planeación, existe información asociada a ella');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
