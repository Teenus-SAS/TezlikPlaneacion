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

        $stmt = $connection->prepare("SELECT *  FROM plan_cicles_machine WHERE id_cicles_machine = :id_cicles_machine");
        $stmt->execute(['id_cicles_machine' => $id_cicles_machine]);
        $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }

    // public function findPlansCiclesMachine($dataCiclesMachine, $id_company)
    // {
    //     $connection = Connection::getInstance()->getConnection();

    //     $stmt = $connection->prepare("SELECT *  FROM plan_cicles_machine
    //                                   WHERE id_product = :id_product AND id_company = :id_company");
    //     $stmt->execute([
    //         'id_product' => $dataCiclesMachine['idProduct'],
    //         'id_company' => $id_company
    //     ]);
    //     $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
    //     return $planCiclesMachine;
    // }

    public function findAllPlanCiclesMachine($id_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT *  FROM plan_cicles_machine
                                      WHERE id_machine = :id_machine");
        $stmt->execute(['id_machine' => $id_machine]);
        $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $machines;
    }

    public function findAllPlanCiclesMachineByProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pcm.id_cicles_machine, pcm.cicles_hour, pcm.units_turn, pcm.units_month, p.id_product, p.reference, p.product, IFNULL(pc.id_process, 0) AS id_process, IFNULL(pc.process, '') AS process, m.id_machine, m.machine
                                      FROM plan_cicles_machine pcm
                                        INNER JOIN products p ON p.id_product = pcm.id_product
                                        INNER JOIN machines m ON m.id_machine = pcm.id_machine
                                        LEFT JOIN process pc ON pc.id_process = pcm.id_process
                                      WHERE pcm.id_product = :id_product AND pcm.id_company = :id_company");
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
                                      FROM plan_cicles_machine pcm
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

        $stmt = $connection->prepare("SELECT * FROM plan_cicles_machine
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

        $stmt = $connection->prepare("SELECT * FROM plan_cicles_machine
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
                                      FROM plan_cicles_machine
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
            $stmt = $connection->prepare("UPDATE plan_cicles_machine SET units_turn = :units_turn, units_month = :units_month
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
            $stmt = $connection->prepare("UPDATE plan_cicles_machine SET route = :route WHERE id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'route' => $dataCiclesMachine['route'],
                'id_cicles_machine' => $dataCiclesMachine['idCiclesMachine']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
