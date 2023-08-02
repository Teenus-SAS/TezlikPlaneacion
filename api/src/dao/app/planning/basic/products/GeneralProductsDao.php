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
        $findProduct = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProduct;
    }

    public function findProductByCategoryInProcess($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products WHERE reference = :reference
                                  AND product = :product AND category LIKE '%en proceso' AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataProduct['referenceProduct']),
            'product' => ucfirst(strtolower(trim($dataProduct['product']))),
            'id_company' => $id_company
        ]);
        $findProduct = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProduct;
    }

    public function updateAccumulatedQuantity($id_product, $accumulated_quantity, $op)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            if ($op == 1)
                $stmt = $connection->prepare("UPDATE products SET accumulated_quantity = :accumulated_quantity WHERE id_product = :id_product");
            else
                $stmt = $connection->prepare("UPDATE products SET accumulated_quantity = :accumulated_quantity, quantity = :accumulated_quantity WHERE id_product = :id_product");

            $stmt->execute([
                'accumulated_quantity' => $accumulated_quantity,
                'id_product' => $id_product
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
