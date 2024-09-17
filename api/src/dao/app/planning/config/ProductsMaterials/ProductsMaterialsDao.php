<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsMaterials($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pm.id_product, pm.id_product_material, pm.id_material, m.reference, m.material, mi.status, IFNULL(mg.id_magnitude, 0) AS id_magnitude, m.id_material_type, 'MATERIAL' AS type,
                                             IFNULL(mg.magnitude, '') AS magnitude, IFNULL(u.id_unit, 0) AS id_unit, IFNULL(u.unit, '') AS unit, IFNULL(u.abbreviation, '') AS abbreviation, pm.quantity, (mi.quantity - mi.reserved) AS quantity_material, mi.reserved,
                                             ((mi.quantity / pm.quantity_converted) - IFNULL((SELECT SUM(quantity) FROM programming WHERE id_product = pm.id_product), 0)) AS total_quantity
                                      FROM products_materials pm
                                        LEFT JOIN materials m ON m.id_material = pm.id_material
                                        LEFT JOIN inv_materials mi ON mi.id_material = pm.id_material
                                        LEFT JOIN admin_units u ON u.id_unit = pm.id_unit
                                        LEFT JOIN admin_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE pm.id_product = :id_product AND pm.id_company = :id_company -- AND pm.id_material IN (SELECT id_material FROM materials INNER JOIN admin_units ON admin_units.id_unit = materials.unit WHERE id_material = pm.id_material)
                                      ORDER BY `m`.`material` ASC");
        $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
        $productsmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsmaterials));
        return $productsmaterials;
    }

    // Insertar productos materia prima
    public function insertProductsMaterialsByCompany($dataProductMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO products_materials (id_material, id_unit, id_company, id_product, quantity)
                                          VALUES (:id_material, :id_unit, :id_company, :id_product, :quantity)");
            $stmt->execute([
                'id_material' => $dataProductMaterial['material'],
                'id_unit' => $dataProductMaterial['unit'],
                'id_company' => $id_company,
                'id_product' => $dataProductMaterial['idProduct'],
                'quantity' => trim($dataProductMaterial['quantity']),
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // Actualizar productos materia prima general
    public function updateProductsMaterials($dataProductMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_materials SET id_material = :id_material, id_unit = :id_unit,
                                                 id_product = :id_product, quantity = :quantity
                                          WHERE id_product_material = :id_product_material");
            $stmt->execute([
                'id_product_material' => $dataProductMaterial['idProductMaterial'],
                'id_material' => $dataProductMaterial['material'],
                'id_unit' => $dataProductMaterial['unit'],
                'id_product' => $dataProductMaterial['idProduct'],
                'quantity' => trim($dataProductMaterial['quantity']),
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // Borrar productos materia prima general
    public function deleteProductMaterial($id_product_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_materials WHERE id_product_material = :id_product_material");
        $stmt->execute(['id_product_material' => $id_product_material]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM products_materials WHERE id_product_material = :id_product_material");
            $stmt->execute(['id_product_material' => $id_product_material]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
