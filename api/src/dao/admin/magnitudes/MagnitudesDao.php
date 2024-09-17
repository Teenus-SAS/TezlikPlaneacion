<?php

namespace TezlikPlaneacion\Dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MagnitudesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllMagnitudes()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM admin_magnitudes");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $magnitudes = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $magnitudes;
    }

    public function findMagnitude($dataMagnitude)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM admin_magnitudes WHERE magnitude = :magnitude");
        $stmt->execute([
            'magnitude' => strtoupper(trim($dataMagnitude['magnitude']))
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $magnitude = $stmt->fetch($connection::FETCH_ASSOC);
        return $magnitude;
    }

    public function insertMagnitude($dataMagnitude)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO admin_magnitudes (magnitude) VALUES (:magnitude)");
            $stmt->execute([
                'magnitude' => strtoupper(trim($dataMagnitude['magnitude']))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Magnitud duplicada. Ingrese una nueva magnitud';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateMagnitude($dataMagnitude)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE admin_magnitudes SET magnitude = :magnitude WHERE id_magnitude = :id_magnitude");
            $stmt->execute([
                'id_magnitude' => $dataMagnitude['idMagnitude'],
                'magnitude' => strtoupper(trim($dataMagnitude['magnitude']))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Magnitud duplicada. Ingrese una nueva magnitud';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteMagnitude($id_magnitude)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM admin_magnitudes WHERE id_magnitude = :id_magnitude");
            $stmt->execute([
                'id_magnitude' => $id_magnitude
            ]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM admin_magnitudes WHERE id_magnitude = :id_magnitude");
                $stmt->execute([
                    'id_magnitude' => $id_magnitude
                ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
