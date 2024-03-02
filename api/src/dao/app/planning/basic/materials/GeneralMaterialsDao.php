<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllMaterialsStockByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT m.id_material, m.reference, m.material, m.material AS descript, mg.id_magnitude, mg.magnitude, u.id_unit, u.unit, u.abbreviation, m.quantity, m.reserved, 
                                             IFNULL(s.max_term, 0) AS max_term, IFNULL(s.usual_term, 0) AS usual_term, m.minimum_stock,IFNULL(s.id_provider, 0) AS id_provider, IFNULL(c.client, '') AS client                               
                                      FROM materials m
                                          INNER JOIN convert_units u ON u.id_unit = m.unit
                                          INNER JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                          LEFT JOIN stock s ON s.id_material = m.id_material
                                          LEFT JOIN plan_clients c ON c.id_client = s.id_provider
                                      WHERE m.id_company = :id_company ORDER BY m.material ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("materials", array('materials' => $materials));
        return $materials;
    }

    /* Consultar si existe materia prima en la BD */
    public function findReservedMaterial($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT SUM(pg.quantity * pm.quantity) AS reserved
                                      FROM programming pg 
                                        LEFT JOIN plan_orders o ON o.id_order = pg.id_order
                                        LEFT JOIN products_materials pm ON pm.id_product = o.id_product 
                                      WHERE pm.id_material = :id_material AND o.status = 4");
        $stmt->execute([
            'id_material' => $id_material,
        ]);
        $material = $stmt->fetch($connection::FETCH_ASSOC);
        return $material;
    }

    public function findMaterial($dataMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_material FROM materials 
                                        WHERE (reference = :reference OR material = :material) 
                                        AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataMaterial['refRawMaterial']),
            'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
            'id_company' => $id_company,
        ]);
        $findMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findMaterial;
    }

    public function findMaterialById($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM materials WHERE id_material = :id_material");
        $stmt->execute(['id_material' => $id_material]);
        $findMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findMaterial;
    }

    // Calcular inventario Materia Prima Recibida
    public function calcMaterialRecieved($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT (m.quantity + r.quantity) AS quantity
                                      FROM materials m
                                        INNER JOIN requisitions r ON r.id_material = m.id_material
                                      WHERE m.id_material = :id_material");
        $stmt->execute([
            'id_material' => $id_material,
        ]);
        $material = $stmt->fetch($connection::FETCH_ASSOC);
        return $material;
    }

    public function updateQuantityMaterial($id_material, $quantity)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE materials SET quantity = :quantity WHERE id_material = :id_material");
            $stmt->execute([
                'id_material' => $id_material,
                'quantity' => $quantity
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateStoreMaterial($dataMaterial)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE materials SET quantity = :quantity, delivery_store =  delivery_store + :delivery_store, delivery_pending = :delivery_pending
                                          WHERE id_material = :id_material");
            $stmt->execute([
                'id_material' => $dataMaterial['idMaterial'],
                'quantity' => $dataMaterial['stored'],
                'delivery_store' => $dataMaterial['delivered'],
                'delivery_pending' => $dataMaterial['pending']
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateReservedMaterial($id_material, $reserved)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE materials SET reserved = :reserved WHERE id_material = :id_material");
            $stmt->execute([
                'id_material' => $id_material,
                'reserved' => $reserved
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateStockMaterial($id_material, $stock)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE materials SET minimum_stock = :minimum_stock WHERE id_material = :id_material");
            $stmt->execute([
                'id_material' => $id_material,
                'minimum_stock' => $stock
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateDeliveryDateMaterial($id_material, $date)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE materials SET delivery_date = :delivery_date WHERE id_material = :id_material");
            $stmt->execute([
                'id_material' => $id_material,
                'delivery_date' => $date
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
