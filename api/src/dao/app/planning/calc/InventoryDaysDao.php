<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class InventoryDaysDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcInventoryProductDays($id_product)
    {
        try {
            $connection = Connection::getInstance()->getconnection();
            $stmt = $connection->prepare("SELECT (pi.quantity / ((IFNULL(u.jan, 0) + IFNULL(u.feb, 0) + IFNULL(u.mar, 0) + IFNULL(u.apr, 0) + IFNULL(u.may, 0) + IFNULL(u.jun, 0) + IFNULL(u.jul, 0) + IFNULL(u.aug, 0) + IFNULL(u.sept, 0) + IFNULL(u.oct, 0) + IFNULL(u.nov, 0) + IFNULL(u.dece, 0)) / 
                                                 NULLIF((u.jan > 0) + (u.feb > 0) + (u.mar > 0) + (u.apr > 0) + (u.may > 0) + (u.jun > 0) + (u.jul > 0) + (u.aug > 0) + (u.sept > 0) + (u.oct > 0) + (u.nov > 0) + (u.dece > 0), 0)) * 
                                                 (SELECT days FROM sale_days WHERE month = MONTH(CURRENT_DATE()) AND year = YEAR(CURRENT_DATE()) AND id_company = pi.id_company)) AS days
                                      FROM products_inventory pi
                                      LEFT JOIN plan_unit_sales u ON u.id_product = pi.id_product
                                      WHERE pi.id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            // $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            $product = $stmt->fetch($connection::FETCH_ASSOC);

            return $product;
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updateInventoryProductDays($id_product, $inventoryDay)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_inventory SET days = :days WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $id_product,
                'days' => $inventoryDay
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function calcInventoryMaterialDays($id_material)
    {
        try {
            $connection = Connection::getInstance()->getconnection();
            $stmt = $connection->prepare("SELECT 
                                            IFNULL(
                                                SUM(
                                                    m.quantity / 
                                                    (
                                                        (
                                                            IFNULL(u.jan, 0) + IFNULL(u.feb, 0) + IFNULL(u.mar, 0) + IFNULL(u.apr, 0) + IFNULL(u.may, 0) + IFNULL(u.jun, 0) + 
                                                            IFNULL(u.jul, 0) + IFNULL(u.aug, 0) + IFNULL(u.sept, 0) + IFNULL(u.oct, 0) + IFNULL(u.nov, 0) + IFNULL(u.dece, 0)
                                                        ) / 
                                                        NULLIF(
                                                            (u.jan > 0) + (u.feb > 0) + (u.mar > 0) + (u.apr > 0) + (u.may > 0) + (u.jun > 0) + 
                                                            (u.jul > 0) + (u.aug > 0) + (u.sept > 0) + (u.oct > 0) + (u.nov > 0) + (u.dece > 0)
                                                        , 0)
                                                    ) * 
                                                    (
                                                        SELECT days FROM sale_days WHERE month = MONTH(CURRENT_DATE()) AND year = YEAR(CURRENT_DATE()) AND id_company = m.id_company
                                                    )
                                                )
                                            , 0) AS days
                                            FROM materials_inventory m
                                            INNER JOIN products_materials pm ON pm.id_material = m.id_material
                                            LEFT JOIN plan_unit_sales u ON u.id_product = pm.id_product
                                            WHERE m.id_material = :id_material");
            $stmt->execute(['id_material' => $id_material]);
            // $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            $product = $stmt->fetch($connection::FETCH_ASSOC);

            return $product;
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updateInventoryMaterialDays($id_material, $inventoryDay)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE materials_inventory SET days = :days WHERE id_material = :id_material");
            $stmt->execute([
                'id_material' => $id_material,
                'days' => $inventoryDay
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
