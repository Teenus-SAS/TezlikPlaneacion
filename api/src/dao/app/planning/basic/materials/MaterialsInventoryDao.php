<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MaterialsInventoryDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findMaterialInventory($id_material)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM materials_inventory WHERE id_material = :id_material");
        $stmt->execute(['id_material' => $id_material]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $material = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("material", array('material' => $material));
        return $material;
    }

    public function insertMaterialInventory($dataMaterial, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("INSERT INTO materials_inventory (id_material, id_company, quantity)
                                          VALUES (:id_material, :id_company, :quantity)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_material' => $dataMaterial['idMaterial'],
                'quantity' => $dataMaterial['quantity']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateMaterialInventory($dataMaterial)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE materials_inventory SET quantity = :quantity WHERE id_material = :id_material");
            $stmt->execute([
                'id_material' => $dataMaterial['idMaterial'],
                'quantity' => $dataMaterial['quantity']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deleteMaterialInventory($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM materials_inventory WHERE id_material = :id_material");
            $stmt->execute(['id_material' => $id_material]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM materials_inventory WHERE id_material = :id_material");
                $stmt->execute(['id_material' => $id_material]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
