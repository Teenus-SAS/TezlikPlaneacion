<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsInventoryDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }
    public function insertProductsInventory($id_product, $quantity, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $quantity = str_replace('.', '', $quantity);
            $quantity = str_replace(',', '.', $quantity);

            $stmt = $connection->prepare("INSERT INTO products_inventory (id_product, quantity, id_company) VALUES (:id_product, :quantity, :id_company)");
            $stmt->execute([
                'id_product' => $id_product,
                'quantity' => $quantity,
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
    public function updateProductsInventory($id_product, $quantity)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $quantity = str_replace('.', '', $quantity);
            $quantity = str_replace(',', '.', $quantity);

            $stmt = $connection->prepare("UPDATE products_inventory SET quantity = :quantity WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $id_product,
                'quantity' => $quantity
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
