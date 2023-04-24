<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class TimeConvertDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Convertir tiempo
    public function timeConverter($dataPMachines)
    {
        $dataPMachines['year'] = date('Y');
        $dataPMachines['hourStart'] = date("G:i", strtotime($dataPMachines['hourStart']));
        $dataPMachines['hourEnd'] = date("G:i", strtotime($dataPMachines['hourEnd']));

        return $dataPMachines;
    }
}
