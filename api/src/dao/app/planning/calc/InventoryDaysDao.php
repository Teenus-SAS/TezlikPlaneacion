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

    public function calcInventoryDays($dataInventory)
    {
        $connection = Connection::getInstance()->getconnection();

        /* SELECT (p.quantity / ((IFNULL(u.jan, 0) + IFNULL(u.feb, 0) + IFNULL(u.mar, 0) + IFNULL(u.apr, 0) + IFNULL(u.may, 0) + IFNULL(u.jun, 0) + IFNULL(u.jul, 0) + IFNULL(u.aug, 0) + IFNULL(u.sept, 0) + IFNULL(u.oct, 0) + IFNULL(u.nov, 0) + IFNULL(u.dece, 0)) / 12)) AS days
                                      FROM products p
                                      LEFT JOIN plan_unit_sales u ON u.id_product = p.id_product
                                      WHERE p.id_product = */
        $stmt = $connection->prepare("SELECT (((p.product / (pph.january + pph.february + pph.march + pph.april + pph.june + pph.july + pph.august + pph.september + pph.november + pph.december)/12)/4)/7) AS inventory_day 
                                      FROM products p
                                      INNER JOIN products_price_history pph ON pph.id_product = p.id_product
                                      WHERE p.id_product = :id_product");
        $stmt->execute(['id_product' => $dataInventory['idProduct']]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $inventoryDays = $stmt->fetch($connection::FETCH__ASSOC);

        $this->updateInventoryDays($dataInventory, $inventoryDays['inventory_day']);
    }

    public function updateInventoryDays($dataInventory, $inventoryDay)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_price_history SET inventory_day = :inventory_day WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $dataInventory['idProduct'],
                'inventory_day' => $inventoryDay
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
