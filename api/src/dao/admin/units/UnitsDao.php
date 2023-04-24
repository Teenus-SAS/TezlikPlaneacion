<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UnitsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllUnits()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT u.id_unit, m.id_magnitude, m.magnitude, u.unit, u.abbreviation
                                      FROM convert_units u
                                        INNER JOIN convert_magnitudes m ON m.id_magnitude = u.id_magnitude
                                      ORDER BY m.magnitude ASC");
        $stmt->execute();

        $units = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        return $units;
    }

    public function findUnitsByMagnitude($id_magnitude)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM convert_units WHERE id_magnitude = :id_magnitude");
        $stmt->execute(['id_magnitude' => $id_magnitude]);

        $units = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        return $units;
    }

    public function findUnit($dataUnit)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM convert_units WHERE id_magnitude = :id_magnitude AND abbreviation = :abbreviation");
        $stmt->execute([
            'id_magnitude' => $dataUnit['idMagnitude'],
            'abbreviation' => strtoupper(trim($dataUnit['abbreviation']))
        ]);

        $unit = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        return $unit;
    }

    public function insertUnit($dataUnit)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO convert_units (id_magnitude, unit, abbreviation) 
                                          VALUES (:id_magnitude, :unit, :abbreviation)");
            $stmt->execute([
                'id_magnitude' => $dataUnit['magnitude'],
                'unit' => strtoupper(trim($dataUnit['unit'])),
                'abbreviation' => strtoupper(trim($dataUnit['abbreviation']))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'La unidad ya existe con el mismo nombre';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateUnit($dataUnit)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE convert_units SET id_magnitude = :id_magnitude, unit = :unit, abbreviation = :abbreviation 
                                          WHERE id_unit = :id_unit");
            $stmt->execute([
                'id_unit' => $dataUnit['idUnit'],
                'id_magnitude' => $dataUnit['magnitude'],
                'unit' => strtoupper(trim($dataUnit['unit'])),
                'abbreviation' => strtoupper(trim($dataUnit['abbreviation']))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteUnit($id_unit)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM convert_units WHERE id_unit = :id_unit");
            $stmt->execute(['id_unit' => $id_unit]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM convert_units WHERE id_unit = :id_unit");
                $stmt->execute(['id_unit' => $id_unit]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
