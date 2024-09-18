<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PStockDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllStockByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT s.id_stock_product, p.id_product, p.reference, p.product, pi.quantity, s.max_term, s.min_term, p.composite, pi.classification
                                      FROM inv_stock_products s
                                        INNER JOIN products p ON p.id_product = s.id_product
                                        INNER JOIN inv_products pi ON pi.id_product = s.id_product
                                      WHERE s.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $stock = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("stock", array('stock' => $stock));
        return $stock;
    }

    public function insertStockByCompany($dataStock, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO inv_stock_products (id_company, id_product, max_term, min_term) 
                                          VALUES (:id_company, :id_product, :max_term, :min_term)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataStock['idProduct'],
                'min_term' => $dataStock['min'],
                'max_term' => $dataStock['max'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateStock($dataStock)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE inv_stock_products SET id_product = :id_product, max_term = :max_term, min_term = :min_term 
                                          WHERE id_stock_product = :id_stock_product");
            $stmt->execute([
                'id_stock_product' => $dataStock['idStock'],
                'id_product' => $dataStock['idProduct'],
                'min_term' => $dataStock['min'],
                'max_term' => $dataStock['max'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
