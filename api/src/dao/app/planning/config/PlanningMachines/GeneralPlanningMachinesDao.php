<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPlanningMachinesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Buscar si existe en la base de datos */
    public function findPlanMachines($dataPMachines, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_program_machines 
                                      WHERE id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute(['id_machine' => $dataPMachines['idMachine'], 'id_company' => $id_company]);
        $planningMachines = $stmt->fetch($connection::FETCH_ASSOC);
        return $planningMachines;
    }

    public function changeStatusPmachine($id_program_machine, $status)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE plan_program_machines SET status = :status WHERE id_program_machine = :id_program_machine");
            $stmt->execute([
                'status' => $status,
                'id_program_machine' => $id_program_machine
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
