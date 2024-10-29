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

        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            pr.id_programming_routes,
                                            pr.id_order,
                                            o.num_order,
                                            pr.id_product,
                                            pr.route,
                                            pcm.route AS route1,
                                            
                                            p.id_process,
                                            p.process,
                                            IFNULL(pgm.status, 0) AS status,
                                            IFNULL(apgm.status, 0) AS status_alternal_machine
                                      FROM programming_routes pr
                                        INNER JOIN orders o ON o.id_order = pr.id_order
                                        INNER JOIN machine_cicles pcm ON pcm.id_product = pr.id_product
                                        LEFT JOIN machine_programs pgm ON pgm.id_machine = pcm.id_machine
                                        LEFT JOIN alternal_machines am ON am.id_cicles_machine = pcm.id_cicles_machine
                                        LEFT JOIN machine_programs apgm ON apgm.id_machine = am.id_machine 
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

            $stmt = $connection->prepare("INSERT INTO programming_routes (id_company, id_product, id_order, route) 
                                          VALUES (:id_company, :id_product, :id_order, :route)");
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

    public function deleteProgrammingRoute($id_order)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM programming_routes WHERE id_order = :id_order");
            $stmt->execute(['id_order' => $id_order]);

            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM programming_routes WHERE id_order = :id_order");
                $stmt->execute(['id_order' => $id_order]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
