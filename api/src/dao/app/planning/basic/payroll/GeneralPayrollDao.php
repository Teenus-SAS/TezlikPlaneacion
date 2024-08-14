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

    public function findPayroll($dataPayroll)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_payroll WHERE firstname = :firstname AND
                                      lastname = :lastname AND id_process = :id_process AND id_area = :id_area");
        $stmt->execute([
            'firstname' => strtoupper(trim($dataPayroll['firstname'])),
            'lastname' => strtoupper(trim($dataPayroll['lastname'])),
            'id_process' => $dataPayroll['idProcess'],
            'id_area' => $dataPayroll['idArea']
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $payroll = $stmt->fetch($connection::FETCH_ASSOC);
        return $payroll;
    }
}
