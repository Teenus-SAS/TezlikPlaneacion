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

        $stmt = $connection->prepare("SELECT pr.id_programming_routes, pr.id_product, pr.route, pcm.route AS route1, p.id_process, p.process
                                        FROM programming_routes pr
                                        -- INNER JOIN plan_orders o ON o.id_order = pr.id_order
                                        INNER JOIN plan_cicles_machine pcm ON pcm.id_product = pr.id_product -- AND FIND_IN_SET(pcm.route, pr.route) > 0
                                        INNER JOIN process p ON p.id_process = pcm.id_process
                                        WHERE pr.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function insertProgrammingRoutes($dataProgramming, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO programming_routes (id_company, id_product, route) 
                                          VALUES (:id_company, :id_product, :route)");
            $stmt->execute([
                'id_company' => $id_company,
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

            $stmt = $connection->prepare("UPDATE programming_routes SET route = :route WHERE id_programming_routes = :id_programming_routes");
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

    public function deleteProgrammingRoute($id_programming_routes)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM programming_routes WHERE id_programming_routes = :id_programming_routes");
            $stmt->execute(['id_programming_routes' => $id_programming_routes]);

            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM programming_routes WHERE id_programming_routes = :id_programming_routes");
                $stmt->execute(['id_programming_routes' => $id_programming_routes]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
