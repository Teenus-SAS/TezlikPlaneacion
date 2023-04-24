<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DatesMachinesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllDatesMachines($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT dm.id_machine, m.machine, dm.start_dat, dm.final_date, dm.creation_date 
                                      FROM dates_machines dm 
                                        INNER JOIN machines m ON m.id_machine = dm.id_machine 
                                      WHERE dm.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $machines;
    }

    public function findDatesMachine($dataMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM dates_machines WHERE id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute([
            'id_machine' => $dataMachine['idMachine'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }

    public function insertDatesMachine($dataMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $today_date = date("Y-m-d");
        try {
            $stmt = $connection->prepare("INSERT INTO dates_machines (id_product, id_machine, id_company, start_dat, creation_date)
                                          VALUES (:id_product, :id_machine, :id_company, :start_dat, :creation_date)");
            $stmt->execute([
                'id_product' => $dataMachine['idProduct'],
                'id_machine' => $dataMachine['idMachine'],
                'id_company' => $id_company,
                'start_dat' => $dataMachine['startDate'],
                'creation_date' => $today_date
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
