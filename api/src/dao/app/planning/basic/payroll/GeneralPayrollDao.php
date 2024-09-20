<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPayrollDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findCountEmployeesByMachine($id_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT COUNT(DISTINCT IFNULL(id_plan_payroll, 0)) AS employees
                                      FROM payroll
                                      WHERE id_machine = :id_machine AND status = 1");
        $stmt->execute([
            'id_machine' => $id_machine,
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $payroll = $stmt->fetch($connection::FETCH_ASSOC);
        return $payroll;
    }

    public function findPayroll($dataPayroll)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM payroll WHERE firstname = :firstname AND lastname = :lastname AND 
                                      id_process = :id_process AND id_machine = :id_machine AND id_area = :id_area");
        $stmt->execute([
            'firstname' => strtoupper(trim($dataPayroll['firstname'])),
            'lastname' => strtoupper(trim($dataPayroll['lastname'])),
            'id_process' => $dataPayroll['idProcess'],
            'id_machine' => $dataPayroll['idMachine'],
            'id_area' => $dataPayroll['idArea']
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $payroll = $stmt->fetch($connection::FETCH_ASSOC);
        return $payroll;
    }

    public function findPayrollByEmployee($dataPayroll)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM payroll WHERE firstname = :firstname AND lastname = :lastname AND position = :position");
        $stmt->execute([
            'firstname' => strtoupper(trim($dataPayroll['firstname'])),
            'lastname' => strtoupper(trim($dataPayroll['lastname'])),
            'position' => strtoupper(trim($dataPayroll['position'])),
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $payroll = $stmt->fetch($connection::FETCH_ASSOC);
        return $payroll;
    }

    public function changeStatusPayroll($id_plan_payroll, $status)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE payroll SET status = :status WHERE id_plan_payroll = :id_plan_payroll");
            $stmt->execute([
                'status' => $status,
                'id_plan_payroll' => $id_plan_payroll
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
