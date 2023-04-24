<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsProcessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsprocess($idProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, pp.id_process, pp.id_machine, pp.id_product_process,
                                                     pp.enlistment_time, pp.operation_time, mc.machine, pc.process
                                  FROM products p 
                                  INNER JOIN products_process pp ON pp.id_product = p.id_product
                                  LEFT JOIN machines mc ON mc.id_machine = pp.id_machine 
                                  INNER JOIN process pc ON pc.id_process = pp.id_process
                                  WHERE p.id_product = :id_product AND p.id_company = :id_company ORDER BY pp.id_machine ASC");
        $stmt->execute(['id_product' => $idProduct, 'id_company' => $id_company]);
        $productsprocess = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsprocess));
        return $productsprocess;
    }

    // Insertar productos procesos general
    public function insertProductsProcessByCompany($dataProductProcess, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();


        $dataProductProcess['idMachine'] == 0 ? $idMachine = null : $idMachine = $dataProductProcess['idMachine'];

        try {
            $stmt = $connection->prepare("SELECT id_product_process FROM products_process WHERE id_product = :id_product AND id_company = :id_company
                                          AND id_process = :id_process AND id_machine = :id_machine");
            $stmt->execute([
                'id_product' => $dataProductProcess['idProduct'],
                'id_company' => $id_company,
                'id_process' => $dataProductProcess['idProcess'],
                'id_machine' => $idMachine,
            ]);
            $row = $stmt->fetch($connection::FETCH_ASSOC);

            if ($row > 0) {
                return 1;
            } else {
                $stmt = $connection->prepare("INSERT INTO products_process (id_product, id_company, id_process, 
                id_machine, enlistment_time, operation_time) VALUES (:id_product, :id_company, :id_process, :id_machine, :enlistment_time, :operation_time)");
                $stmt->execute([
                    'id_product' => $dataProductProcess['idProduct'],
                    'id_company' => $id_company,
                    'id_process' => $dataProductProcess['idProcess'],
                    'id_machine' => $idMachine,
                    'enlistment_time' => trim($dataProductProcess['enlistmentTime']),
                    'operation_time' => trim($dataProductProcess['operationTime'])
                ]);
            }
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // Actualizar productos procesos general
    public function updateProductsProcess($dataProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        $dataProductProcess['idMachine'] == 0 ? $idMachine = null : $idMachine = $dataProductProcess['idMachine'];

        try {
            $stmt = $connection->prepare("UPDATE products_process SET id_product = :id_product, id_process = :id_process, id_machine = :id_machine, enlistment_time = :enlistment_time, operation_time = :operation_time
                                          WHERE id_product_process = :id_product_process");
            $stmt->execute([
                'id_product_process' => $dataProductProcess['idProductProcess'],
                'id_product' => $dataProductProcess['idProduct'],
                'id_process' => $dataProductProcess['idProcess'],
                'id_machine' => $idMachine,
                'enlistment_time' => trim($dataProductProcess['enlistmentTime']),
                'operation_time' => trim($dataProductProcess['operationTime'])
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteProductProcess($dataProductProcess)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_process WHERE id_product_process = :id_product_process");
        $stmt->execute(['id_product_process' => $dataProductProcess['idProductProcess']]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM products_process WHERE id_product_process = :id_product_process");
            $stmt->execute(['id_product_process' => $dataProductProcess['idProductProcess']]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
