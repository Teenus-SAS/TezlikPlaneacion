<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralExplosionMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findEXMaterial($id_material)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM plan_explosions_materials WHERE id_material = :id_material");
        $stmt->execute(['id_material' => $id_material]);
        $material = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->notice("pedidos", array('pedidos' => $material));
        return $material;
    }

    public function findAllMaterialsConsolidated($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            pi.id_product,
                                            o.id_order,
                                            o.num_order,
                                            pm.id_product_material,
                                            SUM(IFNULL(pi.quantity, 0)) AS quantity_product,
                                            mi.id_material,
                                            mi.quantity AS quantity_material,
                                            mi.transit,
                                            (o.original_quantity * pm.quantity_converted) AS need,
                                            mi.minimum_stock
                                        FROM products p
                                            INNER JOIN inv_products pi ON pi.id_product = p.id_product
                                            INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                            INNER JOIN inv_materials mi ON mi.id_material = pm.id_material
                                            INNER JOIN orders o ON o.id_product = p.id_product
                                           -- LEFT JOIN requisitions r ON r.id_material = pm.id_material
                                           -- LEFT JOIN programming pg ON pg.id_order = o.id_order
                                        WHERE p.id_company = :id_company AND o.status IN(1, 4, 5, 6, 12)
                                        GROUP BY pm.id_product_material, o.id_order");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

        $this->logger->notice("pedidos", array('pedidos' => $materials));
        return $materials;
    }

    public function findAllChildrenMaterialsConsolidaded($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            o.id_order,
                                            o.num_order,
                                            pi.id_product, 
                                            pm.id_product_material,
                                            SUM(IFNULL(pi.quantity, 0)) AS quantity_product,
                                            mi.id_material,
                                            mi.quantity AS quantity_material,
                                            mi.transit,
                                            (expp.need * pm.quantity_converted) AS need,
                                            mi.minimum_stock
                                      FROM products p
                                        INNER JOIN inv_products pi ON pi.id_product = p.id_product
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN inv_materials mi ON mi.id_material = pm.id_material
                                        INNER JOIN plan_explosions_products expp ON expp.id_product = p.id_product
                                       -- LEFT JOIN requisitions r ON r.id_material = pm.id_material
                                        INNER JOIN orders o ON o.id_product = p.id_product
                                      WHERE p.id_company = :id_company
                                        GROUP BY pm.id_product_material");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

        $this->logger->notice("pedidos", array('pedidos' => $materials));
        return $materials;
    }

    public function findAllMaterialsConsolidatedByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pi.id_product, o.id_order, o.num_order, pm.id_product_material, SUM(pi.quantity) AS quantity_product, mi.id_material, mi.quantity AS quantity_material,
                                         mi.transit, (o.original_quantity * pm.quantity_converted) AS need, mi.minimum_stock
                                      FROM products p 
                                        INNER JOIN inv_products pi ON pi.id_product = p.id_product
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN inv_materials mi ON mi.id_material = pm.id_material 
                                        INNER JOIN orders o ON o.id_product = p.id_product
                                        LEFT JOIN requisitions_materials r ON r.id_material = pm.id_material
                                        LEFT JOIN programming pg ON pg.id_order = o.id_order
                                      WHERE p.id_product = :id_product AND o.status IN (1,4,5,6)
                                      GROUP BY pm.id_product_material, o.id_order");
        $stmt->execute(['id_product' => $id_product]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

        $this->logger->notice("pedidos", array('pedidos' => $materials));
        return $materials;
    }

    public function findAllMaterialsConsolidatedByMaterial($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pi.id_product, o.id_order, o.num_order, pm.id_product_material, SUM(pi.quantity) AS quantity_product, mi.id_material, mi.quantity AS quantity_material, 
                                            mi.transit, (o.original_quantity * pm.quantity_converted) AS need, mi.minimum_stock 
                                      FROM inv_materials mi 
                                        INNER JOIN products_materials pm ON pm.id_material = mi.id_material
                                        INNER JOIN inv_products pi ON pi.id_product = pm.id_product  
                                        INNER JOIN orders o ON o.id_product = pm.id_product
                                        LEFT JOIN requisitions_materials r ON r.id_material = mi.id_material
                                        LEFT JOIN programming pg ON pg.id_order = o.id_order
                                      WHERE mi.id_material = :id_material AND o.status IN (1,4,5,6)
                                      GROUP BY pm.id_product_material, o.id_order");
        $stmt->execute(['id_material' => $id_material]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

        $this->logger->notice("pedidos", array('pedidos' => $materials));
        return $materials;
    }

    public function setDataEXMaterials($materials)
    {
        try {
            $data = array();

            foreach ($materials as $arr) {
                $repeat = false;
                for ($i = 0; $i < sizeof($data); $i++) {
                    if ($data[$i]['id_material'] == $arr['id_material']) {
                        $data[$i]['id_order'] = $data[$i]['id_order'] . ',' . $arr['id_order'];
                        $data[$i]['num_order'] = $data[$i]['num_order'] . ',' . $arr['num_order'];
                        $data[$i]['transit'] = $arr['transit'];
                        $data[$i]['need'] += $arr['need'];
                        $data[$i]['minimum_stock'] = $arr['minimum_stock'];
                        $data[$i]['available'] += $arr['quantity_material'] + $arr['transit'] - $data[$i]['minimum_stock'] - $data[$i]['need'];
                        $repeat = true;
                        break;
                    }
                }

                if ($repeat == false) {
                    $data[] = array(
                        'id_order' => $arr['id_order'],
                        'num_order' => $arr['num_order'],
                        'id_product' => $arr['id_product'],
                        'quantity_product' => $arr['quantity_product'],
                        'id_material' => $arr['id_material'],
                        'quantity_material' => $arr['quantity_material'],
                        'transit' => $arr['transit'],
                        'need' => $arr['need'],
                        'minimum_stock' => $arr['minimum_stock'],
                        'available' => $arr['quantity_material'] + $arr['transit'] - $arr['minimum_stock'] - $arr['need'],
                    );
                }
            }

            return $data;
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
