<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPlanCiclesMachinesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Buscar si existe en la BD
    public function findPlanCiclesMachine($id_cicles_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM machine_cicles WHERE id_cicles_machine = :id_cicles_machine");
        $stmt->execute(['id_cicles_machine' => $id_cicles_machine]);
        $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }

    // public function findPlansCiclesMachine($dataCiclesMachine, $id_company)
    // {
    //     $connection = Connection::getInstance()->getConnection();

    //     $stmt = $connection->prepare("SELECT *  FROM machine_cicles
    //                                   WHERE id_product = :id_product AND id_company = :id_company");
    //     $stmt->execute([
    //         'id_product' => $dataCiclesMachine['idProduct'],
    //         'id_company' => $id_company
    //     ]);
    //     $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
    //     return $planCiclesMachine;
    // }

    public function findAllPlanCiclesMachine($id_machine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM machine_cicles
                                      WHERE id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute([
            'id_machine' => $id_machine,
            'id_company' => $id_company
        ]);
        $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $machines;
    }

    public function findNextRouteByPG($id_product, $route)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT mc.id_machine, mc.route
                                      FROM machine_cicles mc  
                                      WHERE mc.id_product = :id_product AND mc.route = :route
                                        ORDER BY `mc`.`route` ASC LIMIT 1");
        $stmt->execute([
            'id_product' => $id_product,
            'route' => $route
        ]);
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }

    public function findAllPlanCiclesMachineByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            pcm.id_cicles_machine,
                                            pcm.cicles_hour,
                                            pcm.units_turn,
                                            pcm.units_month, 
                                            IFNULL(pm.status, 0) AS status,
                                            p.id_product,
                                            p.reference,
                                            p.product,
                                            IFNULL(pc.id_process, 0) AS id_process,
                                            IFNULL(pc.process, '') AS process,
                                            IFNULL(pcm.id_machine, 0) AS id_machine,
                                            IFNULL(m.machine, 'PROCESO MANUAL') AS machine,
                                            COUNT(DISTINCT py.id_plan_payroll) AS employees,
                                            ROW_NUMBER() OVER() AS route,
                                            IFNULL(am.id_alternal_machine, 0) AS id_alternal_machine,
                                            IFNULL(am.id_machine, 0) AS id_alternal_machine,
                                            IFNULL(amm.machine, '') AS alternal_machine,
                                            IFNULL(am.cicles_hour, 0) AS alternal_cicles_hour,
                                            IFNULL(am.units_turn, 0) AS alternal_units_turn,
                                            IFNULL(am.units_month, 0) AS alternal_units_month
                                    FROM machine_cicles pcm
                                        INNER JOIN products p ON p.id_product = pcm.id_product
                                        LEFT JOIN machines m ON m.id_machine = pcm.id_machine
                                        LEFT JOIN machine_programs pm ON pm.id_machine = pcm.id_machine
                                        LEFT JOIN process pc ON pc.id_process = pcm.id_process
                                        LEFT JOIN payroll py ON py.id_process = pcm.id_process AND py.id_machine = pcm.id_machine -- AND py.status = 1
                                        LEFT JOIN alternal_machines am ON am.id_cicles_machine = pcm.id_cicles_machine
                                        LEFT JOIN machines amm ON amm.id_machine = am.id_machine
                                    WHERE pcm.id_product = :id_product AND pcm.id_company = :id_company
                                    GROUP BY pcm.id_cicles_machine
                                    ORDER BY `pcm`.`route` ASC;");
        $stmt->execute([
            'id_product' => $id_product,
            'id_company' => $id_company
        ]);
        $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $machines;
    }

    public function findAllRoutes($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pcm.id_cicles_machine, pcm.cicles_hour, pcm.units_turn, pcm.units_month, p.id_product, p.reference, p.product, IFNULL(pc.id_process, 0) AS id_process, IFNULL(pc.process, '') AS process, m.id_machine, m.machine, pcm.route
                                      FROM machine_cicles pcm
                                        INNER JOIN products p ON p.id_product = pcm.id_product
                                        INNER JOIN machines m ON m.id_machine = pcm.id_machine
                                        LEFT JOIN process pc ON pc.id_process = pcm.id_process
                                      WHERE pcm.id_product = :id_product AND pcm.id_company = :id_company
                                      ORDER BY pcm.route ASC");
        $stmt->execute([
            'id_product' => $id_product,
            'id_company' => $id_company
        ]);
        $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $machines;
    }

    public function findPlanCiclesMachineByProductAndMachine($id_product, $id_machine, $id_process)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM machine_cicles
                                      WHERE id_product = :id_product AND id_machine = :id_machine AND id_process = :id_process");
        $stmt->execute([
            'id_product' => $id_product,
            'id_machine' => $id_machine,
            'id_process' => $id_process
        ]);
        $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }

    public function findPlansCiclesMachine($dataCiclesMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM machine_cicles
                                      WHERE id_product = :id_product AND id_process = :id_process AND id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataCiclesMachine['idProduct'],
            'id_process' => $dataCiclesMachine['idProcess'],
            'id_machine' => $dataCiclesMachine['idMachine'],
            'id_company' => $id_company
        ]);
        $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }

    public function findNextRouteByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(route) + 1 AS route
                                      FROM machine_cicles
                                      WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product,
        ]);
        $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }

    public function updateUnits($dataCiclesMachine)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE machine_cicles SET units_turn = :units_turn, units_month = :units_month
                                          WHERE id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'units_turn' => $dataCiclesMachine['units_turn'],
                'units_month' => $dataCiclesMachine['units_month'],
                'id_cicles_machine' => $dataCiclesMachine['idCiclesMachine']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function changeRouteById($dataCiclesMachine)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE machine_cicles SET route = :route WHERE id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'route' => $dataCiclesMachine['route'],
                'id_cicles_machine' => $dataCiclesMachine['idCiclesMachine']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function deletePlanCiclesMachineByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM machine_cicles WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM machine_cicles WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
