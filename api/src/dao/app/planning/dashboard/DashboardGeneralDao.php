<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DashboardGeneralDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findClassificationInvByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT SUM(CASE WHEN classification = 'A' THEN 1 ELSE 0 END) AS A, SUM(CASE WHEN classification = 'B' THEN 1 ELSE 0 END) AS B,
                                             SUM(CASE WHEN classification = 'C' THEN 1 ELSE 0 END) AS C
                                      FROM products_inventory
                                      WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $classification = $stmt->fetch($connection::FETCH_ASSOC);
        return $classification;
    }

    public function findProductsOutOfStock($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT (COUNT(CASE WHEN pi.quantity = 0 THEN 1 END) * 100.0 / COUNT(*)) AS productsOutStock 
                FROM products_inventory pi
                WHERE id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findOrdersNoProgramm($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT (COUNT(CASE WHEN po.status IN (1, 5, 6, 9) THEN 1 END) * 100.0 / COUNT(*)) AS ordersNoProgramed 
                FROM plan_orders po
                WHERE id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findOrdersNoMP($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT (COUNT(CASE WHEN po.status IN (6) THEN 1 END) * 100.0 / COUNT(*)) AS OrdersNoMP 
                FROM plan_orders po
                WHERE id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findOrdersDelivered($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT status, COUNT(*) AS total_pedidos
                FROM plan_orders
                WHERE
                    (status = 2 OR status = 3)
                    AND max_date <> 0
                    AND (
                        (status = 2 AND YEAR(date_order) = YEAR(CURDATE()) AND MONTH(date_order) <= MONTH(CURDATE()))
                        OR (status = 3 AND YEAR(date_order) = YEAR(CURDATE()) AND MONTH(date_order) = MONTH(CURDATE()))
                    )
                WHERE id_company = :id_company
                GROUP BY status";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findOrdersDeliveredOnTime($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT ROUND( SUM( CASE WHEN delivery_date IS NOT NULL AND delivery_date <= max_date THEN 1 WHEN delivery_date IS NULL AND CURDATE() <= max_date THEN 1 ELSE 0 END ) / COUNT(*) * 100, 2 ) AS deliveredOnTime 
                FROM plan_orders 
                WHERE max_date <> '0000-00-00' AND id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findPendignOC($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT
                    COUNT(*) AS total_requisiciones,
                    SUM(CASE WHEN application_date <> '0000-00-00' THEN 1 ELSE 0 END) AS requisiciones_cumplidas,
                    ROUND((SUM(CASE WHEN application_date <> '0000-00-00' THEN 1 ELSE 0 END) / COUNT(*) * 100), 2) AS participacion
                FROM
                    requisitions
                WHERE id_company = :id_company;";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $quantityOC = $stmt->fetch($connection::FETCH_ASSOC);
        return $quantityOC;
    }

    public function findOrderxDay($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $mes = date('m'); // Mes actual
        $anio = date('Y'); // Año actual        

        $sql = "SELECT DATE(date_order) AS day, COUNT(*) AS total_orders
                FROM plan_orders
                WHERE 
                    MONTH(date_order) = $mes  -- Reemplaza <mes> por el número del mes que deseas filtrar (Ej: 8 para agosto)
                    AND YEAR(date_order) = $anio  -- Reemplaza <año> por el año que deseas filtrar
                    AND id_company = :id_company
                GROUP BY DATE(date_order)
                ORDER BY DATE(date_order) ASC";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $orderxDay = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $orderxDay;
    }

    public function findQuantityOrdersByClients($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT pc.client, COUNT(po.id_client) AS total_pedidos
                FROM plan_orders po
                INNER JOIN plan_clients pc ON po.id_client = pc.id_client
                WHERE po.id_company = :id_company
                GROUP BY pc.client
                ORDER BY total_pedidos DESC
                LIMIT 10";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $quantityOrdersByClients = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $quantityOrdersByClients;
    }
}
