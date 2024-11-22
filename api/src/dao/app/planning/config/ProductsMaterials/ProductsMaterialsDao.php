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
        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            pm.id_product,
                                            pm.id_product_material,
                                            pm.id_material,
                                            m.reference,
                                            m.material,
                                            mi.status,
                                            IFNULL(mg.id_magnitude, 0) AS id_magnitude,
                                            m.id_material_type,
                                            'MATERIAL' AS TYPE,
                                            IFNULL(mg.magnitude, '') AS magnitude,
                                            IFNULL(u.id_unit, 0) AS id_unit,
                                            IFNULL(u.unit, '') AS unit,
                                            IFNULL(u.abbreviation, '') AS abbreviation,
                                            pm.quantity,
                                            (IFNULL(mi.quantity, 0) - IFNULL(mi.reserved, 0)) AS quantity_material,
                                            mi.reserved,
                                            (
                                                (
                                                    mi.quantity / pm.quantity_converted
                                                ) - IFNULL(
                                                    (
                                                    SELECT
                                                        SUM(quantity)
                                                    FROM
                                                        programming
                                                    WHERE
                                                        id_product = pm.id_product
                                                ),
                                                0
                                                )
                                            ) AS total_quantity,
                                            IFNULL(am.id_material, 0) AS id_alternal_material,
                                            IFNULL(am.id_unit, 0) AS id_alternal_unit,
                                            IFNULL(amm.material, '') AS alternal_material,
                                            IFNULL(am.quantity, 0) AS alternal_quantity,
                                            IFNULL(am.quantity_converted, 0) AS alternal_quantity_converted,
                                            IFNULL(au.unit, '') AS alternal_unit,
                                            IFNULL(au.abbreviation, '') AS alternal_abbreviation,
                                            IFNULL(amg.id_magnitude, 0) AS id_alternal_magnitude,
                                            IFNULL(amg.magnitude, '') AS alternal_magnitude
                                      FROM products_materials pm
                                          LEFT JOIN materials m ON m.id_material = pm.id_material
                                          LEFT JOIN inv_materials mi ON mi.id_material = pm.id_material
                                          LEFT JOIN admin_units u ON u.id_unit = pm.id_unit
                                          LEFT JOIN admin_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                          LEFT JOIN alternal_materials am ON am.id_product_material = pm.id_product_material
                                          LEFT JOIN materials amm ON amm.id_material = am.id_material
                                          LEFT JOIN admin_units au ON au.id_unit = am.id_unit
                                          LEFT JOIN admin_magnitudes amg ON amg.id_magnitude = au.id_magnitude
                                      WHERE pm.id_product = :id_product AND pm.id_company = :id_company
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
