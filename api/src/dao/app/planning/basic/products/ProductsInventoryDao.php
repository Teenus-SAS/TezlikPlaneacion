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

    public function insertProductsInventory($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO products_inventory (id_product, quantity, id_company) 
                                          VALUES (:id_product, :quantity, :id_company)");
            $stmt->execute([
                'id_product' => $dataProduct['idProduct'],
                'quantity' => $dataProduct['quantity'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateProductsInventory($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_inventory SET quantity = :quantity WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $dataProduct['idProduct'],
                'quantity' => $dataProduct['quantity']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteProductInventory($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM products_inventory WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM products_inventory WHERE id_product = :id_product");
                $stmt->execute(['id_product' => $id_product]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // if ($e->getCode() == 23000)
            //   $message = 'No es posible eliminar, el producto esta asociado a cotización';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
