<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralOfficesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllActualsOfficesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, p.reference, p.quantity, o.id_product, o.status, o.num_order, o.date_order, o.original_quantity, p.product, c.client, o.min_date, o.max_date, o.delivery_date
                                      FROM plan_orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                      WHERE o.status = 'Despacho' AND MONTH(o.delivery_date) = MONTH(CURRENT_DATE) AND o.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findAllFilterOrders($min_date, $max_date, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, o.id_product, o.num_order, o.status, o.date_order, o.original_quantity, p.product, c.client, o.min_date, o.max_date, o.delivery_date
                                      FROM plan_orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                      WHERE o.id_company = :id_company AND (o.date_order BETWEEN :min_date AND :max_date)");
        $stmt->execute([
            'min_date' => $min_date,
            'max_date' => $max_date,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $orders;
    }
}
