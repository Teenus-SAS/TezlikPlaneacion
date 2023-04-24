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

    public function findProductsByOrders($dataProgramming)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT m.id_machine, m.machine, p.id_product, p.product
                                      FROM products p
                                      INNER JOIN plan_cicles_machine pcm ON pcm.id_product = p.id_product
                                      INNER JOIN machines m ON m.id_machine = pcm.id_machine
                                      INNER JOIN plan_orders o ON o.id_product = p.id_product
                                      WHERE o.id_order = :id_order");
        $stmt->execute(['id_order' => $dataProgramming['idOrder']]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }
}
