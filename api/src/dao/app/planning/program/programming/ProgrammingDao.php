<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProgrammingDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }
    /*
    public function findProductsAndOrdersByMachine($dataProgramming)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.product, o.id_order, o.num_order
                                      FROM products p 
                                      INNER JOIN plan_cicles_machine pcm ON pcm.id_product = p.id_product
                                      INNER JOIN plan_orders o ON o.id_product = p.id_product 
                                      WHERE pcm.id_machine = :id_machine");
        $stmt->execute(['id_machine' => $dataProgramming['idMachine']]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $productsAndOrders = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $productsAndOrders;
    }

    public function findMachinesAndOrdersByProducts($dataProgramming)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT m.id_machine, m.machine, o.id_order, o.num_order 
                                      FROM machines m 
                                      INNER JOIN plan_cicles_machine pcm ON pcm.id_machine = m.id_machine 
                                      INNER JOIN plan_orders o ON o.id_product = pcm.id_product
                                      WHERE o.id_product = :id_product");
        $stmt->execute(['id_product' => $dataProgramming['idProduct']]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $machinesAndOrders = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $machinesAndOrders;
    }
 */
    public function findProductsByOrders($num_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, p.id_product, p.reference, p.product, p.quantity, o.original_quantity, IFNULL(o.accumulated_quantity, 0) AS accumulated_quantity
                                      FROM products p
                                      INNER JOIN plan_orders o ON o.id_product = p.id_product
                                      WHERE o.num_order = :num_order AND (o.status = 'Programar' OR o.status = 'Programado')
                                      AND (o.accumulated_quantity IS NULL OR o.accumulated_quantity != 0)
                                      GROUP BY p.id_product");
        $stmt->execute(['num_order' => $num_order]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findAllProgrammingByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pg.id_programming, o.id_order, o.num_order, o.date_order, o.original_quantity AS quantity_order, o.accumulated_quantity, pg.quantity AS quantity_programming, p.id_product, 
                                             p.reference, p.product, m.id_machine, m.machine, c.client, pg.min_date, HOUR(pg.min_date) AS min_hour, pm.hour_start, pg.max_date, HOUR(pg.max_date) AS max_hour,
                                             (SELECT IFNULL((1*cm.quantity/cpm.quantity), 0) FROM products_materials cpm INNER JOIN materials cm ON cm.id_material = cpm.id_material WHERE cpm.id_product = pg.id_product ORDER BY `IFNULL((1*cm.quantity/cpm.quantity), 0)` ASC LIMIT 1) AS quantity_mp, pc.id_process, pc.process,
                                             pg.status, pg.min_programming
                                      FROM programming pg
                                        INNER JOIN plan_orders o ON o.id_order = pg.id_order
                                        INNER JOIN products p ON p.id_product = pg.id_product
                                        INNER JOIN machines m ON m.id_machine = pg.id_machine
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                        INNER JOIN plan_program_machines pm ON pm.id_machine = pg.id_machine
                                        INNER JOIN plan_cicles_machine cp ON cp.id_product = pg.id_product AND cp.id_machine = pg.id_machine 
                                        INNER JOIN process pc ON pc.id_process = cp.id_process
                                      WHERE pg.id_company = :id_company AND pg.status = 0");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function insertProgrammingByCompany($dataProgramming, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $quantity = str_replace('.', '', $dataProgramming['quantity']);
            $quantity = str_replace(',', '.', $quantity);

            $stmt = $connection->prepare("INSERT INTO programming (id_company, id_order, id_product, id_machine, quantity, min_date, max_date)
                                          VALUES (:id_company, :id_order, :id_product, :id_machine, :quantity, :min_date, :max_date)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_order' => $dataProgramming['order'],
                'id_product' => $dataProgramming['idProduct'],
                'id_machine' => $dataProgramming['idMachine'],
                'quantity' => $quantity,
                'min_date' => $dataProgramming['minDate'],
                'max_date' => $dataProgramming['maxDate']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateProgramming($dataProgramming)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $quantity = str_replace('.', '', $dataProgramming['quantity']);
            $quantity = str_replace(',', '.', $quantity);

            $stmt = $connection->prepare("UPDATE programming SET id_order = :id_order, id_product = :id_product, id_machine = :id_machine, quantity = :quantity, min_date = :min_date, max_date = :max_date
                                          WHERE id_programming = :id_programming");
            $stmt->execute([
                'id_programming' => $dataProgramming['idProgramming'],
                'id_order' => $dataProgramming['order'],
                'id_product' => $dataProgramming['idProduct'],
                'id_machine' => $dataProgramming['idMachine'],
                'quantity' => $quantity,
                'min_date' => $dataProgramming['minDate'],
                'max_date' => $dataProgramming['maxDate']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteProgramming($id_programming)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM programming WHERE id_programming = :id_programming");
            $stmt->execute(['id_programming' => $id_programming]);

            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM programming WHERE id_programming = :id_programming");
                $stmt->execute(['id_programming' => $id_programming]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
