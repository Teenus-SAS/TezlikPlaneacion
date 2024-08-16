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
    public function findAllProgrammingByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pg.id_programming, o.id_order, o.num_order, o.date_order, o.original_quantity AS quantity_order, o.accumulated_quantity, pg.quantity AS quantity_programming, p.id_product, 
                                             p.reference, p.product, m.id_machine, m.machine, c.client, pg.min_date, HOUR(pg.min_date) AS min_hour, pm.hour_start, pg.max_date, HOUR(pg.max_date) AS max_hour,
                                             (SELECT IFNULL((1*cm.quantity/cpm.quantity), 0) FROM products_materials cpm INNER JOIN materials_inventory cm ON cm.id_material = cpm.id_material WHERE cpm.id_product = pg.id_product ORDER BY `IFNULL((1*cm.quantity/cpm.quantity), 0)` ASC LIMIT 1) AS quantity_mp,
                                             pg.status
                                      FROM programming pg
                                        INNER JOIN plan_orders o ON o.id_order = pg.id_order
                                        INNER JOIN products p ON p.id_product = pg.id_product
                                        INNER JOIN machines m ON m.id_machine = pg.id_machine
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                        INNER JOIN plan_program_machines pm ON pm.id_machine = pg.id_machine
                                      WHERE pg.id_company = :id_company AND pg.status = 1");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findAllProgrammingByMachine($id_machine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pg.id_programming, o.id_order, o.num_order, o.date_order, o.original_quantity AS quantity_order, o.accumulated_quantity, pg.quantity AS quantity_programming, p.id_product, 
                                             p.reference, p.product, m.id_machine, m.machine, c.client, pg.min_date, HOUR(pg.min_date) AS min_hour, pm.hour_start, pg.max_date, HOUR(pg.max_date) AS max_hour,
                                             (SELECT IFNULL((1*cm.quantity/cpm.quantity), 0) FROM products_materials cpm INNER JOIN materials_inventory cm ON cm.id_material = cpm.id_material WHERE cpm.id_product = pg.id_product ORDER BY `IFNULL((1*cm.quantity/cpm.quantity), 0)` ASC LIMIT 1) AS quantity_mp, pc.id_process, pc.process,
                                             pg.status
                                      FROM programming pg
                                        INNER JOIN plan_orders o ON o.id_order = pg.id_order
                                        INNER JOIN products p ON p.id_product = pg.id_product
                                        INNER JOIN machines m ON m.id_machine = pg.id_machine
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                        INNER JOIN plan_program_machines pm ON pm.id_machine = pg.id_machine
                                        INNER JOIN plan_cicles_machine cp ON cp.id_product = pg.id_product AND cp.id_machine = pg.id_machine 
                                        INNER JOIN process pc ON pc.id_process = cp.id_process
                                      WHERE pg.id_machine = :id_machine AND pg.id_company = :id_company AND pg.status = 0");
        $stmt->execute([
            'id_machine' => $id_machine,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findAllProductionOrder($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SET @rownum := 0");
        $stmt->execute();

        $stmt = $connection->prepare("SELECT pg.id_programming, CONCAT('OP', @rownum := @rownum + 1) AS op, o.id_order, o.num_order, o.date_order, o.min_date AS min_date_order, o.max_date AS max_date_order, o.original_quantity AS quantity_order, o.accumulated_quantity, pg.quantity AS quantity_programming, p.id_product, pc.id_process, pc.process,
                                             p.reference, p.product, m.id_machine, m.machine, c.client, pg.min_date AS min_date_programming, HOUR(pg.min_date) AS min_hour, pm.hour_start, pg.max_date AS max_date_programming, HOUR(pg.max_date) AS max_hour, ps.status
                                      FROM programming pg
                                        INNER JOIN plan_orders o ON o.id_order = pg.id_order
                                        INNER JOIN products p ON p.id_product = pg.id_product
                                        INNER JOIN machines m ON m.id_machine = pg.id_machine
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                        INNER JOIN plan_program_machines pm ON pm.id_machine = pg.id_machine
                                        INNER JOIN plan_cicles_machine pcm ON pcm.id_product = pg.id_product AND pcm.id_machine = pg.id_machine
                                        INNER JOIN process pc ON pc.id_process = pcm.id_process
                                        INNER JOIN plan_status ps ON ps.id_status = o.status
                                      WHERE pg.status = 1 AND pg.id_company = :id_company
                                      ORDER BY pg.id_programming ASC");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findAllOrdersByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, o.id_product, o.num_order, ps.status, o.date_order, o.accumulated_quantity, o.original_quantity, o.min_date, o.max_date, o.delivery_date, o.office_date
                                      FROM plan_orders o
                                        INNER JOIN plan_status ps ON ps.id_status = o.status
                                      WHERE o.id_company = :id_company
                                      AND o.status IN (1, 4) AND (o.accumulated_quantity IS NULL OR o.accumulated_quantity != 0)");
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

    public function checkAccumulatedQuantityOrder($id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.original_quantity, (SELECT IFNULL(SUM(quantity), 0) FROM programming WHERE id_order = o.id_order) AS quantity_programming
                                      FROM plan_orders o 
                                      WHERE o.id_order = :id_order");
        $stmt->execute([
            'id_order' => $id_order
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        return $order;
    }

    // public function setMinDateProgramming($id_programming, $min_date)
    // {
    //     try {
    //         $connection = Connection::getInstance()->getConnection();

    //         $stmt = $connection->prepare("UPDATE programming SET min_date = :min_date
    //                                       WHERE id_programming = :id_programming");
    //         $stmt->execute([
    //             'id_programming' => $id_programming,
    //             'min_date' => $min_date
    //         ]);
    //     } catch (\Exception $e) {
    //         $message = $e->getMessage();
    //         $error = array('info' => true, 'message' => $message);
    //         return $error;
    //     }
    // }

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

    public function changeStatusProgramming($id_programming, $status)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE programming SET status = :status WHERE id_programming = :id_programming");
            $stmt->execute([
                'id_programming' => $id_programming,
                'status' => $status
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
}
