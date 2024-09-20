<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ExplosionProductsdao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllExplosionProductsByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            exp.id_explosion_product,
                                            IFNULL(
                                                    (
                                                        SELECT GROUP_CONCAT(oo.num_order)
                                                        FROM orders oo
                                                        INNER JOIN plan_explosions_products cexp ON cexp.order LIKE CONCAT('%', oo.id_order, '%')
                                                        WHERE cexp.id_explosion_product = exp.id_explosion_product
                                                    )
                                            , 0) AS num_order,
                                            p.reference AS reference_material,
                                            p.product AS material,
                                            'UND' AS abbreviation,
                                            pi.quantity AS quantity_material,
                                            pi.minimum_stock,
                                            0 AS transit,
                                            exp.need,
                                            exp.available 
                                      FROM plan_explosions_products exp
                                        INNER JOIN products p ON p.id_product = exp.id_product
                                        INNER JOIN inv_products pi ON pi.id_product = exp.id_product
                                      WHERE exp.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);

        $this->logger->notice("pedidos", array('pedidos' => $products));
        return $products;
    }

    public function insertNewEXPByCompany($dataEXP, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO plan_explosions_products (id_company, `order`, id_product, need, available)
                                    VALUES (:id_company, :order, :id_product, :need, :available)");
            $stmt->execute([
                'id_company' => $id_company,
                'order' => $dataEXP['id_order'],
                'id_product' => $dataEXP['id_child_product'],
                'need' => $dataEXP['need'],
                'available' => $dataEXP['available'],
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateEXProduct($dataEXP)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE plan_explosions_products SET `order` = :order, id_product = :id_product, need = :need, available = :available
                                          WHERE id_explosion_product = :id_explosion_product");
            $stmt->execute([
                'id_explosion_product' => $dataEXP['id_explosion_product'],
                'order' => $dataEXP['id_order'],
                'id_product' => $dataEXP['id_child_product'],
                'need' => $dataEXP['need'],
                'available' => $dataEXP['available'],
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deleteEXProduct($id_explosion_product)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM plan_explosions_products WHERE id_explosion_product = :id_explosion_product");
            $stmt->execute(['id_explosion_product' => $id_explosion_product]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM plan_areas WHERE id_explosion_product = :id_explosion_product");
                $stmt->execute(['id_explosion_product' => $id_explosion_product]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return ['info' => true, 'message' => $message];
        }
    }
}
