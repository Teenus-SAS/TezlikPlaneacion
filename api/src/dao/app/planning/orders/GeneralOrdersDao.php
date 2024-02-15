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

    public function findAllOrdersByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, o.id_product, o.num_order, o.status, o.date_order, o.original_quantity, p.product, c.client, o.min_date, o.max_date, o.delivery_date, pi.accumulated_quantity
                                      FROM plan_orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN products_inventory pi ON pi.id_product = o.id_product
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client  
                                      WHERE o.status_order = 0 AND o.id_company = :id_company AND o.status != 'Entregado'
                                      ORDER BY o.status ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findAllOrdersWithMaterialsByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, o.id_product, o.num_order, o.status, o.date_order, o.original_quantity, p.product, c.client, o.min_date, o.max_date, o.delivery_date, pi.accumulated_quantity, IFNULL(m.quantity, 0) AS quantity_material,
                                             IFNULL((SELECT id_programming FROM programming WHERE id_order = o.id_order LIMIT 1), 0) AS programming
                                      FROM plan_orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        LEFT JOIN products_materials pm ON pm.id_product = o.id_product
                                        LEFT JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN products_inventory pi ON pi.id_product = o.id_product
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client  
                                      WHERE o.status_order = 0 AND o.id_company = :id_company AND o.status != 'Entregado' AND o.status != 'En Produccion' AND o.status != 'Fabricado'");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findAllOrdersByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_orders WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
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

    public function findLastNumOrder($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT (IFNULL(MAX(num_order), 0) + 1) AS num_order 
                                      FROM plan_orders 
                                      WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetch($connection::FETCH_ASSOC);
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

    public function checkAccumulatedQuantityOrder($id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.original_quantity, p.quantity, pi.accumulated_quantity, o.status
                                      FROM plan_orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN products_inventory pi ON pi.id_product = o.id_product
                                      WHERE o.id_order = :id_order");
        $stmt->execute([
            'id_order' => $id_order
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

    public function changeStatus($id_order, $status)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE plan_orders SET status = :status WHERE id_order = :id_order");
            $stmt->execute([
                'status' => $status,
                'id_order' => $id_order
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateAccumulatedOrder($dataOrder)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            isset($dataOrder['accumulatedQuantity']) == '' ? $quantity = null : $quantity = $dataOrder['accumulatedQuantity'];

            $stmt = $connection->prepare("UPDATE plan_orders SET accumulated_quantity = :accumulated_quantity WHERE id_order = :id_order");
            $stmt->execute([
                'accumulated_quantity' => $quantity,
                'id_order' => $dataOrder['order']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateOfficeDate($id_order, $date)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE plan_orders SET office_date = :office_date WHERE id_order = :id_order");
            $stmt->execute([
                'office_date' => $date,
                'id_order' => $id_order
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
