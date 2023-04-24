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

        $stmt = $connection->prepare("SELECT pm.id_program_machine, m.machine, pm.number_workers, pm.hours_day, pm.hour_start, pm.hour_end, pm.year, pm.january, pm.february,
                                             pm.march, pm.april, pm.may, pm.june, pm.july, pm.august, pm.september, pm.october, pm.november, pm.december                                
                                      FROM plan_program_machines pm
                                        INNER JOIN machines m ON m.id_machine = pm.id_machine
                                      WHERE pm.id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $planningMachines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $planningMachines;
    }

    public function insertPlanMachinesByCompany($dataPMachines, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("INSERT INTO plan_program_machines (id_machine, id_company, number_workers, hours_day, hour_start, hour_end, year, january, 
                                                        february, march, april, may, june, july, august, september, october, november, december)
                                      VALUES (:id_machine, :id_company, :number_workers, :hours_day, :hour_start, :hour_end, :year, :january, 
                                              :february, :march, :april, :may, :june, :july, :august, :september, :october, :november, :december)");
            $stmt->execute([
                'id_company' => $id_company,                            'april' => $dataPMachines['april'],
                'id_machine' => $dataPMachines['idMachine'],            'may' => $dataPMachines['may'],
                'number_workers' => $dataPMachines['numberWorkers'],    'june' => $dataPMachines['june'],
                'hours_day' => $dataPMachines['hoursDay'],              'july' => $dataPMachines['july'],
                'hour_start' => $dataPMachines['hourStart'],            'august' => $dataPMachines['august'],
                'hour_end' => $dataPMachines['hourEnd'],                'september' => $dataPMachines['september'],
                'year' =>  $dataPMachines['year'],                      'october' => $dataPMachines['october'],
                'january' => $dataPMachines['january'],                 'november' => $dataPMachines['november'],
                'february' => $dataPMachines['february'],               'december' => $dataPMachines['december'],
                'march' => $dataPMachines['march']
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
            $stmt = $connection->prepare("UPDATE plan_program_machines SET id_machine = :id_machine, number_workers = :number_workers, hours_day = :hours_day, hour_start = :hour_start, hour_end = :hour_end, 
                                                    year = :year, january = :january, february = :february, march = :march, april = :april, may = :may, june = :june, july = :july,
                                                    august = :august, september = :september, october = :october, november = :november, december = :december
                                          WHERE id_program_machine = :id_program_machine");
            $stmt->execute([
                'id_program_machine' => $dataPMachines['idProgramMachine'],     'april' => $dataPMachines['april'],
                'id_machine' => $dataPMachines['idMachine'],                    'may' => $dataPMachines['may'],
                'number_workers' => $dataPMachines['numberWorkers'],            'june' => $dataPMachines['june'],
                'hours_day' => $dataPMachines['hoursDay'],                      'july' => $dataPMachines['july'],
                'hour_start' => $dataPMachines['hourStart'],                    'august' => $dataPMachines['august'],
                'hour_end' => $dataPMachines['hourEnd'],                        'september' => $dataPMachines['september'],
                'year' => $dataPMachines['year'],                               'october' => $dataPMachines['october'],
                'january' => $dataPMachines['january'],                         'november' => $dataPMachines['november'],
                'february' => $dataPMachines['february'],                       'december' => $dataPMachines['december'],
                'march' => $dataPMachines['march']
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

        $stmt = $connection->prepare("SELECT * FROM plan_program_machines WHERE id_program_machine = :id_program_machine");
        $stmt->execute(['id_program_machine' => $id_program_machine]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM plan_program_machines WHERE id_program_machine = :id_program_machine");
            $stmt->execute(['id_program_machine' => $id_program_machine]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
