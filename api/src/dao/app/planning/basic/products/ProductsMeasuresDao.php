<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsMeasuresDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsMeasuresByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT
                    -- Columnas
                        IFNULL(pm.id_product_measure, 0) AS id_product_measure,
                        p.id_product,
                        p.id_product_type,
                        IFNULL(pt.product_type, '') AS product_type,
                        p.reference,
                        p.product,
                        p.composite,
                        p.origin,
                        p.img,
                        IFNULL(pm.width, 0) AS width,
                        IFNULL(pm.high, 0) AS high,
                        IFNULL(pm.length, 0) AS length,
                        IFNULL(pm.useful_length, 0) AS useful_length,
                        IFNULL(pm.total_width, 0) AS total_width,
                        IFNULL(pm.window, 0) AS window,
                        IFNULL(pm.inks, 0) AS inks
                FROM products p
                    LEFT JOIN products_measures pm ON pm.id_product = p.id_product
                    LEFT JOIN products_type pt ON pt.id_product_type = p.id_product_type
                WHERE p.id_company = :id_company
                ORDER BY p.reference ASC";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'id_company' => $id_company
        ]);

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function insertPMeasureByCompany($dataProduct, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $sql = "INSERT INTO products_measures (id_product, id_company, width, high, length, useful_length, 
                                                    total_width, window, inks) 
                    VALUES (:id_product, :id_company, :width, :high, :length, :useful_length, 
                            :total_width, :window, :inks)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataProduct['idProduct'],
                'width' => $dataProduct['width'],
                'high' => $dataProduct['high'],
                'length' => $dataProduct['length'],
                'useful_length' => $dataProduct['usefulLength'],
                'total_width' => $dataProduct['totalWidth'],
                'window' => $dataProduct['window'],
                'inks' => $dataProduct['inks'],
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updatePMeasure($dataProduct)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $sql = "UPDATE products_measures 
                    SET width = :width, high = :high, length = :length, useful_length = :useful_length, total_width = :total_width, window = :window, inks = :inks
                    WHERE id_product_measure = :id_product_measure";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id_product_measure' => $dataProduct['idProductMeasure'],
                'width' => $dataProduct['width'],
                'high' => $dataProduct['high'],
                'length' => $dataProduct['length'],
                'useful_length' => $dataProduct['usefulLength'],
                'total_width' => $dataProduct['totalWidth'],
                'window' => $dataProduct['window'],
                'inks' => $dataProduct['inks'],
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deletePMeasure($id_product_measure)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $sql = "SELECT * FROM products_measures WHERE id_product_measure = :id_product_measure";
            $stmt = $connection->prepare($sql);
            $stmt->execute(['id_product_measure' => $id_product_measure]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM products_measures WHERE id_product_measure = :id_product_measure");
                $stmt->execute(['id_product_measure' => $id_product_measure]);
            }
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
