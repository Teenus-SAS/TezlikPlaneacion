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

    public function addPlanCiclesMachines($dataCiclesMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO alternate_machines (id_company, id_cicles_machine, id_process, cicles_hour) 
                                          VALUES (:id_company, :id_cicles_machine, :id_process, :cicles_hour)");
            $stmt->execute([
                'id_process' => $dataCiclesMachine['idProcess'],
                'id_cicles_machine' => $dataCiclesMachine['idCicleMachine'],
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

    public function updatePlanCiclesMachine($dataCiclesMachine)
    {
        $connection = Connection::getInstance()->getConnection();


        try {
            $stmt = $connection->prepare("UPDATE alternate_machines SET id_process = :id_process, cicles_hour = :cicles_hour
                                          WHERE id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'id_cicles_machine' => $dataCiclesMachine['idCicleMachine'],
                'id_process' => $dataCiclesMachine['idProcess'],
                'cicles_hour' => $dataCiclesMachine['ciclesHour']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
