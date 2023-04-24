<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralMoldsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function activeMold($dataMold)
    {
        $connection = Connection::getInstance()->getConnection();

        $fecha_hoy = date('Y-m-d');

        try {
            $stmt = $connection->prepare("UPDATE plan_inv_molds SET date_active = :date_active, active = 1 
                                          WHERE id_mold = :id_mold");
            $stmt->execute([
                'date_active' => $fecha_hoy,
                'id_mold' => $dataMold['idMold']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function inactiveMold($dataMold)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE plan_inv_molds SET date_active = '', observation = :observation, active = 0 
                                          WHERE id_mold = :id_mold");
            $stmt->execute([
                'id_mold' => $dataMold['idMold'],
                'observation' => $dataMold['observationMold']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
