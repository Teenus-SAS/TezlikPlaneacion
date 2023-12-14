<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MinimumStockDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // public function calcMinimumStock($dataStock, $id_company)
    // {
    //     $connection = Connection::getInstance()->getconnection();
    //     $stmt = $connection->prepare("SELECT 
    //                                         (((us.jan + us.feb + us.mar + us.apr + us.may + us.jun + us.jul + us.aug + us.sept + us.oct + us.nov + us.dece) / 
    //                                         (ppm.january + ppm.february + ppm.march + ppm.april + ppm.may + ppm.june + ppm.july + ppm.august + ppm.september + ppm.october + ppm.november + ppm.december)) * pp.lead_time) AS minimum_stock 
    //                                   FROM plan_unit_sales us 
    //                                     INNER JOIN plan_cicles_machine cm ON cm.id_product = us.id_product 
    //                                     INNER JOIN plan_program_machines ppm ON ppm.id_machine = cm.id_machine 
    //                                     INNER JOIN products_materials pm ON pm.id_product = us.id_product
    //                                     INNER JOIN products_providers pp ON pp.id_material = pm.id_material
    //                                   WHERE us.id_product = :id_product AND us.id_company = :id_company");
    //     $stmt->execute([
    //         'id_product' => $dataStock['idProduct'],
    //         'id_company' => $id_company
    //     ]);
    //     $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    //     $minimumStock = $stmt->fetch($connection::FETCH_ASSOC);
    //     return $minimumStock;
    // }

    public function calcStockByProduct($id_product)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("SELECT SUM(IFNULL(m.minimum_stock, 0) * IFNULL(pm.quantity, 0)) AS stock
                                          FROM products_materials pm
                                          LEFT JOIN materials m ON m.id_material = pm.id_material
                                          WHERE pm.id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            $product = $stmt->fetch($connection::FETCH_ASSOC);
            return $product;
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }

    public function calcStockByMaterial($id_material)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("SELECT SUM((IFNULL(s.max_term, 0) - IFNULL(s.usual_term, 0)) * (((IFNULL(u.jan, 0) + IFNULL(u.feb, 0) + IFNULL(u.mar, 0) + IFNULL(u.apr, 0) + IFNULL(u.may, 0) + IFNULL(u.jun, 0) + IFNULL(u.jul, 0) + IFNULL(u.aug, 0) + IFNULL(u.sept, 0) + IFNULL(u.oct, 0) + IFNULL(u.nov, 0) + IFNULL(u.dece, 0)) / 
                                                 NULLIF((u.jan > 0) + (u.feb > 0) + (u.mar > 0) + (u.apr > 0) + (u.may > 0) + (u.jun > 0) + (u.jul > 0) + (u.aug > 0) + (u.sept > 0) + (u.oct > 0) + (u.nov > 0) + (u.dece > 0), 0))/ 12) * pm.quantity) AS stock  
                                          FROM products_materials pm
                                          LEFT JOIN stock s ON s.id_material = pm.id_material
                                          LEFT JOIN plan_unit_sales u ON u.id_product = pm.id_product
                                          WHERE pm.id_material = :id_material");
            $stmt->execute(['id_material' => $id_material]);
            $material = $stmt->fetch($connection::FETCH_ASSOC);
            return $material;
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }
}
