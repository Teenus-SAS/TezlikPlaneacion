<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsTypeDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsTypeByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM products_type
                                      WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $Products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("Products", array('Products' => $Products));
        return $Products;
    }

    public function findProductsType($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM products_type
                                      WHERE product_type = :product_type AND id_company = :id_company");
        $stmt->execute([
            'product_type' => trim(strtoupper($dataProduct['productType'])),
            'id_company' => $id_company
        ]);
        $material = $stmt->fetch($connection::FETCH_ASSOC);
        return $material;
    }

    public function insertProductsTypeByCompany($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO products_type (id_company, product_type) 
                                          VALUES (:id_company ,:product_type)");
            $stmt->execute([
                'id_company' => $id_company,
                'product_type' => strtoupper(trim($dataProduct['productType']))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateProductType($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_type SET product_type = :product_type WHERE id_product_type = :id_product_type");
            $stmt->execute([
                'id_product_type' => $dataProduct['idProductType'],
                'product_type' => strtoupper(trim($dataProduct['productType']))
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteProductType($id_product_type)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM products_type WHERE id_product_type = :id_product_type");
            $stmt->execute(['id_product_type' => $id_product_type]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM products_type WHERE id_product_type = :id_product_type");
                $stmt->execute(['id_product_type' => $id_product_type]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
