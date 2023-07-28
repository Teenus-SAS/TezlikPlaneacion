<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ExplosionMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllMaterialsConsolidated($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT p.id_product, p.reference AS reference_product, p.product, m.id_material, m.reference AS reference_material, m.material, 
                                             (SELECT SUM(quantity) FROM products_materials WHERE id_product = p.id_product AND id_material = m.id_material) AS quantity
                                      FROM products p
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN plan_orders o ON o.id_product = p.id_product
                                      WHERE p.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $materials));
        return $materials;
    }
}
