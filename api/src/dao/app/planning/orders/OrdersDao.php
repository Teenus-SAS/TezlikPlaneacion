<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class OrdersDao
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

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, o.id_product, o.num_order, ps.status, o.date_order, IFNULL(pi.accumulated_quantity, 0) AS accumulated_quantity, o.accumulated_quantity, o.original_quantity, p.reference, p.product, c.client, o.min_date, o.max_date, o.delivery_date,
                                             o.office_date, IFNULL(pi.classification, '') AS classification, s.id_seller, s.firstname, s.lastname
                                      FROM orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        LEFT JOIN inv_products pi ON pi.id_product = o.id_product
                                        INNER JOIN sellers s ON s.id_seller = o.id_seller
                                        INNER JOIN third_parties c ON c.id_client = o.id_client
                                        INNER JOIN orders_status ps ON ps.id_status = o.status
                                      WHERE o.status_order = 0 AND o.id_company = :id_company ORDER BY o.num_order DESC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function insertOrderByCompany($dataOrder, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO orders (num_order, date_order, min_date, max_date, id_company, id_product, id_seller, id_client, original_quantity, status) 
                                          VALUES (:num_order, :date_order, :min_date, :max_date, :id_company, :id_product, :id_seller, :id_client, :original_quantity, :status)");
            $stmt->execute([
                'num_order' => trim($dataOrder['order']),
                'date_order' => trim($dataOrder['dateOrder']),
                'min_date' => trim($dataOrder['minDate']),
                'max_date' => trim($dataOrder['maxDate']),
                'id_company' => $id_company,
                'id_product' => $dataOrder['idProduct'],
                'id_seller' => $dataOrder['idSeller'],
                'id_client' => $dataOrder['idClient'],
                'original_quantity' => trim($dataOrder['originalQuantity']),
                'status' => 1
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // if ($e->getCode() == 23000)
            //     $message = 'Pedido duplicado. Ingrese una nuevo pedido';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateOrder($dataOrder)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE orders SET date_order = :date_order, min_date = :min_date, max_date = :max_date, id_product = :id_product,
                                                 id_seller = :id_seller, id_client = :id_client, original_quantity = :original_quantity, status = :status, status_order = 0
                                          WHERE id_order = :id_order");
            $stmt->execute([
                'date_order' => trim($dataOrder['dateOrder']),
                'min_date' => trim($dataOrder['minDate']),
                'max_date' => trim($dataOrder['maxDate']),
                'id_product' => $dataOrder['idProduct'],
                'id_seller' => $dataOrder['idSeller'],
                'id_client' => $dataOrder['idClient'],
                'original_quantity' => trim($dataOrder['originalQuantity']),
                'id_order' => $dataOrder['idOrder'],
                'status' => 1
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteOrder($id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM orders WHERE id_order = :id_order");
            $stmt->execute(['id_order' => $id_order]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM orders WHERE id_order = :id_order");
                $stmt->execute(['id_order' => $id_order]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
