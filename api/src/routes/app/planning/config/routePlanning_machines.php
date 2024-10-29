<?php

use TezlikPlaneacion\dao\CiclesMachineDao;
use TezlikPlaneacion\Dao\CompositeProductsDao;
use TezlikPlaneacion\dao\GeneralClientsDao;
use TezlikPlaneacion\dao\GeneralMachinesDao;
use TezlikPlaneacion\dao\GeneralMaterialsDao;
use TezlikPlaneacion\dao\GeneralOrdersDao;
use TezlikPlaneacion\dao\GeneralPayrollDao;
use TezlikPlaneacion\dao\GeneralPlanCiclesMachinesDao;
use TezlikPlaneacion\dao\GeneralPlanningMachinesDao;
use TezlikPlaneacion\dao\GeneralProductsDao;
use TezlikPlaneacion\dao\GeneralProgrammingDao;
use TezlikPlaneacion\dao\GeneralRequisitionsProductsDao;
use TezlikPlaneacion\dao\LastDataDao;
use TezlikPlaneacion\dao\Planning_machinesDao;
use TezlikPlaneacion\dao\ProductsMaterialsDao;
use TezlikPlaneacion\dao\TimeConvertDao;

$planningMachinesDao = new Planning_machinesDao();
$generalPayrollDao = new GeneralPayrollDao();
$generalOrdersDao = new GeneralOrdersDao();
$productsMaterialsDao = new ProductsMaterialsDao();
$compositeProductsDao = new CompositeProductsDao();
$generalOrdersDao = new GeneralOrdersDao();
$productsDao = new GeneralProductsDao();
$generalProgrammingDao = new GeneralProgrammingDao();
$generalMaterialsDao = new GeneralMaterialsDao();
$generalClientsDao = new GeneralClientsDao();
$generalRequisitionsProductsDao = new GeneralRequisitionsProductsDao();
$generalPlanningMachinesDao = new GeneralPlanningMachinesDao();
$generalPlanCiclesMachinesDao = new GeneralPlanCiclesMachinesDao();
$ciclesMachinesDao = new CiclesMachineDao();
$lastDataDao = new LastDataDao();
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
        $dataImportPlanMachines = [];
        $debugg = [];

        for ($i = 0; $i < sizeof($planningMachines); $i++) {
            if (
                empty($planningMachines[$i]['type']) ||
                empty($planningMachines[$i]['workShift']) ||
                empty($planningMachines[$i]['hoursDay']) ||
                empty($planningMachines[$i]['hourStart']) ||
                empty($planningMachines[$i]['january']) ||
                empty($planningMachines[$i]['february']) ||
                empty($planningMachines[$i]['march']) ||
                empty($planningMachines[$i]['april']) ||
                empty($planningMachines[$i]['may']) ||
                empty($planningMachines[$i]['june']) ||
                empty($planningMachines[$i]['july']) ||
                empty($planningMachines[$i]['august']) ||
                empty($planningMachines[$i]['september']) ||
                empty($planningMachines[$i]['october']) ||
                empty($planningMachines[$i]['november']) ||
                empty($planningMachines[$i]['december']) ||
                empty($planningMachines[$i]['active'])
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Columna vacia"));
            }
            if (
                empty(trim($planningMachines[$i]['type'])) ||
                empty(trim($planningMachines[$i]['workShift'])) ||
                empty(trim($planningMachines[$i]['hoursDay'])) ||
                empty(trim($planningMachines[$i]['hourStart'])) ||
                empty(trim($planningMachines[$i]['january'])) ||
                empty(trim($planningMachines[$i]['february'])) ||
                empty(trim($planningMachines[$i]['march'])) ||
                empty(trim($planningMachines[$i]['april'])) ||
                empty(trim($planningMachines[$i]['may'])) ||
                empty(trim($planningMachines[$i]['june'])) ||
                empty(trim($planningMachines[$i]['july'])) ||
                empty(trim($planningMachines[$i]['august'])) ||
                empty(trim($planningMachines[$i]['september'])) ||
                empty(trim($planningMachines[$i]['october'])) ||
                empty(trim($planningMachines[$i]['november'])) ||
                empty(trim($planningMachines[$i]['december'])) ||
                empty(trim($planningMachines[$i]['active']))
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Columna vacia"));
            }

            if (
                $planningMachines[$i]['january'] > 31 ||
                $planningMachines[$i]['february'] > 28 ||
                $planningMachines[$i]['march'] > 31 ||
                $planningMachines[$i]['april'] > 30 ||
                $planningMachines[$i]['may'] > 31 ||
                $planningMachines[$i]['june'] > 30 ||
                $planningMachines[$i]['july'] > 31 ||
                $planningMachines[$i]['august'] > 31 ||
                $planningMachines[$i]['september'] > 30 ||
                $planningMachines[$i]['october'] > 31 ||
                $planningMachines[$i]['november'] > 30 ||
                $planningMachines[$i]['december'] > 31
            ) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: El valor es mayor al último día del mes"));
            }

            if (!strpos($planningMachines[$i]['hourStart'], 'AM')) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Verificar que el formato de la hora de inico sea correcto"));
            }

            // Obtener id maquina
            $findMachine = $machinesDao->findMachine($planningMachines[$i], $id_company);

            if (!$findMachine) {
                $row = $i + 2;
                array_push($debugg, array('error' => true, 'message' => "Fila-$row: Máquina no existe"));
            } else $planningMachines[$i]['idMachine'] = $findMachine['id_machine'];

            if (sizeof($debugg) == 0) {
                $findPlanMachines = $generalPlanningMachinesDao->findPlanMachines($planningMachines[$i], $id_company);
                if (!$findPlanMachines) $insert = $insert + 1;
                else $update = $update + 1;
                $dataImportPlanMachines['insert'] = $insert;
                $dataImportPlanMachines['update'] = $update;
            }
        }
    } else $dataImportPlanMachines = array('error' => true, 'message' => 'El archivo se encuentra vacio. Intente nuevamente');

    $data['import'] = $dataImportPlanMachines;
    $data['debugg'] = $debugg;

    $response->getBody()->write(json_encode($data, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addPlanningMachines', function (Request $request, Response $response, $args) use (
    $planningMachinesDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $productsDao,
    $generalProgrammingDao,
    $generalMaterialsDao,
    $generalClientsDao,
    $generalRequisitionsProductsDao,
    $generalPlanningMachinesDao,
    $generalPlanCiclesMachinesDao,
    $ciclesMachinesDao,
    $generalPayrollDao,
    $timeConvertDao,
    $lastDataDao,
    $machinesDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $dataPMachines = $request->getParsedBody();

    $dataPMachine =  sizeof($dataPMachines);
    $resolution = null;

    if ($dataPMachine > 1) {
        $findPlanMachines = $generalPlanningMachinesDao->findPlanMachines($dataPMachines, $id_company);

        if (!$findPlanMachines) {
            $dataPMachine = $timeConvertDao->timeConverter($dataPMachines);
            $planningMachines = $planningMachinesDao->insertPlanMachinesByCompany($dataPMachine, $id_company);

            $machines = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachine($dataPMachine['idMachine'], $id_company);

            foreach ($machines as $k) {
                if (isset($planningMachines['info'])) break;
                $data = [];
                $data['idCiclesMachine'] = $k['id_cicles_machine'];

                $arr = $ciclesMachinesDao->calcUnitsTurn($k['id_cicles_machine']);
                $data['units_turn'] = $arr['units_turn'];

                $arr = $ciclesMachinesDao->calcUnitsMonth($data, 2);
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

            $hourEnd = $timeConvertDao->calculateHourEnd($planningMachines[$i]['hourStart'], $planningMachines[$i]['workShift'], $planningMachines[$i]['hoursDay']);

            if (isset($hourEnd['info'])) {
                $resolution = $hourEnd;
                break;
            }

            $planningMachines[$i]['year'] = date('Y');
            $planningMachines[$i]['hourStart'] = date("G.i", strtotime($planningMachines[$i]['hourStart']));
            $planningMachines[$i]['hourEnd'] = date("G.i", strtotime($hourEnd));

            $planningMachines[$i]['type'] == 'PROCESO MANUAL' ? $planningMachines[$i]['typePM'] = 0 : $planningMachines[$i]['typePM'] = 1;

            // obtener numero trabajadores
            $payroll = $generalPayrollDao->findCountEmployeesByMachine($planningMachines[$i]['idMachine']);
            $planningMachines[$i]['numberWorkers'] = $payroll['employees'];

            if (!$findPlanMachines) {
                $resolution = $planningMachinesDao->insertPlanMachinesByCompany($planningMachines[$i], $id_company);
                $lastMachine = $lastDataDao->findLastInsertedPMachine($id_company);
                $planningMachines[$i]['idProgramMachine'] = $lastMachine['id_program_machine'];
            } else {
                $planningMachines[$i]['idProgramMachine'] = $findPlanMachines['id_program_machine'];
                $resolution = $planningMachinesDao->updatePlanMachines($planningMachines[$i]);
            }
            $planningMachines[$i]['active'] == 'SI' ? $status = 1 : $status = 0;

            $resolution = $generalPlanningMachinesDao->changeStatusPmachine($planningMachines[$i]['idProgramMachine'], $status);

            $machines = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachine($planningMachines[$i]['idMachine'], $id_company);

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
        if ($resolution == null)
            $resp = array('success' => true, 'message' => 'Planeacion de maquina importada correctamente');
        else if (isset($resolution['info']))
            $resp = array('info' => true, 'message' => $resolution['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras importaba la información. Intente nuevamente');
    }

    if ($resolution == null) {
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Ficha tecnica
            $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
            $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
            $productsFTM = array_merge($productsMaterials, $compositeProducts);

            $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

            // if ($orders[$i]['origin'] == 2) {
            if (
                $orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FINALIZADO' &&
                $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO'
            ) {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {

                    if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                        $orders[$i]['origin'] == 2 ? $status = 5 : $status = 13;

                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], $status);
                        $status = false;
                    } else {
                        foreach ($planCicles as $arr) {
                            // Verificar Maquina Disponible
                            if ($arr['status'] == 0 && $arr['status_alternal_machine'] == 0) {
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
            // } else if ($orders[$i]['origin'] == 1) {
            //     if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
            //         $resolution = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 13);

            //         $data = [];
            //         $data['idProduct'] = $orders[$i]['id_product'];

            //         $provider = $generalClientsDao->findInternalClient($id_company);

            //         $id_provider = 0;

            //         if (isset($provider['id_provider'])) $id_provider = $provider['id_provider'];

            //         $data['idProvider'] = $id_provider;
            //         $data['numOrder'] = $orders[$i]['num_order'];
            //         $data['applicationDate'] = '';
            //         $data['deliveryDate'] = '';
            //         $data['requiredQuantity'] = $orders[$i]['original_quantity'];
            //         $data['purchaseOrder'] = '';
            //         $data['requestedQuantity'] = 0;

            //         $requisition = $generalRequisitionsProductsDao->findRequisitionByApplicationDate($orders[$i]['id_product']);

            //         if (!$requisition)
            //             $generalRequisitionsProductsDao->insertRequisitionAutoByCompany($data, $id_company);
            //         else {
            //             $data['idRequisition'] = $requisition['id_requisition_product'];
            //             $generalRequisitionsProductsDao->updateRequisitionAuto($data);
            //         }
            //     }
            // }

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

        $machines = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachine($dataPMachine['idMachine'], $id_company);

        foreach ($machines as $k) {
            if (isset($planningMachines['info'])) break;
            $data = [];
            $data['idCiclesMachine'] = $k['id_cicles_machine'];

            $arr = $ciclesMachinesDao->calcUnitsTurn($k['id_cicles_machine']);
            $data['units_turn'] = $arr['units_turn'];

            $arr = $ciclesMachinesDao->calcUnitsMonth($data, 2);
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
    $ciclesMachinesDao,
    $productsMaterialsDao,
    $compositeProductsDao,
    $productsDao,
    $generalProgrammingDao,
    $generalMaterialsDao,
    $generalClientsDao,
    $generalRequisitionsProductsDao,
    $generalOrdersDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $resolution = $planningMachinesDao->deletePlanMachines($args['id_program_machine']);

    if ($resolution == null) {
        $machines = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachine($args['id_machine'], $id_company);

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

    if ($resolution == null) {
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Ficha tecnica
            $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
            $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
            $productsFTM = array_merge($productsMaterials, $compositeProducts);

            $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

            // if ($orders[$i]['origin'] == 2) {
            if (
                $orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FINALIZADO' &&
                $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO'
            ) {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {

                    if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                        $orders[$i]['origin'] == 2 ? $status = 5 : $status = 13;

                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], $status);
                        $status = false;
                    } else {
                        foreach ($planCicles as $arr) {
                            // Verificar Maquina Disponible
                            if ($arr['status'] == 0 && $arr['status_alternal_machine'] == 0) {
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
            // } else if ($orders[$i]['origin'] == 1) {
            //     if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
            //         $resolution = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 13);

            //         $data = [];
            //         $data['idProduct'] = $orders[$i]['id_product'];

            //         $provider = $generalClientsDao->findInternalClient($id_company);

            //         $id_provider = 0;

            //         if (isset($provider['id_provider'])) $id_provider = $provider['id_provider'];

            //         $data['idProvider'] = $id_provider;
            //         $data['numOrder'] = $orders[$i]['num_order'];
            //         $data['applicationDate'] = '';
            //         $data['deliveryDate'] = '';
            //         $data['requiredQuantity'] = $orders[$i]['original_quantity'];
            //         $data['purchaseOrder'] = '';
            //         $data['requestedQuantity'] = 0;

            //         $requisition = $generalRequisitionsProductsDao->findRequisitionByApplicationDate($orders[$i]['id_product']);

            //         if (!$requisition)
            //             $generalRequisitionsProductsDao->insertRequisitionAutoByCompany($data, $id_company);
            //         else {
            //             $data['idRequisition'] = $requisition['id_requisition_product'];
            //             $generalRequisitionsProductsDao->updateRequisitionAuto($data);
            //         }
            //     }
            // }

            // Pedidos automaticos
            if ($orders[$i]['status'] == 'FABRICADO') {
                $chOrders = $generalOrdersDao->findAllChildrenOrders($orders[$i]['num_order']);

                foreach ($chOrders as $arr) {
                    $resolution = $generalOrdersDao->changeStatus($arr['id_order'], 12);
                }
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Planeación de maquina eliminada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else $resp = array('error' => true, 'message' => 'No se pudo eliminar la planeación, existe información asociada a ella');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/changeStatusPMachine/{id_program_machine}/{status}', function (Request $request, Response $response, $args) use (
    $generalPlanningMachinesDao,
    $generalOrdersDao,
    $productsMaterialsDao,
    $generalMaterialsDao,
    $generalClientsDao,
    $generalRequisitionsProductsDao,
    $compositeProductsDao,
    $generalPlanCiclesMachinesDao,
    $productsDao,
    $generalProgrammingDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $resolution = $generalPlanningMachinesDao->changeStatusPmachine($args['id_program_machine'], $args['status']);

    if ($resolution == null) {
        $orders = $generalOrdersDao->findAllOrdersByCompany($id_company);

        for ($i = 0; $i < sizeof($orders); $i++) {
            $status = true;
            // Ficha tecnica
            $productsMaterials = $productsMaterialsDao->findAllProductsMaterials($orders[$i]['id_product'], $id_company);
            $compositeProducts = $compositeProductsDao->findAllCompositeProductsByIdProduct($orders[$i]['id_product'], $id_company);
            $productsFTM = array_merge($productsMaterials, $compositeProducts);

            $planCicles = $generalPlanCiclesMachinesDao->findAllPlanCiclesMachineByProduct($orders[$i]['id_product'], $id_company);

            // if ($orders[$i]['origin'] == 2) {
            if (
                $orders[$i]['status'] != 'EN PRODUCCION' && $orders[$i]['status'] != 'FINALIZADO' &&
                $orders[$i]['status'] != 'FABRICADO' && $orders[$i]['status'] != 'DESPACHO'
            ) {
                if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {

                    if (sizeof($productsFTM) == 0 || sizeof($planCicles) == 0) {
                        $orders[$i]['origin'] == 2 ? $status = 5 : $status = 13;

                        $generalOrdersDao->changeStatus($orders[$i]['id_order'], $status);
                        $status = false;
                    } else {
                        foreach ($planCicles as $arr) {
                            // Verificar Maquina Disponible
                            if ($arr['status'] == 0 && $arr['status_alternal_machine'] == 0) {
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
            // } else if ($orders[$i]['origin'] == 1) {
            //     if ($orders[$i]['original_quantity'] > $orders[$i]['accumulated_quantity']) {
            //         $resolution = $generalOrdersDao->changeStatus($orders[$i]['id_order'], 13);

            //         $data = [];
            //         $data['idProduct'] = $orders[$i]['id_product'];

            //         $provider = $generalClientsDao->findInternalClient($id_company);

            //         $id_provider = 0;

            //         if (isset($provider['id_provider'])) $id_provider = $provider['id_provider'];

            //         $data['idProvider'] = $id_provider;
            //         $data['numOrder'] = $orders[$i]['num_order'];
            //         $data['applicationDate'] = '';
            //         $data['deliveryDate'] = '';
            //         $data['requiredQuantity'] = $orders[$i]['original_quantity'];
            //         $data['purchaseOrder'] = '';
            //         $data['requestedQuantity'] = 0;

            //         $requisition = $generalRequisitionsProductsDao->findRequisitionByApplicationDate($orders[$i]['id_product']);

            //         if (!$requisition)
            //             $generalRequisitionsProductsDao->insertRequisitionAutoByCompany($data, $id_company);
            //         else {
            //             $data['idRequisition'] = $requisition['id_requisition_product'];
            //             $generalRequisitionsProductsDao->updateRequisitionAuto($data);
            //         }
            //     }
            // }

            // Pedidos automaticos
            if ($orders[$i]['status'] == 'FABRICADO') {
                $chOrders = $generalOrdersDao->findAllChildrenOrders($orders[$i]['num_order']);

                foreach ($chOrders as $arr) {
                    $resolution = $generalOrdersDao->changeStatus($arr['id_order'], 12);
                }
            }
        }
    }

    if ($resolution == null)
        $resp = array('success' => true, 'message' => 'Programacion de maquina modificada correctamente');
    else if (isset($resolution['info']))
        $resp = array('info' => true, 'message' => $resolution['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrió un error mientras modificaba los datos. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
