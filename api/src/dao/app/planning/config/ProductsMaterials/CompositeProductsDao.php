<?php

namespace TezlikPlaneacion\Dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CompositeProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCompositeProductsByIdProduct($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT cp.id_composite_product, 0 AS id_product_material, cp.id_child_product, cp.id_product, p.reference, p.reference AS reference_material, p.product AS material, mg.id_magnitude, mg.magnitude, 
                                             u.id_unit, u.unit, u.abbreviation, cp.quantity, 'PRODUCTO' AS type, p.id_product_type, IFNULL(pi.quantity, 0) AS quantity_material
                                      FROM products p 
                                        INNER JOIN composite_products cp ON cp.id_child_product = p.id_product 
                                        LEFT JOIN products_inventory pi ON pi.id_product = cp.id_child_product
                                        INNER JOIN convert_units u ON u.id_unit = cp.id_unit
                                        INNER JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                      WHERE cp.id_company = :id_company AND cp.id_product = :id_product");
        $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
        $compositeProducts = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $compositeProducts));
        return $compositeProducts;
    }

    public function insertCompositeProductByCompany($dataProduct, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO composite_products (id_company, id_product, id_child_product, id_unit, quantity)
                                          VALUES (:id_company, :id_product, :id_child_product, :id_unit, :quantity)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataProduct['idProduct'],
                'id_child_product' => $dataProduct['compositeProduct'],
                'id_unit' => $dataProduct['unit'],
                'quantity' => $dataProduct['quantity']
            ]);
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }

    public function updateCompositeProduct($dataProduct)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE composite_products SET id_child_product = :id_child_product, id_unit = :id_unit, quantity = :quantity
                                          WHERE id_composite_product = :id_composite_product");
            $stmt->execute([
                'id_composite_product' => $dataProduct['idCompositeProduct'],
                'id_child_product' => $dataProduct['compositeProduct'],
                'id_unit' => $dataProduct['unit'],
                'quantity' => $dataProduct['quantity']
            ]);
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }

    public function deleteCompositeProduct($id_composite_product)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM composite_products WHERE id_composite_product = :id_composite_product");
            $stmt->execute(['id_composite_product' => $id_composite_product]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM composite_products WHERE id_composite_product = :id_composite_product");
                $stmt->execute(['id_composite_product' => $id_composite_product]);
            }
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }
}
