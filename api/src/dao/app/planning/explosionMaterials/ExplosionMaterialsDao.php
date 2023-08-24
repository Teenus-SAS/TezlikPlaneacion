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

        $stmt = $connection->prepare("SELECT p.id_product, p.reference AS reference_product, p.product, SUM(p.quantity) AS quantity_product, m.id_material, m.reference AS reference_material, m.material, SUM(m.quantity) AS quantity_material, 
                                             IFNULL(IF(r.quantity = NULL OR r.admission_date != NULL, 0, r.quantity), 0) AS transit, ((SELECT SUM(cpm.quantity) FROM products_materials cpm INNER JOIN plan_orders co ON cpm.id_product = p.id_product 
                                             WHERE cpm.id_material = m.id_material AND co.status = 'Alistamiento') * (SELECT SUM(co.original_quantity) FROM plan_orders co INNER JOIN products_materials cpm ON cpm.id_product = o.id_product WHERE cpm.id_material = m.id_material AND co.status = 'Alistamiento')) AS need, u.unit
                                      FROM products p
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN convert_units u ON u.id_unit = m.unit
                                        INNER JOIN plan_orders o ON o.id_product = p.id_product
                                        LEFT JOIN requisitons r ON r.id_material = pm.id_material
                                      WHERE p.id_company = :id_company AND o.status = 'Alistamiento'
                                      GROUP BY m.id_material;");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("pedidos", array('pedidos' => $materials));
        return $materials;
    }
}
