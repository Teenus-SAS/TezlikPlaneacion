<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CalenderDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCalenderByMonth($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM calender
                                      WHERE id_company = :id_company AND month = MONTH(CURRENT_DATE)");
        $stmt->execute([
            'id_company' => $id_company
        ]);

        $calender = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $calender;
    }

    public function addDaysMonth($dataCalender, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("INSERT INTO calender (id_company, month, day, festive, working_day)
                                          VALUES (:id_company, :month, :day, :festive, :working_day)");
            $stmt->execute([
                'id_company' => $id_company,
                'month' => $dataCalender['month'],
                'day' => $dataCalender['day'],
                'festive' => $dataCalender['festive'],
                'working_day' => $dataCalender['workingDay']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateDaysMonth($dataCalender)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE calender SET festive = :festive, working_day = :working_day
                                          WHERE id_calender = :id_calender");
            $stmt->execute([
                'festive' => $dataCalender['festive'],
                'working_day' => $dataCalender['workingDay'],
                'id_calender' => $dataCalender['idCalender']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
