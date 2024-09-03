<?php

namespace TezlikPlaneacion\Dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCompositeProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCompositeProductsByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        // $stmt = $connection->prepare("SELECT cp.id_composite_product, 0 AS id_product_material, cp.id_child_product, cp.id_product, p.reference, p.reference AS reference_material, p.product AS material, mg.id_magnitude, mg.magnitude, 
        //                                      u.id_unit, u.unit, u.abbreviation, cp.quantity, 'Producto' AS type
        //                               FROM products p 
        //                                 INNER JOIN composite_products cp ON cp.id_child_product = p.id_product 
        //                                 INNER JOIN convert_units u ON u.id_unit = cp.id_unit
        //                                 INNER JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
        //                               WHERE cp.id_company = :id_company AND p.active = 1 AND (SELECT active FROM products WHERE id_product = cp.id_product) = 1");
        $stmt = $connection->prepare("SELECT cp.id_composite_product, 0 AS id_product_material, cp.id_product, p.reference AS reference_product, p.product, cp.id_child_product, p.reference AS reference_material, 
                                             p.product AS material, (pi.quantity / cp.quantity) AS quantity, cp.quantity AS quantity_ftm, 'PRODUCTO' AS type,
                                             IFNULL(mg.id_magnitude, 0) AS id_magnitude, IFNULL(mg.magnitude, '') AS magnitude, IFNULL(u.id_unit, 0) AS id_unit, IFNULL(u.unit, '') AS unit, IFNULL(u.abbreviation, '') AS abbreviation 
                                      FROM products p 
                                        INNER JOIN composite_products cp ON cp.id_child_product = p.id_product 
                                        INNER JOIN products_inventory pi ON pi.id_product = cp.id_child_product
                                        LEFT JOIN convert_units u ON u.id_unit = cp.id_unit
                                        LEFT JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE p.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $compositeProducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProducts));
        return $compositeProducts;
    }

    public function findCompositeProduct($dataProduct)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_product = :id_product AND id_child_product = :id_child_product");
        $stmt->execute([
            'id_product' => $dataProduct['idProduct'],
            'id_child_product' => $dataProduct['compositeProduct']
        ]);
        $compositeProduct = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProduct));
        return $compositeProduct;
    }

    public function findCompositeProductCost($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $compositeProduct = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProduct));
        return $compositeProduct;
    }

    public function findCompositeProductByChild($id_child_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT cp.id_product, cp.id_child_product, cp.quantity
                                      FROM composite_products cp
                                      INNER JOIN products p ON p.id_product = cp.id_child_product
                                      WHERE cp.id_child_product = :id_child_product AND p.active = 1 
                                      GROUP BY cp.id_product");
        $stmt->execute([
            'id_child_product' => $id_child_product
        ]);
        $compositeProduct = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProduct));
        return $compositeProduct;
    }

    public function deleteCompositeProductByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM composite_products WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }

    public function deleteChildProductByProduct($id_child_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_child_product = :id_child_product");
        $stmt->execute(['id_child_product' => $id_child_product]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM composite_products WHERE id_child_product = :id_child_product");
            $stmt->execute(['id_child_product' => $id_child_product]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
