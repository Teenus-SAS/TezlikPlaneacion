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

    public function findAllProgrammingByMachine($id_machine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pg.id_programming, o.id_order, o.num_order, o.date_order, o.min_date, o.max_date, o.original_quantity AS quantity_order, o.accumulated_quantity, pg.quantity AS quantity_programming, p.id_product, 
                                             p.reference, p.product, m.id_machine, m.machine, c.client
                                      FROM programming pg
                                        INNER JOIN plan_orders o ON o.id_order = pg.id_order
                                        INNER JOIN products p ON p.id_product = pg.id_product
                                        INNER JOIN machines m ON m.id_machine = pg.id_machine
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                      WHERE pg.id_machine = :id_machine AND pg.id_company = :id_company");
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

        $stmt = $connection->prepare("SELECT * FROM plan_orders o
                                      WHERE o.id_company = :id_company
                                      AND o.id_order NOT IN (SELECT id_order FROM programming WHERE id_company = o.id_company) 
                                      AND o.status != 'Despacho'");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function setMinDateProgramming($id_programming, $min_date)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE programming SET min_date = :min_date
                                          WHERE id_programming = :id_programming");
            $stmt->execute([
                'id_programming' => $id_programming,
                'min_date' => $min_date
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
