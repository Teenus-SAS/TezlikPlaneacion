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

        $stmt = $connection->prepare("SELECT 
                                            -- Columnas
                                                o.id_order, 
                                                o.id_client, 
                                                o.id_product, 
                                                o.num_order, 
                                                o.type_order, 
                                                IFNULL(ps.status, '') AS status, 
                                                o.date_order, 
                                                o.original_quantity, 
                                                IFNULL(p.product, '') AS product, 
                                                IFNULL(p.origin, 0) AS origin,
                                                IFNULL(c.client, ''), 
                                                o.min_date, 
                                                o.max_date, 
                                                o.delivery_date, 
                                                IFNULL(pi.quantity, 0) AS quantity_pro, 
                                                IFNULL(pi.accumulated_quantity, 0) AS accumulated_quantity,
                                                CONCAT(o.num_order, '-' , o.id_product) AS concate                                      
                                      FROM orders o
                                        LEFT JOIN products p ON p.id_product = o.id_product
                                        LEFT JOIN inv_products pi ON pi.id_product = o.id_product
                                        LEFT JOIN third_parties c ON c.id_client = o.id_client  
                                        LEFT JOIN orders_status ps ON ps.id_status = o.status
                                      WHERE o.status_order = 0 AND o.id_company = :id_company AND o.status NOT IN (3)
                                      ORDER BY o.status ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findAllChildrenOrders($num_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM `orders` WHERE num_order LIKE '$num_order-%'");
        $stmt->execute();

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findAllOrdersWithMaterialsByCompany($id_company)
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
                                            o.original_quantity,
                                            p.product,
                                            c.client,
                                            o.min_date,
                                            o.max_date,
                                            pi.status_ds,
                                            mi.id_material,
                                            o.delivery_date,
                                            pi.accumulated_quantity,
                                            IFNULL(mi.quantity, 0) AS quantity_material,
                                            IFNULL(
                                                (
                                                SELECT
                                                    id_programming
                                                FROM
                                                    programming
                                                WHERE
                                                    id_order = o.id_order
                                                LIMIT 1
                                            ),
                                            0
                                            ) AS programming
                                        FROM orders o
                                            INNER JOIN products p ON p.id_product = o.id_product
                                            LEFT JOIN products_materials pm ON pm.id_product = o.id_product
                                            LEFT JOIN inv_materials m ON mi.id_material = pm.id_material
                                            INNER JOIN inv_products PI ON pi.id_product = o.id_product
                                            INNER JOIN third_parties c ON c.id_client = o.id_client
                                            INNER JOIN orders_status ps ON ps.id_status = o.status
                                        WHERE o.status_order = 0 AND o.id_company = :id_company AND o.status NOT IN(3, 7, 8) AND o.max_date != '0000-00-00'
                                        ORDER BY p.id_product ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findAllOrdersByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM orders WHERE id_product = :id_product");
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

        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            o.id_order,
                                            o.num_order,
                                            o.date_order,
                                            o.original_quantity,
                                            o.accumulated_quantity,
                                            p.product,
                                            c.client
                                        FROM orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN third_parties c ON c.id_client = o.id_client
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

        $stmt = $connection->prepare("SELECT * FROM orders WHERE num_order = :num_order AND id_product = :id_product AND id_company = :id_company");
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

    public function findSameOrder($dataOrder)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM orders 
                                      WHERE min_date = :min_date AND max_date = :max_date AND id_seller = :id_seller 
                                            AND original_quantity = :original_quantity AND id_product = :id_product AND 
                                            id_client = :id_client AND status = 1");
        $stmt->execute([
            'min_date' => trim($dataOrder['minDate']),
            'max_date' => trim($dataOrder['maxDate']),
            'id_product' => $dataOrder['idProduct'],
            'id_seller' => $dataOrder['idSeller'],
            'id_client' => $dataOrder['idClient'],
            'original_quantity' => trim($dataOrder['originalQuantity']),
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $orders = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findLastSameOrder($dataOrder)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM orders 
                                      WHERE min_date = '0000-00-00' AND max_date = '0000-00-00' 
                                      -- AND num_order = :num_order 
                                      AND id_product = :id_product 
                                      AND id_client = :id_client");
        $stmt->execute([
            // 'num_order' => $dataOrder['order'],
            'id_product' => $dataOrder['idProduct'],
            'id_client' => $dataOrder['idClient']
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $orders = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function checkAccumulatedQuantityOrder($id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.original_quantity, pi.quantity, pi.accumulated_quantity, ps.status
                                      FROM orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN inv_products pi ON pi.id_product = o.id_product
                                        INNER JOIN orders_status ps ON ps.id_status = o.status
                                      WHERE o.id_order = :id_order");
        $stmt->execute([
            'id_order' => $id_order
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        return $order;
    }

    public function findLastNumOrderByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT CONCAT('PED', COUNT(IFNULL(id_order, 0)) + 1) AS num_order 
                FROM orders 
                WHERE id_company = :id_company AND num_order NOT LIKE '%-%'";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        return $order;
    }

    public function findLastOrderByNumOrder($num_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT CONCAT('$num_order', '-', COUNT(IFNULL(id_order, 0)) + 1) AS num_order 
                FROM orders 
                WHERE num_order LIKE '$num_order-%'";
        $stmt = $connection->prepare($sql);
        $stmt->execute();

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        return $order;
    }

    public function findOrderByProductAndClient($dataOrder)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT *
                FROM orders 
                WHERE id_product = :id_product
                AND id_client = :id_client";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'id_product' => $dataOrder['idProduct'],
            'id_client' => $dataOrder['idClient']
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $order;
    }

    public function changeStatusOrder($num_order, $id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE orders SET status_order = 0 WHERE num_order = :num_order AND id_product = :id_product");
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
            $stmt = $connection->prepare("UPDATE orders SET status = :status WHERE id_order = :id_order");
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

    public function changeStatusByNumOrder($num_order, $status)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE orders SET status = :status WHERE num_order = :num_order");
            $stmt->execute([
                'status' => $status,
                'num_order' => $num_order
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function changeStatusByProduct($id_product, $status)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE orders SET status = :status WHERE id_product = :id_product");
            $stmt->execute([
                'status' => $status,
                'id_product' => $id_product
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

            $dataOrder['accumulated_quantity_order'] == '' ? $quantity = null : $quantity = $dataOrder['accumulated_quantity_order'];

            $stmt = $connection->prepare("UPDATE orders SET accumulated_quantity = :accumulated_quantity WHERE id_order = :id_order");
            $stmt->execute([
                'accumulated_quantity' => $quantity,
                'id_order' => $dataOrder['id_order']
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

            $stmt = $connection->prepare("UPDATE orders SET office_date = :office_date WHERE id_order = :id_order");
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

    public function updateNeedOrder($id_order, $need)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE plan_orders SET need = :need WHERE id_order = :id_order");
            $stmt->execute([
                'need' => $need,
                'id_order' => $id_order
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteOrderByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM orders WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM orders WHERE id_product = :id_product");
                $stmt->execute(['id_product' => $id_product]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
