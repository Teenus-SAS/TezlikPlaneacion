<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class StoreDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllStore($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT pg.id_programming, o.id_order, o.num_order, m.id_material, m.reference, m.material, m.quantity
                                      FROM programming pg
                                        INNER JOIN plan_orders o ON o.id_order = pg.id_order
                                        INNER JOIN products_materials pm ON pm.id_product = pg.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                      WHERE pg.id_company = :id_company AND pg.status = 1 AND m.status = 0
                                      ORDER BY `o`.`num_order` ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $store = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $store;
    }

    public function saveDelivery($dataStore)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE materials SET status = :status WHERE id_material = :id_material");
            $stmt->execute([
                'status' => 1,
                'id_material' => $dataStore['idMaterial']
            ]);
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }
}
