<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProgrammingRoutesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProgrammingRoutes($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("");
        $stmt->execute([]);
        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function insertProgrammingRoutes($dataProgramming)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO progamming_routes (id_order, id_product, route) 
                                          VALUES (:id_order, :id_product, :route)");
            $stmt->execute([
                'id_order' => $dataProgramming['idOrder'],
                'id_product' => $dataProgramming['idProduct'],
                'route' => $dataProgramming['route']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateProgrammingRoutes($dataProgramming)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE progamming_routes SET route = :route WHERE id_programming_routes = :id_programming_routes");
            $stmt->execute([
                'route' => $dataProgramming['route'],
                'id_programming_routes' => $dataProgramming['idProgrammingRoutes']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteProgrammingRoute($id_progamming_routes)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM progamming_routes WHERE id_progamming_routes = :id_progamming_routes");
            $stmt->execute(['id_progamming_routes' => $id_progamming_routes]);

            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM progamming_routes WHERE id_progamming_routes = :id_progamming_routes");
                $stmt->execute(['id_progamming_routes' => $id_progamming_routes]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
