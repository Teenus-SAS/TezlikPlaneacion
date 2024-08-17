<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsUnitSalesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, p.product AS descript, p.img, pi.quantity, pi.reserved, pi.classification, 'UNIDAD' AS unit, IFNULL(u.jan , 0) AS jan, pi.minimum_stock, 
                                         IFNULL(u.feb, 0) AS feb, IFNULL(u.mar, 0) AS mar, IFNULL(u.apr, 0) AS apr, IFNULL(u.may, 0) AS may, IFNULL(u.jun, 0) AS jun, IFNULL(u.jul, 0) AS jul, IFNULL(u.aug, 0) AS aug, IFNULL(u.sept, 0) AS sept, IFNULL(u.oct, 0) AS oct, IFNULL(u.nov, 0) AS nov, IFNULL(u.dece, 0) AS dece
                                  FROM products p
                                  LEFT JOIN plan_unit_sales u ON u.id_product = p.id_product 
                                  INNER JOIN products_inventory pi ON pi.id_product = p.id_product 
                                  WHERE p.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    public function findAllProductsStockByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pi.quantity, pi.minimum_stock,
                                             IFNULL(sp.max_term, 0) AS max_term, IFNULL(sp.usual_term, 0) AS usual_term                              
                                      FROM products p
                                          INNER JOIN products_inventory pi ON pi.id_product = p.id_product
                                          LEFT JOIN stock_products sp ON sp.id_product = p.id_product
                                      WHERE p.id_company = :id_company ORDER BY p.product ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    /* Consultar si existe producto en BD por compañia */
    public function findProduct($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products
                                  WHERE reference = :reference
                                  AND product = :product 
                                  AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataProduct['referenceProduct']),
            'product' => strtoupper(trim($dataProduct['product'])),
            'id_company' => $id_company
        ]);
        $findProduct = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProduct;
    }

    public function findProductById($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $product = $stmt->fetch($connection::FETCH_ASSOC);
        return $product;
    }

    // Buscar producto por el idMaterial
    public function findProductByMaterial($idMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pm.id_product, m.id_magnitude
                                      FROM products p
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN convert_units c ON c.id_unit = pm.id_unit
                                        INNER JOIN convert_magnitudes m ON m.id_magnitude = c.id_magnitude
                                      WHERE pm.id_material =:id_material AND p.id_company = :id_company");
        $stmt->execute(['id_material' => $idMaterial, 'id_company' => $id_company]);
        $dataProduct = $stmt->fetchAll($connection::FETCH_ASSOC);

        return $dataProduct;
    }

    /* Consultar si existe referencia o nombre de producto en BD por compañia */
    public function findProductByReferenceOrName($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products
                                      WHERE id_company = :id_company AND (reference = :reference OR product = :product)");
        $stmt->execute([
            'reference' => trim($dataProduct['referenceProduct']),
            'product' => strtoupper(trim($dataProduct['product'])),
            'id_company' => $id_company
        ]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findAllProductsStatus($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, IFNULL((SELECT id_product_material FROM products_materials WHERE id_product = p.id_product LIMIT 1), 0) AS status
                                      FROM products p 
                                      WHERE p.id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }

    public function findProductStatus($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT IFNULL((SELECT id_product_material FROM products_materials WHERE id_product = p.id_product LIMIT 1), 0) AS status
                                      FROM products p 
                                      WHERE p.id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $product = $stmt->fetch($connection::FETCH_ASSOC);
        return $product;
    }

    public function findProductInventory($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_inventory
                                      WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $id_product,
            'id_company' => $id_company,
        ]);
        $product = $stmt->fetch($connection::FETCH_ASSOC);
        return $product;
    }

    public function findProductReserved($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT SUM(original_quantity) AS reserved
                                      FROM plan_orders 
                                      WHERE id_product = :id_product AND status = 2");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $findProduct = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProduct;
    }

    public function updateAccumulatedQuantity($id_product, $accumulated_quantity, $op)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if ($op == 1) {
                $stmt = $connection->prepare("UPDATE products_inventory SET accumulated_quantity = :accumulated_quantity WHERE id_product = :id_product");
                $stmt->execute([
                    'accumulated_quantity' => $accumulated_quantity,
                    'id_product' => $id_product
                ]);
            } else {
                $stmt = $connection->prepare("UPDATE products_inventory SET accumulated_quantity = :accumulated_quantity, quantity = :quantity
                                              WHERE id_product = :id_product");
                $stmt->execute([
                    'quantity' => $accumulated_quantity,
                    'accumulated_quantity' => $accumulated_quantity,
                    'id_product' => $id_product
                ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateAccumulatedQuantityGeneral($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_inventory
                                          SET accumulated_quantity = quantity
                                          WHERE id_company = :id_company");

            $stmt->execute([
                'id_company' => $id_company
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
    public function clearAccumulatedQuantityGeneral($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_inventory
                                          SET accumulated_quantity = 0
                                          WHERE id_company = :id_company");

            $stmt->execute([
                'id_company' => $id_company
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateStatusByProduct($id_product, $status_ds)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_inventory SET status_ds = :status_ds WHERE id_product = :id_product");

            $stmt->execute([
                'status_ds' => $status_ds,
                'id_product' => $id_product
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateReservedByProduct($id_product, $reserved)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_inventory SET reserved = :reserved WHERE id_product = :id_product");

            $stmt->execute([
                'reserved' => $reserved,
                'id_product' => $id_product
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateStockByProduct($id_product, $stock)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_inventory SET minimum_stock = :minimum_stock WHERE id_product = :id_product");

            $stmt->execute([
                'minimum_stock' => $stock,
                'id_product' => $id_product
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
    public function updateGeneralClassification($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE products_inventory SET classification = 'C' WHERE id_company = :id_company");

            $stmt->execute([
                'id_company' => $id_company
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
