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

  public function findAllExplosionMaterialsByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $stmt = $connection->prepare("SELECT
                                    -- Columnas
                                      exm.id_explosion_material,
                                      IFNULL(
                                                (
                                                    SELECT GROUP_CONCAT(oo.num_order)
                                                    FROM orders oo
                                                    INNER JOIN plan_explosions_materials cexm ON cexm.order LIKE CONCAT('%', oo.id_order, '%')
                                                    WHERE cexm.id_explosion_material = exm.id_explosion_material
                                                )
                                      , 0) AS num_order,
                                      m.reference AS reference_material,
                                      m.material,
                                      au.abbreviation,
                                      mi.quantity AS quantity_material,
                                      mi.minimum_stock,
                                      mi.transit,
                                      exm.need,
                                      exm.available
                                  FROM plan_explosions_materials exm
                                    INNER JOIN materials m ON m.id_material = exm.id_material
                                    INNER JOIN inv_materials mi ON mi.id_material = exm.id_material
                                    INNER JOIN admin_units au ON au.id_unit = m.unit
                                  WHERE exm.id_company = :id_company");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $materials = $stmt->fetchAll($connection::FETCH_ASSOC);

    $this->logger->notice("pedidos", array('pedidos' => $materials));
    return $materials;
  }

  public function insertNewEXMByCompany($dataEXM, $id_company)
  {
    try {
      $connection = Connection::getInstance()->getConnection();

      $stmt = $connection->prepare("INSERT INTO plan_explosions_materials (id_company, `order`, id_material, need, available)
                                    VALUES (:id_company, :order, :id_material, :need, :available)");
      $stmt->execute([
        'id_company' => $id_company,
        'order' => $dataEXM['id_order'],
        'id_material' => $dataEXM['id_material'],
        'need' => $dataEXM['need'],
        'available' => $dataEXM['available'],
      ]);
    } catch (\Exception $e) {
      return ['info' => true, 'message' => $e->getMessage()];
    }
  }

  public function updateEXMaterials($dataEXM)
  {
    try {
      $connection = Connection::getInstance()->getConnection();

      $stmt = $connection->prepare("UPDATE plan_explosions_materials SET `order` = :order, id_material = :id_material, need = :need, available = :available
                                          WHERE id_explosion_material = :id_explosion_material");
      $stmt->execute([
        'id_explosion_material' => $dataEXM['id_explosion_material'],
        'order' => $dataEXM['id_order'],
        'id_material' => $dataEXM['id_material'],
        'need' => $dataEXM['need'],
        'available' => $dataEXM['available'],
      ]);
    } catch (\Exception $e) {
      return ['info' => true, 'message' => $e->getMessage()];
    }
  }

  public function deleteEXMaterial($id_explosion_material)
  {
    try {
      $connection = Connection::getInstance()->getConnection();

      $stmt = $connection->prepare("SELECT * FROM plan_explosions_materials WHERE id_explosion_material = :id_explosion_material");
      $stmt->execute(['id_explosion_material' => $id_explosion_material]);
      $rows = $stmt->rowCount();

      if ($rows > 0) {
        $stmt = $connection->prepare("DELETE FROM plan_areas WHERE id_explosion_material = :id_explosion_material");
        $stmt->execute(['id_explosion_material' => $id_explosion_material]);
      }
    } catch (\Exception $e) {
      $message = $e->getMessage();
      return ['info' => true, 'message' => $message];
    }
  }
}
