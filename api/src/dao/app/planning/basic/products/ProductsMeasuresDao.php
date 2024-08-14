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
        $stmt = $connection->prepare("SELECT pm.id_product_measure, pm.id_product, p.reference, p.product, p.img, pm.grammage, pm.width, pm.high, pm.length, pm.useful_length, pm.total_width, pm.window, pm.weight 
                                      FROM products_measures pm
                                        INNER JOIN products p ON p.id_product = pm.id_product
                                      WHERE pm.id_company = :id_company");
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

            $stmt = $connection->prepare("INSERT INTO products_measures (id_product, id_company, grammage, width, high, length, useful_length, total_width, window, weight) 
                                          VALUES (:id_product, :id_company, :grammage, :width, :high, :length, :useful_length, :total_width, :window, :weight)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataProduct['idProduct'],
                'grammage' => $dataProduct['grammage'],
                'width' => $dataProduct['width'],
                'high' => $dataProduct['high'],
                'length' => $dataProduct['length'],
                'useful_length' => $dataProduct['usefulLength'],
                'total_width' => $dataProduct['totalWidth'],
                'window' => $dataProduct['window'],
                'weight' => $dataProduct['weight']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updatePMeasure($dataProduct)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE products_measures SET grammage = :grammage, width = :width, high = :high, length = :length, useful_length = :useful_length, total_width = :total_width, window = :window, weight = :weight
                                          WHERE id_product_measure = :id_product_measure");
            $stmt->execute([
                'id_product_measure' => $dataProduct['idProductMeasure'],
                'grammage' => $dataProduct['grammage'],
                'width' => $dataProduct['width'],
                'high' => $dataProduct['high'],
                'length' => $dataProduct['length'],
                'useful_length' => $dataProduct['usefulLength'],
                'total_width' => $dataProduct['totalWidth'],
                'window' => $dataProduct['window'],
                'weight' => $dataProduct['weight']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deletePMeasure($id_product_measure)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM products_measures WHERE id_product_measure = :id_product_measure");
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
