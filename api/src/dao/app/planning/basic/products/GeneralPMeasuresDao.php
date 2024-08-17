<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPMeasuresDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findProductMeasure($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $sql = "SELECT * FROM products_measures WHERE id_product = :id_product AND id_company = :id_company";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'id_product' => $dataProduct['idProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $product = $stmt->fetch($connection::FETCH_ASSOC);
        return $product;
    }

    public function deletePMeasure($id_product)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $sql = "SELECT * FROM products_measures WHERE id_product = :id_product";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_product' => $id_product]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM products_measures WHERE id_product = :id_product");
                $stmt->execute(['id_product' => $id_product]);
            }
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
