<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsPlansDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsPlansByCompany($id_company, $id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pp.id_product_plan, p.id_product, p.reference, p.product, pi.quantity, pp.mechanical_plan, pp.assembly_plan
                                      FROM products_plans pp
                                        INNER JOIN products p ON p.id_product = pp.id_product
                                        LEFT JOIN products_inventory pi ON pi.id_product = pp.id_product
                                      WHERE pp.id_company = :id_company AND pp.id_product = :id_product");
        $stmt->execute([
            'id_company' => $id_company,
            'id_product' => $id_product,
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products_plans = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products_plans", array('products_plans' => $products_plans));
        return $products_plans;
    }

    public function insertProductPlanByCompany($dataProduct, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO products_plans (id_company, id_product, mechanical_plan, assembly_plan) 
                    VALUES (:id_company, :id_product, :mechanical_plan, :assembly_plan)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataProduct['idProduct'],
                'mechanical_plan' => $dataProduct['mechanicalFile'],
                'assembly_plan' => $dataProduct['assemblyFile']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function insertProductMechanicalPlanByCompany($dataProduct, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO products_plans (id_company, id_product, mechanical_plan) 
                    VALUES (:id_company, :id_product, :mechanical_plan)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataProduct['idProduct'],
                'mechanical_plan' => $dataProduct['mechanicalFile']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function insertProductAssemblyPlanByCompany($dataProduct, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO products_plans (id_company, id_product, assembly_plan) 
                    VALUES (:id_company, :id_product, :assembly_plan)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataProduct['idProduct'],
                'assembly_plan' => $dataProduct['assemblyFile']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateProductPlan($dataProduct)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE products_plans SET mechanical_plan = :mechanical_plan, assembly_plan = :assembly_plan
                                          WHERE id_product_plan = :id_product_plan");
            $stmt->execute([
                'id_product_plan' => $dataProduct['idProductPlan'],
                'mechanical_plan' => $dataProduct['mechanicalFile'],
                'assembly_plan' => $dataProduct['assemblyFile']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateProductMechanicalPlan($dataProduct)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE products_plans SET mechanical_plan = :mechanical_plan
                                          WHERE id_product_plan = :id_product_plan");
            $stmt->execute([
                'id_product_plan' => $dataProduct['idProductPlan'],
                'mechanical_plan' => $dataProduct['mechanicalFile'],
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateProductAssemblyPlan($dataProduct)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE products_plans SET assembly_plan = :assembly_plan
                                          WHERE id_product_plan = :id_product_plan");
            $stmt->execute([
                'id_product_plan' => $dataProduct['idProductPlan'],
                'assembly_plan' => $dataProduct['assemblyFile']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deleteProductPlan($id_product_plan)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM products_plans WHERE id_product_plan = :id_product_plan");
            $stmt->execute(['id_product_plan' => $id_product_plan]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM products_plans WHERE id_product_plan = :id_product_plan");
                $stmt->execute(['id_product_plan' => $id_product_plan]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
