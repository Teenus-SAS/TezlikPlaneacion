<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class InventoryABCDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllInventoryABCByComapny($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM inv_abc WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $InventoryABC = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("InventoryABC", array('InventoryABC' => $InventoryABC));
        return $InventoryABC;
    }

    public function insertInventoryABC($dataInventory, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO inv_abc (id_company, a, b, c) VALUES (:id_company, :a, :b, :c)");
            $stmt->execute([
                'id_company' => $id_company,
                'a' => $dataInventory['a'],
                'b' => $dataInventory['b'],
                'c' => $dataInventory['c'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Categoría duplicada. Ingrese una nueva categoría';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateInventoryABC($dataInventory)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE inv_abc SET a = :a, b = :b, c = :c WHERE id_inventory = :id_inventory");
            $stmt->execute([
                'id_inventory' => $dataInventory['idInventory'],
                'a' => $dataInventory['a'],
                'b' => $dataInventory['b'],
                'c' => $dataInventory['c'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // public function deleteInventoryABC($id_inventory)
    // {
    //     $connection = Connection::getInstance()->getConnection();
    //     try {
    //         $stmt = $connection->prepare("SELECT * FROM inv_abc WHERE id_inventory = :id_inventory");
    //         $stmt->execute(['id_inventory' => $id_inventory]);
    //         $rows = $stmt->rowCount();

    //         if ($rows > 0) {
    //             $stmt = $connection->prepare("DELETE FROM inv_abc WHERE id_inventory = :id_inventory");
    //             $stmt->execute(['id_inventory' => $id_inventory]);
    //         }
    //         $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    //     } catch (\Exception $e) {
    //         $message = $e->getMessage();
    //         if ($e->getCode() == 23000)
    //             $message = 'Categoria asociada a un material o producto. Imposible Eliminar';
    //         $error = array('info' => true, 'message' => $message);
    //         return $error;
    //     }
    // }
}
