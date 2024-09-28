<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralExplosionProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findEXProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM plan_explosions_products WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);
        $products = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->notice("pedidos", array('pedidos' => $products));
        return $products;
    }

    public function findAllCompositeConsolidated($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            cp.id_product, 
                                            o.id_order,
                                            o.num_order,
                                            cp.id_child_product, 
                                            SUM(IFNULL(pi.quantity, 0)) AS quantity_product, 
                                            IFNULL(cpi.quantity, 0) AS quantity_material, 
                                            u.abbreviation, 
                                            (o.original_quantity * cp.quantity) AS need, 
                                            IFNULL(cpi.minimum_stock, 0) AS minimum_stock
                                            -- p.reference AS reference_material,  p.product AS material
                                        FROM products_composite cp
                                            LEFT JOIN inv_products pi ON pi.id_product = cp.id_product
                                            LEFT JOIN inv_products cpi ON cpi.id_product = cp.id_child_product
                                            -- LEFT JOIN products p ON p.id_product = cp.id_child_product
                                            INNER JOIN admin_units u ON u.id_unit = cp.id_unit
                                            INNER JOIN orders o ON o.id_product = cp.id_product
                                        WHERE cp.id_company = :id_company AND o.status IN (1,4,5,6)
                                        GROUP BY cp.id_composite_product, o.id_order");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

        $this->logger->notice("pedidos", array('pedidos' => $materials));
        return $materials;
    }

    public function findAllChildrenCompositeConsolidaded($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                            -- Columnas
                                                o.id_order,
                                                o.num_order,
                                                cp.id_product, 
                                                cp.id_child_product, 
                                                SUM(IFNULL(pi.quantity, 0)) AS quantity_product, 
                                                IFNULL(cpi.quantity, 0) AS quantity_material, 
                                                u.abbreviation, 
                                                (expo.need * cp.quantity) AS need, 
                                                IFNULL(cpi.minimum_stock, 0) AS minimum_stock
                                        FROM products_composite cp
                                            LEFT JOIN inv_products pi ON pi.id_product = cp.id_product
                                            LEFT JOIN inv_products cpi ON cpi.id_product = cp.id_child_product
                                            INNER JOIN admin_units u ON u.id_unit = cp.id_unit
                                            INNER JOIN plan_explosions_products expo ON expo.id_product = cp.id_product
                                            INNER JOIN orders o ON o.id_product = cp.id_product
                                        WHERE cp.id_company = :id_company
                                        GROUP BY cp.id_composite_product");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

        $this->logger->notice("pedidos", array('pedidos' => $materials));
        return $materials;
    }

    public function setDataEXComposite($products)
    {
        try {
            $data = array();

            foreach ($products as $arr) {
                $repeat = false;
                for ($i = 0; $i < sizeof($data); $i++) {
                    if ($data[$i]['id_child_product'] == $arr['id_child_product']) {
                        $data[$i]['id_order'] = $data[$i]['id_order'] . ',' . $arr['id_order'];
                        $data[$i]['num_order'] = $data[$i]['num_order'] . ',' . $arr['num_order'];
                        $data[$i]['need'] += $arr['need'];
                        $data[$i]['minimum_stock'] = $arr['minimum_stock'];
                        $data[$i]['available'] = $arr['quantity_material'] - $data[$i]['minimum_stock'] - $data[$i]['need'];
                        $repeat = true;
                        break;
                    }
                }

                if ($repeat == false) {
                    $data[] = array(
                        'id_order' => $arr['id_order'],
                        'num_order' => $arr['num_order'],
                        'id_product' => $arr['id_product'],
                        'id_child_product' => $arr['id_child_product'],
                        'quantity_product' => $arr['quantity_product'],
                        'quantity_material' => $arr['quantity_material'],
                        'need' => $arr['need'],
                        'minimum_stock' => $arr['minimum_stock'],
                        'transit' => 0,
                        'available' => $arr['quantity_material'] - $arr['minimum_stock'] - $arr['need']
                    );
                }
            }

            return $data;
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
