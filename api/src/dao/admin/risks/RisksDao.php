<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class RisksDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllRisks()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM risks");
        $stmt->execute();

        $risks = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $risks;
    }

    public function findRiskByName($dataRisk)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM risks WHERE risk_level = :risk_level");
        $stmt->execute(['risk_level' => $dataRisk['riskLevel']]);

        $risk = $stmt->fetch($connection::FETCH_ASSOC);
        return $risk;
    }

    public function updateRisk($dataRisk)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE risks SET percentage = :percentage WHERE id_risk = :id_risk");
            $stmt->execute([
                'percentage' => $dataRisk['percentage'],
                'id_risk' => $dataRisk['idRisk']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
