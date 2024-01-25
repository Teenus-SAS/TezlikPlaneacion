<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ClassificationDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcInventoryABCBYProduct($id_product, $months)
    {
        $connection = Connection::getInstance()->getConnection();

        // Calcular rotación, ventas al año y promedio unidades 
        $stmt = $connection->prepare("SELECT ((IF(IFNULL(u.jan, 0) > 0, 1, 0) + IF(IFNULL(u.feb, 0) > 0, 1, 0) + IF(IFNULL(u.mar, 0) > 0, 1, 0) + IF(IFNULL(u.apr, 0) > 0, 1, 0) + 
                                               IF(IFNULL(u.may, 0) > 0, 1, 0) + IF(IFNULL(u.jun, 0) > 0, 1, 0) + IF(IFNULL(u.jul, 0) > 0, 1, 0) + IF(IFNULL(u.aug, 0) > 0, 1, 0) + 
                                               IF(IFNULL(u.sept, 0) > 0, 1, 0) + IF(IFNULL(u.oct, 0) > 0, 1, 0) + IF(IFNULL(u.nov, 0) > 0, 1, 0) + IF(IFNULL(u.dece, 0) > 0, 1, 0)) / :cant_months) AS year_sales                                             
                                      FROM products p
                                      LEFT JOIN plan_unit_sales u ON u.id_product = p.id_product
                                      WHERE p.id_product = :id_product");
        $stmt->execute([
            'cant_months' => $months,
            'id_product' => $id_product
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $inventoryABC = $stmt->fetch($connection::FETCH_ASSOC);
        return $inventoryABC;
    }

    public function calcClassificationByProduct($year_sales, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT (IF(:years > (a / 100), 'A', IF(:years >= (b / 100), 'B', 'C'))) AS classification
                                          FROM inventory_abc
                                          WHERE id_company = :id_company");
            $stmt->execute([
                'years' => $year_sales,
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            $inventory = $stmt->fetch($connection::FETCH_ASSOC);
            return $inventory;
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
    // public function calcInventoryABC($dataInventory, $id_company)
    // {
    //     $connection = Connection::getInstance()->getConnection();

    //     // Calcular rotación, ventas al año y promedio unidades 
    //     $stmt = $connection->prepare("SELECT ((IF(jan > 0, 1, 0) + IF(feb > 0, 1, 0) + IF(mar > 0, 1, 0) + IF(apr > 0, 1, 0) + 
    //                                            IF(may > 0, 1, 0) + IF(jun > 0, 1, 0) + IF(jul > 0, 1, 0) + IF(aug > 0, 1, 0) + 
    //                                            IF(sept > 0, 1, 0) + IF(oct > 0, 1, 0) + IF(nov > 0, 1, 0) + IF(dece > 0, 1, 0)) / :cant_months) AS year_sales,
    //                                          ((jan + feb + mar + apr + may + jun + jul + aug + sept + oct + nov + dece)/:cant_months) AS average_units
    //                                   FROM plan_unit_sales 
    //                                   WHERE id_product = :id_product AND id_company = :id_company;");
    //     $stmt->execute([
    //         'cant_months' => $dataInventory['cantMonths'],
    //         'id_product' => $dataInventory['idProduct'],
    //         'id_company' => $id_company
    //     ]);
    //     $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    //     $inventoryABC = $stmt->fetch($connection::FETCH_ASSOC);
    //     return $inventoryABC;
    // }


    // public function calcClassificationByProduct($dataInventory, $id_company)
    // {
    //     // Calcular Ventas al año
    //     $inventoryABC = $this->calcInventoryABC($dataInventory, $id_company);

    //     if ($inventoryABC) {
    //         /* Crear Clasificación */
    //         if ($inventoryABC['year_sales'] > 0.83) $dataInventory['classification'] = 'A';
    //         else if ($inventoryABC['year_sales'] >= 0.50) $dataInventory['classification'] = 'B';
    //         else $dataInventory['classification'] = 'C';

    //         // Modificar clasificación en tabla products
    //         $this->updateProductClassification($dataInventory);
    //     } else {
    //         return 1;
    //     }
    // }

    public function updateProductClassification($id_product, $classification)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_inventory SET classification = :classification WHERE id_product = :id_product");
            $stmt->execute([
                'id_product' => $id_product,
                'classification' => $classification
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
