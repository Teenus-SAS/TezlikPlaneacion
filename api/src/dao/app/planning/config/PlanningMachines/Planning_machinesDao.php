<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Planning_machinesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllPlanMachines($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            pm.id_program_machine,
                                            pm.type_program_machine,
                                            pm.id_machine,
                                            m.machine,
                                            COUNT(py.id_plan_payroll) AS number_workers,
                                            pm.hours_day,
                                            pm.hour_start,
                                            pm.hour_end,
                                            pm.year,
                                            pm.january,
                                            pm.february,
                                            pm.march,
                                            pm.april,
                                            pm.may,
                                            pm.june,
                                            pm.july,
                                            pm.august,
                                            pm.september,
                                            pm.october,
                                            pm.november,
                                            pm.december,
                                            pm.work_shift,
                                            pm.status
                                      FROM machine_programs pm
                                        INNER JOIN machines m ON m.id_machine = pm.id_machine
                                        LEFT JOIN payroll py ON py.id_machine = pm.id_machine
                                      WHERE pm.id_company = :id_company
                                      GROUP BY pm.id_program_machine");
        $stmt->execute(['id_company' => $id_company]);
        $planningMachines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $planningMachines;
    }

    public function insertPlanMachinesByCompany($dataPMachines, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("INSERT INTO machine_programs (type_program_machine, id_machine, id_company, number_workers, hours_day, hour_start, hour_end, year, january, 
                                                        february, march, april, may, june, july, august, september, october, november, december, work_shift, status)
                                      VALUES (:type_program_machine, :id_machine, :id_company, :number_workers, :hours_day, :hour_start, :hour_end, :year, :january, 
                                              :february, :march, :april, :may, :june, :july, :august, :september, :october, :november, :december, :work_shift, :status)");
            $stmt->execute([
                'type_program_machine' => $dataPMachines['typePM'],
                'id_machine' => $dataPMachines['idMachine'],
                'id_company' => $id_company,
                'number_workers' => $dataPMachines['numberWorkers'],
                'hours_day' => $dataPMachines['hoursDay'],
                'hour_start' => $dataPMachines['hourStart'],
                'hour_end' => $dataPMachines['hourEnd'],
                'year' =>  $dataPMachines['year'],
                'january' => $dataPMachines['january'],
                'february' => $dataPMachines['february'],
                'march' => $dataPMachines['march'],
                'april' => $dataPMachines['april'],
                'may' => $dataPMachines['may'],
                'june' => $dataPMachines['june'],
                'july' => $dataPMachines['july'],
                'august' => $dataPMachines['august'],
                'september' => $dataPMachines['september'],
                'october' => $dataPMachines['october'],
                'november' => $dataPMachines['november'],
                'december' => $dataPMachines['december'],
                'work_shift' => $dataPMachines['workShift'],
                'status' => 1,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updatePlanMachines($dataPMachines)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE machine_programs SET type_program_machine = :type_program_machine, id_machine = :id_machine, number_workers = :number_workers, hours_day = :hours_day, hour_start = :hour_start, hour_end = :hour_end, 
                                                    year = :year, january = :january, february = :february, march = :march, april = :april, may = :may, june = :june, july = :july,
                                                    august = :august, september = :september, october = :october, november = :november, december = :december, work_shift = :work_shift
                                          WHERE id_program_machine = :id_program_machine");
            $stmt->execute([
                'id_program_machine' => $dataPMachines['idProgramMachine'],
                'type_program_machine' => $dataPMachines['typePM'],
                'id_machine' => $dataPMachines['idMachine'],
                'number_workers' => $dataPMachines['numberWorkers'],
                'hours_day' => $dataPMachines['hoursDay'],
                'hour_start' => $dataPMachines['hourStart'],
                'hour_end' => $dataPMachines['hourEnd'],
                'year' => $dataPMachines['year'],
                'january' => $dataPMachines['january'],
                'february' => $dataPMachines['february'],
                'march' => $dataPMachines['march'],
                'april' => $dataPMachines['april'],
                'may' => $dataPMachines['may'],
                'june' => $dataPMachines['june'],
                'july' => $dataPMachines['july'],
                'august' => $dataPMachines['august'],
                'september' => $dataPMachines['september'],
                'october' => $dataPMachines['october'],
                'november' => $dataPMachines['november'],
                'december' => $dataPMachines['december'],
                'work_shift' => $dataPMachines['workShift'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deletePlanMachines($id_program_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM machine_programs WHERE id_program_machine = :id_program_machine");
        $stmt->execute(['id_program_machine' => $id_program_machine]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM machine_programs WHERE id_program_machine = :id_program_machine");
            $stmt->execute(['id_program_machine' => $id_program_machine]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
