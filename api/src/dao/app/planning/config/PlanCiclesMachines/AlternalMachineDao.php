<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class AlternalMachineDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // public function findAllPlanCiclesMachine($id_company)
    // {
    //     $connection = Connection::getInstance()->getConnection();

    //     $stmt = $connection->prepare("SELECT pcm.id_cicles_machine, pcm.id_product, p.reference, p.product, pcm.id_process, IFNULL(pc.process, '') AS process, pcm.id_machine, 
    //                                          m.machine, pcm.cicles_hour, pcm.units_turn, pcm.units_month, pcm.route
    //                                   FROM machine_cicles pcm
    //                                    INNER JOIN machines m ON m.id_machine = pcm.id_machine
    //                                    INNER JOIN products p ON p.id_product = pcm.id_product
    //                                    LEFT JOIN process pc ON pc.id_process = pcm.id_process
    //                                   WHERE pcm.id_company = :id_company ORDER BY pcm.route ASC");
    //     $stmt->execute(['id_company' => $id_company]);
    //     $planCiclesMachines = $stmt->fetchAll($connection::FETCH_ASSOC);
    //     return $planCiclesMachines;
    // }

    public function findAlternalMachine($id_cicles_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM alternal_machines
                                      WHERE id_cicles_machine = :id_cicles_machine");
        $stmt->execute(['id_cicles_machine' => $id_cicles_machine]);
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }

    public function addAlternalMachines($dataCiclesMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO alternal_machines (id_company, id_cicles_machine, id_machine, cicles_hour) 
                                          VALUES (:id_company, :id_cicles_machine, :id_machine, :cicles_hour)");
            $stmt->execute([
                'id_machine' => $dataCiclesMachine['idMachine'],
                'id_cicles_machine' => $dataCiclesMachine['idCiclesMachine'],
                'id_company' => $id_company,
                'cicles_hour' => $dataCiclesMachine['ciclesHour']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateAlternalMachine($dataCiclesMachine)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE alternal_machines SET id_machine = :id_machine, cicles_hour = :cicles_hour
                                          WHERE id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'id_cicles_machine' => $dataCiclesMachine['idCiclesMachine'],
                'id_machine' => $dataCiclesMachine['idMachine'],
                'cicles_hour' => $dataCiclesMachine['ciclesHour']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateUnits($dataCiclesMachine)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE alternal_machines SET units_turn = :units_turn, units_month = :units_month
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
}
