<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findProductsMaterialsByCompany($arr, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pm.quantity 
                                      FROM products_materials pm
                                      INNER JOIN materials m ON m.id_material = pm.id_material
                                      WHERE pm.id_product = :id_product AND pm.id_company = :id_company
                                      AND m.id_material_type = :id_material_type
                                      ORDER BY `pm`.`id_product_material` ASC LIMIT 1");
        $stmt->execute([
            'id_product' => $arr['idProduct'],
            'id_material_type' => $arr['type'],
            'id_company' => $id_company
        ]);
        $productsmaterial = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("product", array('product' => $productsmaterial));
        return $productsmaterial;
    }

    public function findAllProductsMaterials($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT
                                      -- Columnas
                                            p.id_product,
                                            p.reference AS reference_product,
                                            p.product,
                                            m.id_material,
                                            m.reference AS reference_material,
                                            m.material,
                                            m.cost,
                                            (
                                                mi.quantity / pm.quantity_converted
                                            ) AS quantity,
                                            pm.quantity AS quantity_ftm,
                                            'MATERIAL' AS type,
                                            IFNULL(mg.id_magnitude, 0) AS id_magnitude,
                                            IFNULL(mg.magnitude, '') AS magnitude,
                                            IFNULL(u.id_unit, 0) AS id_unit,
                                            IFNULL(u.unit, '') AS unit,
                                            IFNULL(u.abbreviation, '') AS abbreviation
                                            -- ((m.quantity / pm.quantity) - IFNULL((SELECT SUM(quantity) FROM programming WHERE id_product = pm.id_product), 0)) AS quantity
                                      FROM products p
                                            INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                            INNER JOIN materials m ON m.id_material = pm.id_material
                                            INNER JOIN inv_materials mi ON mi.id_material = pm.id_material
                                            LEFT JOIN admin_units u ON u.id_unit = pm.id_unit
                                            LEFT JOIN admin_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE pm.id_company = :id_company
                                            ORDER BY m.material ASC");
        $stmt->execute(['id_company' => $id_company]);
        $productsmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsmaterials));
        return $productsmaterials;
    }

    public function findAllDistinctMaterials($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pm.id_material
                                      FROM products_materials pm 
                                        INNER JOIN sales_by_units us ON us.id_product = pm.id_product 
                                      WHERE pm.id_company = :id_company
                                      GROUP BY pm.id_material");
        $stmt->execute(['id_company' => $id_company]);
        $productsmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsmaterials));
        return $productsmaterials;
    }

    // Consultar si existe el product_material en la BD
    public function findProductMaterial($dataProductMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_materials
                                      WHERE id_product = :id_product AND id_material = :id_material");
        $stmt->execute([
            'id_product' => $dataProductMaterial['idProduct'],
            'id_material' => $dataProductMaterial['material']
        ]);
        $findProductMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProductMaterial;
    }

    // Consultar si existe el product_material en la BD
    public function findAllProductByMaterial($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pm.id_product_material, pm.id_material, pm.id_product, pi.quantity
                                      FROM products_materials pm
                                      INNER JOIN inv_products pi ON pi.id_product = pm.id_product
                                      INNER JOIN sales_by_units u ON u.id_product = pm.id_product
                                      WHERE id_material = :id_material");
        $stmt->execute(['id_material' => $id_material]);

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function saveQuantityConverted($id_product_material, $quantity)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE products_materials SET quantity_converted = :quantity_converted
                                      WHERE id_product_material = :id_product_material");
            $stmt->execute([
                'id_product_material' => $id_product_material,
                'quantity_converted' => $quantity
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    // Borrar productos materia prima general
    public function deleteProductMaterialByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_materials WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM products_materials WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
