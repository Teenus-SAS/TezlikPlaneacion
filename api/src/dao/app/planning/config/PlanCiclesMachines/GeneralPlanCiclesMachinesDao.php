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
    public function findPlanCiclesMachine($dataCiclesMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT *  FROM plan_cicles_machine
                                      WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataCiclesMachine['idProduct'],
            'id_company' => $id_company
        ]);
        $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }

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

        $stmt = $connection->prepare("SELECT pc.id_cicles_machine, pc.cicles_hour, pc.units_turn, pc.units_month, p.id_product, p.reference, p.product, m.id_machine, m.machine
                                      FROM plan_cicles_machine pc
                                        INNER JOIN products p ON p.id_product = pc.id_product
                                        INNER JOIN machines m ON m.id_machine = pc.id_machine
                                      WHERE pc.id_product = :id_product AND pc.id_company = :id_company");
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

        $stmt = $connection->prepare("SELECT pc.id_cicles_machine, pc.cicles_hour, pc.units_turn, pc.units_month, p.id_product, p.reference, p.product, m.id_machine, m.machine, pc.route
                                      FROM plan_cicles_machine pc
                                        INNER JOIN products p ON p.id_product = pc.id_product
                                        INNER JOIN machines m ON m.id_machine = pc.id_machine
                                      WHERE pc.id_product = :id_product AND pc.id_company = :id_company
                                      ORDER BY pc.route ASC");
        $stmt->execute([
            'id_product' => $id_product,
            'id_company' => $id_company
        ]);
        $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $machines;
    }

    public function findPlanCiclesMachineByProductAndMachine($id_product, $id_machine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_cicles_machine
                                      WHERE id_product = :id_product AND id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $id_product,
            'id_machine' => $id_machine,
            'id_company' => $id_company
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
