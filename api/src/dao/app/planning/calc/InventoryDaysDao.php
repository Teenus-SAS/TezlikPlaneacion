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

        /* SELECT (((:cantProducts/(january + february + march + april + june + july + august + september + november + december)/12)/4)/7) AS InventoryDays 
                                      FROM products_price_history
                                      WHERE id_product = :id_product 'cantProducts' => $dataInventory['cantProducts'],*/
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
