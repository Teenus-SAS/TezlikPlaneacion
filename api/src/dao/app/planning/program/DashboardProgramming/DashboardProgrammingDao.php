<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DashboardProgrammingDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findStaffAvailableByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT p.process, m.machine, COUNT(pp.id_plan_payroll) AS total_operadores
                FROM plan_payroll pp
                INNER JOIN process p ON pp.id_process = p.id_process
                INNER JOIN machines m ON pp.id_machine = m.id_machine
                WHERE pp.status = 1 AND pp.id_company = :id_company
                GROUP BY p.process, m.machine;";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $staffAvailable = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $staffAvailable;
    }
}
