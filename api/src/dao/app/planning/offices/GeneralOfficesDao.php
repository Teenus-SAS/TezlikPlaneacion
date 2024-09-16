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

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, p.reference, pi.quantity, pi.minimum_stock, pi.accumulated_quantity, o.id_product, ps.status, o.num_order, o.date_order, o.original_quantity, p.product, c.client, o.min_date, o.max_date, o.delivery_date,
                                             IFNULL(uso.id_user, 0) AS id_user_order, IFNULL(uso.firstname, '') AS firstname_order, IFNULL(uso.lastname, '') AS lastname_order
                                      FROM plan_orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN inv_products pi ON pi.id_product = o.id_product
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                        INNER JOIN plan_status ps ON ps.id_status = o.status
                                        LEFT JOIN users uso ON uso.id_user = o.id_user_order
                                      WHERE o.status IN (2, 3) AND o.id_company = :id_company
                                      AND (o.delivery_date IS NULL OR MONTH(o.delivery_date) = MONTH(CURRENT_DATE)) ORDER BY `o`.`num_order` DESC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $orders = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $orders));
        return $orders;
    }

    public function findAllFilterOrders($min_date, $max_date, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT o.id_order, o.id_client, o.id_product, pi.minimum_stock, o.num_order, ps.status, o.date_order, o.original_quantity, p.reference, p.product, pi.quantity, pi.accumulated_quantity, c.client, o.min_date, o.max_date, o.delivery_date
                                             IFNULL(uso.id_user, 0) AS id_user_order, IFNULL(uso.firstname, '') AS firstname_order, IFNULL(uso.lastname, '') AS lastname_order
                                      FROM plan_orders o
                                        INNER JOIN products p ON p.id_product = o.id_product
                                        INNER JOIN inv_products pi ON pi.id_product = o.id_product
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                        INNER JOIN plan_status ps ON ps.id_status = o.status
                                        LEFT JOIN users uso ON uso.id_user = o.id_user_order
                                      WHERE o.id_company = :id_company AND (o.delivery_date BETWEEN :min_date AND :max_date) ORDER BY `o`.`status` ASC");
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
