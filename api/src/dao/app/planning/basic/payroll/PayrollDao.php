<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PayrollDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllPayrollByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT py.id_plan_payroll, py.firstname, py.lastname, py.position, py.status, pc.id_process, pc.process, m.id_machine, m.machine, a.id_plan_area, a.area
                                      FROM payroll py
                                        INNER JOIN process pc ON pc.id_process = py.id_process
                                        INNER JOIN machines m ON m.id_machine = py.id_machine
                                        INNER JOIN plan_areas a ON a.id_plan_area = py.id_area
                                      WHERE py.id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function insertPayrollByCompany($dataPayroll, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO payroll (id_company, firstname, lastname, position, id_process, id_machine, id_area, status)
                                          VALUES (:id_company, :firstname, :lastname, :position, :id_process, :id_machine, :id_area, :status)");
            $stmt->execute([
                'id_company' => $id_company,
                'firstname' => strtoupper(trim($dataPayroll['firstname'])),
                'lastname' => strtoupper(trim($dataPayroll['lastname'])),
                'position' => strtoupper(trim($dataPayroll['position'])),
                'id_process' => $dataPayroll['idProcess'],
                'id_machine' => $dataPayroll['idMachine'],
                'id_area' => $dataPayroll['idArea'],
                'status' => 1
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updatePayroll($dataPayroll)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE payroll SET firstname = :firstname, lastname = :lastname, position = :position, id_process = :id_process, id_machine = :id_machine, id_area = :id_area
                                          WHERE id_plan_payroll = :id_plan_payroll");
            $stmt->execute([
                'id_plan_payroll' => $dataPayroll['idPayroll'],
                'firstname' => strtoupper(trim($dataPayroll['firstname'])),
                'lastname' => strtoupper(trim($dataPayroll['lastname'])),
                'position' => strtoupper(trim($dataPayroll['position'])),
                'id_process' => $dataPayroll['idProcess'],
                'id_machine' => $dataPayroll['idMachine'],
                'id_area' => $dataPayroll['idArea']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deletePayroll($id_plan_payroll)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM payroll WHERE id_plan_payroll = :id_plan_payroll");
            $stmt->execute(['id_plan_payroll' => $id_plan_payroll]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM payroll WHERE id_plan_payroll = :id_plan_payroll");
                $stmt->execute(['id_plan_payroll' => $id_plan_payroll]);
            }
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
