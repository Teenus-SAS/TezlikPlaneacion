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

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            IFNULL(
                                                SUM(
                                                    CASE WHEN classification = 'A' THEN 1 ELSE 0
                                                    END
                                                )
                                            , 0) AS A,
                                            IFNULL(
                                                SUM(
                                                    CASE WHEN classification = 'B' THEN 1 ELSE 0
                                                    END
                                                )
                                            , 0) AS B,
                                            IFNULL(
                                                SUM(
                                                    CASE WHEN classification = 'C' THEN 1 ELSE 0
                                                    END
                                                )
                                            , 0) AS C
                                      FROM inv_products
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
        $sql = "SELECT 
                    -- Columnas
                        IFNULL(
                            (
                                COUNT(
                                    CASE WHEN pi.quantity = 0 THEN 1
                                    END
                                ) * 100.0 / 
                                COUNT(*)
                            )
                        , 0) AS productsOutStock,
                        COUNT(*) AS totalProducts
                FROM inv_products pi
                WHERE id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findMPOutOfStock($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT 
                    -- Columnas
                        IFNULL(
                            COUNT(
                                CASE WHEN(mi.quantity - mi.reserved) < mi.minimum_stock THEN 1
                                END
                            )
                        , 0) AS totalMPLowStock,
                        COUNT(*) AS total_materiales,
                        IFNULL(
                            (
                                COUNT(
                                    CASE WHEN(mi.quantity - mi.reserved) < mi.minimum_stock THEN 1
                                    END
                                ) / COUNT(*)
                            ) * 100
                        , 0) AS percentageMPLowStock
                FROM inv_materials mi
                INNER JOIN materials m ON mi.id_material = m.id_material
                WHERE m.id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findAllActiveOrders($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT 
                    -- Columnas
                        IFNULL(
                                SUM(
                                    CASE WHEN type_order = 1 THEN 1 ELSE 0
                                    END
                                )
                        , 0) AS orders_clients,
                        IFNULL(
                                SUM(
                                    CASE WHEN type_order = 2 THEN 1 ELSE 0
                                    END
                                )
                        , 0) AS orders_internalClients
                FROM `orders` 
                WHERE status <> 3 
                    AND id_company = :id_company;";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findOrdersNoProgramm($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT 
                      -- Columnas
                        IFNULL(
                            COUNT(
                                CASE WHEN po.status IN(1, 5, 6, 9) THEN 1
                                END
                            )
                        , 0) AS totalOrdersNoProgrammed,
                        IFNULL(
                            (
                                COUNT(
                                    CASE WHEN po.status IN(1, 5, 6, 9) THEN 1
                                    END
                                ) * 100.0 / COUNT(*)
                            )
                        , 0) AS ordersNoProgramed 
                FROM orders po
                WHERE max_date <> '0000-00-00' AND id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findOrdersNoMP($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT 
                    -- Columnas
                        IFNULL(
                            (
                                COUNT(
                                    CASE WHEN po.status IN(6) THEN 1
                                    END
                                ) * 100.0 / COUNT(*)
                            )
                        , 0) AS OrdersNoMP 
                FROM orders po
                WHERE id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findOrdersDelivered($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT
                    COUNT(*) AS total_orders,
                    SUM(CASE WHEN status IN (3) THEN 1 ELSE 0 END) AS orders_dispatch,
                    ROUND(
                        (SUM(CASE WHEN status IN (3) THEN 1 ELSE 0 END) / COUNT(*)) * 100,
                        2
                    ) AS percentage_dispatch
                FROM orders
                WHERE max_date <> '0000-00-00' AND id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $percent = $stmt->fetch($connection::FETCH_ASSOC);
        return $percent;
    }

    public function findOrdersDeliveredOnTime($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT
                    -- Columnas
                        COUNT(*) AS totalDeliveredOnTime,
                        IFNULL(
                            ROUND(
                                SUM(
                                    CASE WHEN delivery_date IS NOT NULL AND delivery_date <= max_date THEN 1 WHEN delivery_date IS NULL AND CURDATE() <= max_date THEN 1 ELSE 0
                                    END) / COUNT(*) * 100,
                                2
                            ),
                            0
                        ) AS deliveredOnTime
                FROM orders
                WHERE max_date <> '0000-00-00' AND status = 3 AND id_company = :id_company";
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
                    IFNULL(SUM(CASE WHEN application_date <> '0000-00-00' THEN 1 ELSE 0 END), 0) AS requisiciones_cumplidas,
                    IFNULL(ROUND((SUM(CASE WHEN application_date <> '0000-00-00' THEN 1 ELSE 0 END) / COUNT(*) * 100), 2), 0) AS participacion
                FROM
                    requisitions_materials
                WHERE id_company = :id_company";
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

        $sql = "SELECT 
                    DATE(date_order) AS day,            -- Extrae solo la parte de la fecha (sin hora)
                    type_order,                         -- Agrupa por el tipo de orden
                    COUNT(id_order) AS total_orders     -- Cuenta el número de órdenes por cada tipo y día
                FROM orders
                WHERE 
                    -- YEAR(date_order) = YEAR(CURDATE())  -- Año actual
                    -- AND MONTH(date_order) = MONTH(CURDATE())  -- Mes actual
                    -- AND 
                    id_company = :id_company
                GROUP BY 
                    DATE(date_order),                    -- Agrupa por día
                    type_order
                ORDER BY 
                    DATE(date_order),                    -- Ordena por día
                    type_order
                LIMIT 10;";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_company' => $id_company]);
        $orderxDay = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $orderxDay;
    }

    public function findQuantityOrdersByClients($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT pc.client, COUNT(po.id_client) AS total_pedidos
                FROM orders po
                INNER JOIN third_parties pc ON po.id_client = pc.id_client
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
