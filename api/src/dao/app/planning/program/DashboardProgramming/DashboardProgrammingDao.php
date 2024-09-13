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

    //Encontrar Empleadoas disponibles por empresa
    public function findStaffAvailableByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT p.process, 
                    m.machine, 
                    COUNT(pp.id_plan_payroll) AS total_operadores,
                    SUM(CASE WHEN pp.status = 1 THEN 1 ELSE 0 END) AS operarios_disponibles
                FROM plan_payroll pp
                INNER JOIN process p ON pp.id_process = p.id_process
                INNER JOIN machines m ON pp.id_machine = m.id_machine
                WHERE pp.id_company = :id_company
                GROUP BY p.process, m.machine;";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $staffAvailable = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $staffAvailable;
    }

    //Encontrar Maquinas dispobles por empresa
    public function findMachinesAvailableByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT m.machine, pm.status AS status
                FROM plan_program_machines pm
                INNER JOIN machines m ON pm.id_machine = m.id_machine
                WHERE pm.type_program_machine = 1 AND pm.id_company = :id_company;";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $machinesAvailable = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $machinesAvailable;
    }

    public function findMachinesCapacityProgrammedByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT m.machine AS machine_name, (pp.hours_day * 
                    CASE 
                        WHEN MONTH(CURDATE()) = 1 THEN pp.january
                        WHEN MONTH(CURDATE()) = 2 THEN pp.february
                        WHEN MONTH(CURDATE()) = 3 THEN pp.march
                        WHEN MONTH(CURDATE()) = 4 THEN pp.april
                        WHEN MONTH(CURDATE()) = 5 THEN pp.may
                        WHEN MONTH(CURDATE()) = 6 THEN pp.june
                        WHEN MONTH(CURDATE()) = 7 THEN pp.july
                        WHEN MONTH(CURDATE()) = 8 THEN pp.august
                        WHEN MONTH(CURDATE()) = 9 THEN pp.september
                        WHEN MONTH(CURDATE()) = 10 THEN pp.october
                        WHEN MONTH(CURDATE()) = 11 THEN pp.november
                        WHEN MONTH(CURDATE()) = 12 THEN pp.december
                    END) AS monthly_capacity_hours, -- Capacidad mensual en horas
                    COALESCE(SUM(p.min_programming) / 60, 0) AS total_programmed_hours -- Capacidad programada en horas
                FROM machines m
                LEFT JOIN plan_program_machines pp ON m.id_machine = pp.id_machine
                LEFT JOIN programming p ON m.id_machine = p.id_machine 
                WHERE m.id_company = :id_company
                GROUP BY m.machine;";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $machinesCapacityProgrammed = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $machinesCapacityProgrammed;
    }
}
