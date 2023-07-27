<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralOrdersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Obtener informacion pedido
    public function findOrdersByCompany($dataOrder, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.num_order, o.date_order, o.original_quantity, o.quantity, o.accumulated_quantity, p.product, c.client
                                      FROM plan_orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                      WHERE o.id_order = :id_order AND o.id_company = :id_company");
        $stmt->execute([
            'id_order' => $dataOrder['order'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("pedido", array('pedido' => $order));
        return $order;
    }

    public function findOrder($dataOrder, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_orders WHERE num_order = :num_order AND id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'num_order' => trim($dataOrder['order']),
            'id_product' => $dataOrder['idProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $orders = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findAllOrdersConcat($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT CONCAT(num_order, '-' , id_product) AS concate, id_order, id_product FROM plan_orders WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $orders;
    }

    public function checkAccumulatedQuantityOrder($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT (SELECT IFNULL(SUM(original_quantity), 0) FROM plan_orders WHERE id_product = p.id_product) AS accumulated_quantity, p.quantity 
                                      FROM products p 
                                      WHERE p.id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        return $order;
    }

    public function changeStatusOrder($num_order, $id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE plan_orders SET status_order = 0 WHERE num_order = :num_order AND id_product = :id_product");
            $stmt->execute([
                'num_order' => $num_order,
                'id_product' => $id_product,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function changeStatus($id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE plan_orders SET status = :status  WHERE id_order = :id_order");
            $stmt->execute([
                'status' => 'Despacho',
                'id_order' => $id_order
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
