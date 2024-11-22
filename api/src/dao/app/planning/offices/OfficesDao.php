<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class OfficesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllOfficesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, p.reference, pi.quantity, pi.minimum_stock, o.id_product, ps.status, o.num_order, o.date_order, o.original_quantity, p.product, c.client, o.min_date, o.max_date, o.delivery_date
                                      FROM orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN inv_products pi ON pi.id_product = p.id_product
                                        INNER JOIN third_parties c ON c.id_client = o.id_client
                                        INNER JOIN orders_status ps ON ps.id_status = o.status
                                      WHERE o.status IN (2, 3) AND o.id_company = :id_company
                                      ORDER BY o.num_order DESC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function updateDeliveryDate($dataOrder)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE orders SET delivery_date = :delivery_date WHERE id_order = :id_order");
            $stmt->execute([
                'delivery_date' => $dataOrder['date'],
                'id_order' => $dataOrder['idOrder']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);

            return $error;
        }
    }
}
