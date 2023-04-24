<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProcessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findProcess($dataProcess, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_process FROM process
                                  WHERE process = :process AND id_company = :id_company");
        $stmt->execute([
            'process' => strtoupper(trim($dataProcess['process'])),
            'id_company' => $id_company
        ]);
        $findProcess = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProcess;
    }
}
