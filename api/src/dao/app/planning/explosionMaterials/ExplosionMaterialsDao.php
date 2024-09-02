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

  public function findAllCompositeConsolidated($id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT cp.id_product, o.id_order, cp.id_child_product, SUM(pi.quantity) AS quantity_product, cpi.quantity AS quantity_material, u.abbreviation, (o.original_quantity * cp.quantity) AS need, cpi.minimum_stock, p.reference AS reference_material, p.product AS material
                                      FROM composite_products cp
                                        LEFT JOIN products_inventory pi ON pi.id_product = cp.id_product
                                        LEFT JOIN products_inventory cpi ON cpi.id_product = cp.id_child_product
                                        LEFT JOIN products p ON p.id_product = cp.id_child_product
                                        INNER JOIN convert_units u ON u.id_unit = cp.id_unit
                                        INNER JOIN plan_orders o ON o.id_product = cp.id_product
                                      WHERE cp.id_company = :id_company AND o.status IN (1,4,5,6)
                                      GROUP BY cp.id_composite_product, o.id_order");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

    $this->logger->notice("pedidos", array('pedidos' => $materials));
    return $materials;
  }

  public function findAllCompositeConsolidatedByProduct($id_product)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT cp.id_product, o.id_order, cp.id_composite_product, SUM(pi.quantity) AS quantity_product, cpi.quantity AS quantity_material, u.abbreviation, (o.original_quantity * cpi.quantity) AS need, pi.minimum_stock
                                      FROM composite_products cp
                                        LEFT JOIN products_inventory pi ON pi.id_product = cp.id_product
                                        LEFT JOIN products_inventory cpi ON cpi.id_product = cp.id_child_product 
                                        INNER JOIN convert_units u ON u.id_unit = cp.id_unit
                                        INNER JOIN plan_orders o ON o.id_product = cp.id_product
                                      WHERE cp.id_product = :id_product AND o.status IN (1,4,5,6)
                                      GROUP BY cp.id_composite_product, o.id_order");
    $stmt->execute(['id_product' => $id_product]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

    $this->logger->notice("pedidos", array('pedidos' => $materials));
    return $materials;
  }

  public function findAllMaterialsConsolidated($id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT p.id_product, o.id_order, pm.id_product_material, p.reference AS reference_product, p.product, SUM(pi.quantity) AS quantity_product, m.id_material, m.reference AS reference_material, m.material, mi.quantity AS quantity_material, u.abbreviation, 
                                         -- IFNULL(SUM(IF(IFNULL(r.admission_date, 0) = 0 AND r.application_date != '0000-00-00' AND r.delivery_date != '0000-00-00', r.quantity_required, 0)), 0) AS transit, 
                                         mi.transit, (o.original_quantity * pm.quantity_converted) AS need, mi.minimum_stock
                                      FROM products p
                                        INNER JOIN products_inventory pi ON pi.id_product = p.id_product
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN materials_inventory mi ON mi.id_material = pm.id_material
                                        INNER JOIN convert_units u ON u.id_unit = m.unit
                                        INNER JOIN plan_orders o ON o.id_product = p.id_product
                                        LEFT JOIN requisitions r ON r.id_material = pm.id_material
                                        LEFT JOIN programming pg ON pg.id_order = o.id_order
                                      WHERE p.id_company = :id_company AND o.status IN (1,4,5,6)
                                      GROUP BY pm.id_product_material, o.id_order");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

    $this->logger->notice("pedidos", array('pedidos' => $materials));
    return $materials;
  }

  public function findAllMaterialsConsolidatedByProduct($id_product)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT p.id_product, o.id_order, pm.id_product_material, p.reference AS reference_product, p.product, SUM(pi.quantity) AS quantity_product, m.id_material, m.reference AS reference_material, m.material, mi.quantity AS quantity_material, u.abbreviation, 
                                         -- IFNULL(SUM(IF(IFNULL(r.admission_date, 0) = 0 AND r.application_date != '0000-00-00' AND r.delivery_date != '0000-00-00', r.quantity_required, 0)), 0) AS transit,
                                         mi.transit, (o.original_quantity * pm.quantity_converted) AS need, mi.minimum_stock
                                      FROM products p
                                        INNER JOIN products_inventory pi ON pi.id_product = p.id_product
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN materials_inventory mi ON mi.id_material = pm.id_material
                                        INNER JOIN convert_units u ON u.id_unit = m.unit
                                        INNER JOIN plan_orders o ON o.id_product = p.id_product
                                        LEFT JOIN requisitions r ON r.id_material = pm.id_material
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

    $stmt = $connection->prepare("SELECT p.id_product, o.id_order, pm.id_product_material, p.reference AS reference_product, p.product, SUM(pi.quantity) AS quantity_product, m.id_material, m.reference AS reference_material, m.material, mi.quantity AS quantity_material, u.abbreviation, 
                                         -- IFNULL(SUM(IF(IFNULL(r.admission_date, 0) = 0 AND r.application_date != '0000-00-00' AND r.delivery_date != '0000-00-00', r.quantity_required, 0)), 0) AS transit,
                                         mi.transit, (o.original_quantity * pm.quantity_converted) AS need, mi.minimum_stock 
                                      FROM materials m
                                        INNER JOIN materials_inventory mi ON mi.id_material = m.id_material
                                        INNER JOIN products_materials pm ON pm.id_material = m.id_material
                                        INNER JOIN products_inventory pi ON pi.id_product = pm.id_product
                                        INNER JOIN products p ON p.id_product = pm.id_product
                                        INNER JOIN convert_units u ON u.id_unit = m.unit
                                        INNER JOIN plan_orders o ON o.id_product = pm.id_product
                                        LEFT JOIN requisitions r ON r.id_material = m.id_material
                                        LEFT JOIN programming pg ON pg.id_order = o.id_order
                                      WHERE m.id_material = :id_material AND o.status IN (1,4,5,6)
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
            $data[$i]['transit'] = $arr['transit'];
            $data[$i]['need'] += $arr['need'];
            $data[$i]['minimum_stock'] = $arr['minimum_stock'];
            $data[$i]['available'] = $arr['quantity_material'] + $arr['transit'] - $data[$i]['minimum_stock'] - $data[$i]['need'];
            $repeat = true;
            break;
          }
        }

        if ($repeat == false) {
          $data[] = array(
            'id_product' => $arr['id_product'],
            'reference_product' => $arr['reference_product'],
            'product' => $arr['product'],
            'quantity_product' => $arr['quantity_product'],
            'id_material' => $arr['id_material'],
            'reference_material' => $arr['reference_material'],
            'material' => $arr['material'],
            'quantity_material' => $arr['quantity_material'],
            'transit' => $arr['transit'],
            'need' => $arr['need'],
            'abbreviation' => $arr['abbreviation'],
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

  public function setDataEXComposite($products)
  {
    try {
      $data = array();

      foreach ($products as $arr) {
        $repeat = false;
        for ($i = 0; $i < sizeof($data); $i++) {
          if ($data[$i]['id_child_product'] == $arr['id_child_product']) {
            $data[$i]['need'] += $arr['need'];
            $data[$i]['minimum_stock'] = $arr['minimum_stock'];
            $data[$i]['available'] = $arr['quantity_material'] - $data[$i]['minimum_stock'] - $data[$i]['need'];
            $repeat = true;
            break;
          }
        }

        if ($repeat == false) {
          $data[] = array(
            'id_product' => $arr['id_product'],
            'id_child_product' => $arr['id_child_product'],
            'quantity_product' => $arr['quantity_product'],
            'reference_material' => $arr['reference_material'],
            'material' => $arr['material'],
            'quantity_material' => $arr['quantity_material'],
            'need' => $arr['need'],
            'abbreviation' => $arr['abbreviation'],
            'minimum_stock' => $arr['minimum_stock'],
            'transit' => 0,
            'available' => $arr['quantity_material'] - $arr['minimum_stock'] - $arr['need'],
          );
        }
      }

      return $data;
    } catch (\Exception $e) {
      return ['info' => true, 'message' => $e->getMessage()];
    }
  }
}
