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

    $stmt = $connection->prepare("SELECT p.id_product, o.id_order, pm.id_product_material, p.reference AS reference_product, p.product, SUM(p.quantity) AS quantity_product, m.id_material, m.reference AS reference_material, m.material, m.quantity AS quantity_material, u.unit, 
                                         IFNULL(SUM(IF(IFNULL(r.admission_date, 0) = 0 AND r.application_date != '0000-00-00' AND r.delivery_date != '0000-00-00', r.quantity, 0)), 0) AS transit, (o.original_quantity * pm.quantity) AS need, m.minimum_stock
                                         -- ((o.original_quantity * pm.quantity) - IF(m.status = 1, (IFNULL(SUM(pg.quantity * pm.quantity), 0)), 0)) AS need
                                      FROM products p
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN convert_units u ON u.id_unit = m.unit
                                        INNER JOIN plan_orders o ON o.id_product = p.id_product
                                        LEFT JOIN requisitions r ON r.id_material = pm.id_material
                                        LEFT JOIN programming pg ON pg.id_order = o.id_order
                                      WHERE p.id_company = :id_company AND (o.status = 'Programar' OR o.status = 'Programado' OR o.status = 'Sin Ficha Tecnica' OR o.status = 'Sin Materia Prima') -- OR o.status = 'En Produccion')
                                      GROUP BY pm.id_product_material, o.id_order");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

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
          'unit' => $arr['unit'],
          'minimum_stock' => $arr['minimum_stock'],
          'available' => $arr['quantity_material'] + $arr['transit'] - $arr['minimum_stock'] - $arr['need'],
        );
      }
    }

    $this->logger->notice("pedidos", array('pedidos' => $materials));
    return $data;
  }

  // public function findAllMaterialsConsolidatedbyProduct($id_product)
  // {
  //   $connection = Connection::getInstance()->getConnection();

  //   $stmt = $connection->prepare("SELECT m.id_material, (m.quantity + IFNULL(IF(r.admission_date != 'NULL', 0, r.quantity), 0) - ((SELECT cpm.quantity FROM products_materials cpm INNER JOIN plan_orders co ON co.id_product = cpm.id_product 
  //                                            WHERE cpm.id_product_material = pm.id_product_material AND co.status = 'Alistamiento') * (SELECT co.original_quantity FROM plan_orders co INNER JOIN products_materials cpm ON cpm.id_product = co.id_product WHERE cpm.id_product_material = pm.id_product_material AND co.status = 'Alistamiento'))) AS available
  //                                     FROM products p
  //                                       INNER JOIN products_materials pm ON pm.id_product = p.id_product
  //                                       INNER JOIN materials m ON m.id_material = pm.id_material
  //                                       INNER JOIN convert_units u ON u.id_unit = m.unit
  //                                       INNER JOIN plan_orders o ON o.id_product = p.id_product
  //                                       LEFT JOIN requisitions r ON r.id_material = pm.id_material
  //                                     WHERE p.id_product = :id_product AND o.status = 'Alistamiento'
  //                                     GROUP BY pm.id_product_material");
  //   $stmt->execute(['id_product' => $id_product]);

  //   $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

  //   $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

  //   $data = array();

  //   foreach ($materials as $arr) {
  //     $repeat = false;
  //     for ($i = 0; $i < sizeof($data); $i++) {
  //       if ($data[$i]['id_material'] == $arr['id_material']) {
  //         $data[$i]['available'] += $arr['available'];
  //         $repeat = true;
  //         break;
  //       }
  //     }

  //     if ($repeat == false) {
  //       $data[] = array(
  //         'id_material' => $arr['id_material'],
  //         'available' => $arr['available']
  //       );
  //     }
  //   }

  //   $this->logger->notice("pedidos", array('pedidos' => $materials));
  //   return $data;
  // }
}
