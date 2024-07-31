<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class TransitMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcQuantityTransitByMaterial($id_material)
    {
        try {
            $connection = Connection::getInstance()->getconnection();
            $stmt = $connection->prepare("SELECT COALESCE(SUM(
                                                CASE
                                                    WHEN r.admission_date IS NULL 
                                                        AND r.application_date != '0000-00-00' 
                                                        AND r.delivery_date != '0000-00-00' 
                                                    THEN r.quantity_required 
                                                    ELSE 0 
                                                END
                                            ), 0) AS transit
                                          FROM materials m
                                          LEFT JOIN requisitions r ON r.id_material = m.id_material
                                          WHERE m.id_material = :id_material");
            $stmt->execute([
                'id_material' => $id_material
            ]);
            $material = $stmt->fetch($connection::FETCH_ASSOC);

            return $material;
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateQuantityTransitByMaterial($id_material, $quantity)
    {
        try {
            $connection = Connection::getInstance()->getconnection();
            $stmt = $connection->prepare("UPDATE materials_inventory SET transit = :transit WHERE id_material = :id_material");
            $stmt->execute([
                'transit' => $quantity,
                'id_material' => $id_material,
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
