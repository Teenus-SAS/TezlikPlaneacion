<?php

use TezlikPlaneacion\dao\GeneralMachinesDao;
use TezlikPlaneacion\dao\MachinesDao;

$machinesDao = new MachinesDao();
$generalMachinesDao = new GeneralMachinesDao();

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

        for ($i = 0; $i < sizeof($machines); $i++) {

            if (empty($machines[$i]['machine'])) {
                $i = $i + 1;
                $dataImportMachine = array('error' => true, 'message' => "Campos vacios. Fila: {$i}");
                break;
            } else {
                $findMachine = $generalMachinesDao->findMachine($machines[$i], $id_company);
                if (!$findMachine) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportMachine['insert'] = $insert;
                $dataImportMachine['update'] = $update;
            }
        }
    } else
        $dataImportMachine = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $response->getBody()->write(json_encode($dataImportMachine, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});


/* Agregar Maquinas */
$app->post('/addPlanMachines', function (Request $request, Response $response, $args) use (
    $machinesDao,
    $generalMachinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataMachine = $request->getParsedBody();

    if (empty($dataMachine['importMachines'])) {
        $findMachine = $generalMachinesDao->findMachine($dataMachine, $id_company);

        if (!$findMachine) {
            $machines = $machinesDao->insertMachinesByCompany($dataMachine, $id_company);

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

            if (!$machine) {
                $resolution = $machinesDao->insertMachinesByCompany($machines[$i], $id_company);
                if (isset($resolution['info'])) break;
            } else {
                $machines[$i]['idMachine'] = $machine['id_machine'];
                $resolution = $machinesDao->updateMachine($machines[$i]);
            }
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
    $generalMachinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $dataMachine = $request->getParsedBody();

    $machine = $generalMachinesDao->findMachine($dataMachine, $id_company);
    !is_array($machine) ? $data['id_machine'] = 0 : $data = $machine;

    if ($data['id_machine'] == $dataMachine['idMachine'] || $data['id_machine'] == 0) {
        $machines = $machinesDao->updateMachine($dataMachine);

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
    if ($machines != null)
        $resp = array('error' => true, 'message' => 'No es posible eliminar la maquina, existe información asociada a él');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
