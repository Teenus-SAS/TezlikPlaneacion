<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MinuteDepreciationDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcMinuteDepreciationByMachine($id_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT (cost / (years_depreciation * 12)) / hours_machine / days_machine / 60 AS minute_depreciation 
                                      FROM `machines` 
                                      WHERE id_machine = :id_machine");
        $stmt->execute(['id_machine' => $id_machine]);
        $machine = $stmt->fetch($connection::FETCH_ASSOC);

        return $machine['minute_depreciation'];
    }

    // Modificar depreciacion por minuto
    public function updateMinuteDepreciation($dataMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("UPDATE machines SET minute_depreciation = :minute_depreciation 
                                      WHERE id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute([
            'minute_depreciation' => $dataMachine['minuteDepreciation'],
            'id_machine' => $dataMachine['idMachine'],
            'id_company' => $id_company
        ]);
    }
}
