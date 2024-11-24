<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProgrammingDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findProgramming($id_programming, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM programming WHERE id_programming = :id_programming AND id_company = :id_company");
        $stmt->execute([
            'id_programming' => $id_programming,
            'id_company' => $id_company
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findLastNumOPByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT CONCAT('OP', COUNT(id_programming) + 1) AS op FROM programming 
                WHERE id_company = :id_company AND status = 1";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findAllProgrammingByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT
                    -- Columnas
                        pg.id_programming,
                        o.id_order,
                        o.num_order,
                        o.date_order,
                        o.original_quantity AS quantity_order,
                        o.accumulated_quantity,
                        pg.quantity AS quantity_programming,
                        p.id_product,
                        p.reference,
                        p.product,
                        m.id_machine,
                        m.machine,
                        c.client,
                        pg.min_date,
                        HOUR(pg.min_date) AS min_hour,
                        pm.hour_start,
                        pg.max_date,
                        HOUR(pg.max_date) AS max_hour,
                        ( SELECT
                            COALESCE(
                                (1 * cm.quantity / cpm.quantity),
                                0
                            ) AS quantity_ratio
                        FROM products_materials cpm
                        INNER JOIN inv_materials cm ON cm.id_material = cpm.id_material
                        WHERE cpm.id_product = pg.id_product
                        ORDER BY 1 ASC LIMIT 1 ) AS quantity_mp, 
                        pg.status
                FROM programming pg
                    INNER JOIN orders o ON o.id_order = pg.id_order
                    INNER JOIN products p ON p.id_product = pg.id_product
                    INNER JOIN machines m ON m.id_machine = pg.id_machine
                    INNER JOIN third_parties c ON c.id_client = o.id_client
                    INNER JOIN machine_programs pm ON pm.id_machine = pg.id_machine
                WHERE pg.id_company = :id_company AND pg.status = 1";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findAllProgrammingByMachine($id_machine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT
                    -- Columnas
                        pg.id_programming,
                        o.id_order,
                        o.num_order,
                        o.date_order,
                        o.original_quantity AS quantity_order,
                        o.accumulated_quantity,
                        pg.quantity AS quantity_programming,
                        p.id_product,
                        p.reference,
                        p.product,
                        m.id_machine,
                        m.machine,
                        c.client,
                        pg.min_date,
                        HOUR(pg.min_date) AS min_hour,
                        pm.hour_start,
                        pg.max_date,
                        HOUR(pg.max_date) AS max_hour,
                        subquery.quantity_mp,
                        pc.id_process,
                        pc.process,
                        pg.status
                FROM programming pg
                    INNER JOIN orders o ON o.id_order = pg.id_order
                    INNER JOIN products p ON p.id_product = pg.id_product
                    INNER JOIN machines m ON m.id_machine = pg.id_machine
                    INNER JOIN third_parties c ON c.id_client = o.id_client
                    INNER JOIN machine_programs pm ON pm.id_machine = pg.id_machine
                    INNER JOIN machine_cicles cp ON cp.id_product = pg.id_product AND cp.id_machine = pg.id_machine
                    INNER JOIN process pc ON pc.id_process = cp.id_process
                    LEFT JOIN(
                        SELECT cpm.id_product,
                            COALESCE(
                                MIN(1 * cm.quantity / cpm.quantity),
                                0
                            ) AS quantity_mp
                        FROM
                            products_materials cpm
                        INNER JOIN inv_materials cm ON
                            cm.id_material = cpm.id_material
                        GROUP BY
                            cpm.id_product
                    ) AS subquery
                ON subquery.id_product = pg.id_product
                WHERE pg.id_machine = :id_machine AND pg.id_company = :id_company AND pg.status = 0";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'id_machine' => $id_machine,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findAllOrdersByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            o.id_order, 
                                            o.id_client, 
                                            o.id_product, 
                                            o.num_order, 
                                            ps.status, 
                                            o.date_order, 
                                            IFNULL(o.accumulated_quantity, 0) AS accumulated_quantity, 
                                            IFNULL(o.original_quantity, 0) AS original_quantity, 
                                            o.min_date, 
                                            o.max_date, 
                                            o.delivery_date, 
                                            o.office_date
                                      FROM orders o
                                        INNER JOIN orders_status ps ON ps.id_status = o.status
                                      WHERE o.id_company = :id_company AND o.status IN (1, 4) 
                                      AND (o.accumulated_quantity IS NULL OR o.accumulated_quantity != 0)");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findLastProgrammingByMachine($id_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM programming WHERE id_machine = :id_machine");
        $stmt->execute([
            'id_machine' => $id_machine
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findProgrammingByOrder($id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM programming WHERE id_order = :id_order");
        $stmt->execute([
            'id_order' => $id_order
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findProgrammingByOrderAndProduct($id_order, $id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM programming WHERE id_order = :id_order AND id_product = :id_product");
        $stmt->execute([
            'id_order' => $id_order,
            'id_product' => $id_product
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function checkAccumulatedQuantityOrder($id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.original_quantity, (SELECT IFNULL(SUM(quantity), 0) FROM programming WHERE id_order = o.id_order) AS quantity_programming
                                      FROM orders o 
                                      WHERE o.id_order = :id_order");
        $stmt->execute([
            'id_order' => $id_order
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        return $order;
    }

    public function updateFinalDateAndHour($dataProgramming)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE programming SET max_date = :max_date
                                          WHERE id_programming = :id_programming");
            $stmt->execute([
                'max_date' => $dataProgramming['final_date'],
                'id_programming' => $dataProgramming['idProgramming']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function changeStatusProgramming($dataProgramming)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE programming SET status = :status, num_production = :num_production 
                                          WHERE id_programming = :id_programming");
            $stmt->execute([
                'id_programming' => $dataProgramming['id_programming'],
                'status' => $dataProgramming['status'],
                'num_production' => $dataProgramming['numOP']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function addMinutesProgramming($id_programming, $minutes)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE programming SET min_programming = :min_programming WHERE id_programming = :id_programming");
            $stmt->execute([
                'id_programming' => $id_programming,
                'min_programming' => $minutes
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function changeFlagProgramming($id_programming, $flag_cancel)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE programming SET flag_cancel = :flag_cancel WHERE id_programming = :id_programming");
            $stmt->execute([
                'id_programming' => $id_programming,
                'flag_cancel' => $flag_cancel
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteProgrammingByProduct($id_product)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM programming WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);

            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM programming WHERE id_product = :id_product");
                $stmt->execute(['id_product' => $id_product]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
