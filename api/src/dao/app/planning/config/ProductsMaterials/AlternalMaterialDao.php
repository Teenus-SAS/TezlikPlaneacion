<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class AlternalMaterialDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // public function findAllPlanCiclesMachine($id_company)
    // {
    //     $connection = Connection::getInstance()->getConnection();

    //     $stmt = $connection->prepare("SELECT pcm.id_product_material, pcm.id_product, p.reference, p.product, pcm.id_process, IFNULL(pc.process, '') AS process, pcm.id_machine, 
    //                                          m.machine, pcm.cicles_hour, pcm.units_turn, pcm.units_month, pcm.route
    //                                   FROM machine_cicles pcm
    //                                    INNER JOIN machines m ON m.id_machine = pcm.id_machine
    //                                    INNER JOIN products p ON p.id_product = pcm.id_product
    //                                    LEFT JOIN process pc ON pc.id_process = pcm.id_process
    //                                   WHERE pcm.id_company = :id_company ORDER BY pcm.route ASC");
    //     $stmt->execute(['id_company' => $id_company]);
    //     $planCiclesMachines = $stmt->fetchAll($connection::FETCH_ASSOC);
    //     return $planCiclesMachines;
    // }

    public function findAlternalMaterial($id_product_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM alternal_material
                                      WHERE id_product_material = :id_product_material");
        $stmt->execute(['id_product_material' => $id_product_material]);
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }

    public function addAlternalMaterial($dataAMaterials, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO alternal_material (id_company, id_product_material, id_material, id_unit, quantity) 
                                          VALUES (:id_company, :id_product_material, :id_material, :id_unit, :quantity)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product_material' => $dataAMaterials['idProductMaterial'],
                'id_material' => $dataAMaterials['idMaterial'],
                'id_unit' => $dataAMaterials['idUnit'],
                'quantity' => $dataAMaterials['quantity']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateAlternalMaterial($dataAMaterials)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE alternal_material SET id_material = :id_material, id_unit = :id_unit, quantity = :quantity
                                          WHERE id_product_material = :id_product_material");
            $stmt->execute([
                'id_product_material' => $dataAMaterials['idProductMaterial'],
                'id_material' => $dataAMaterials['idMaterial'],
                'id_unit' => $dataAMaterials['idUnit'],
                'quantity' => $dataAMaterials['quantity']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function saveQuantityConverted($id_product_material, $quantity)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE alternal_material SET quantity_converted = :quantity_converted
                                          WHERE id_product_material = :id_product_material");
            $stmt->execute([
                'id_product_material' => $id_product_material,
                'quantity_converted' => $quantity
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
