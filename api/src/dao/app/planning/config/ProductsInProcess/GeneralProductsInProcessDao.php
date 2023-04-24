<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsInProcessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Todos los productos en proceso */
    public function findAllProductsInProcess()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product
                                       FROM products p
                                       INNER JOIN plan_categories c ON c.id_category = p.category
                                       WHERE c.category LIKE '%EN PROCESO'");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productsInProcess = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $productsInProcess;
    }

    public function findProductInProcess($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_products_categories 
                                       WHERE final_product = :final_product, id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'final_product' => $dataProduct['finalProduct'],
            'id_product' => $dataProduct['idProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $productInProcess = $stmt->fetch($connection::FETCH_ASSOC);
        return $productInProcess;
    }
}
