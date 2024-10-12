<?php

namespace TezlikPlaneacion\Dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class BenefitsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllBenefits()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM benefits");
        $stmt->execute();

        $benefits = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $benefits;
    }

    public function updateBenefit($dataBenefit)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE benefits SET percentage = :percentage WHERE id_benefit = :id_benefit");
            $stmt->execute([
                'percentage' => $dataBenefit['percentage'],
                'id_benefit' => $dataBenefit['idBenefit']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
