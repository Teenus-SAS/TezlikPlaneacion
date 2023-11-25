<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MaterialsDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllMaterialsByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT m.id_material, m.reference, m.material, m.material AS descript, mg.id_magnitude, mg.magnitude, u.id_unit, 
                                         u.unit, u.abbreviation, m.quantity, IFNULL((SELECT IFNULL(SUM(pg.quantity), 0) FROM programming pg 
                                                                              LEFT JOIN plan_orders o ON o.id_order = pg.id_order
                                                                              LEFT JOIN products_materials pm ON pm.id_product = o.id_product WHERE pm.id_material = m.id_material AND o.status = 'Programado'), 0) AS reserved
                                  FROM materials m
                                    INNER JOIN convert_units u ON u.id_unit = m.unit
                                    INNER JOIN convert_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                  WHERE m.id_company = :id_company ORDER BY m.material ASC");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("materials", array('materials' => $materials));
    return $materials;
  }

  /* Insertar materia prima */
  public function insertMaterialsByCompany($dataMaterial, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    $quantity = str_replace('.', '', $dataMaterial['quantity']);
    $quantity = str_replace(',', '.', $quantity);

    try {
      $stmt = $connection->prepare("INSERT INTO materials (id_company, reference, material, unit, quantity) 
                                      VALUES(:id_company, :reference, :material, :unit, :quantity)");
      $stmt->execute([
        'id_company' => $id_company,
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
        'unit' => $dataMaterial['unit'],
        'quantity' => $quantity
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'La referencia ya existe. Ingrese una nueva referencia';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar materia prima  */
  public function updateMaterialsByCompany($dataMaterial)
  {
    $connection = Connection::getInstance()->getConnection();

    $quantity = str_replace('.', '', $dataMaterial['quantity']);
    $quantity = str_replace(',', '.', $quantity);

    try {
      $stmt = $connection->prepare("UPDATE materials SET reference = :reference, material = :material, unit = :unit, quantity = :quantity
                                    WHERE id_material = :id_material");
      $stmt->execute([
        'id_material' => $dataMaterial['idMaterial'],
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
        'unit' => trim($dataMaterial['unit']),
        'quantity' => $quantity
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteMaterial($id_material)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("SELECT * FROM materials WHERE id_material = :id_material");
      $stmt->execute(['id_material' => $id_material]);
      $rows = $stmt->rowCount();

      if ($rows > 0) {
        $stmt = $connection->prepare("DELETE FROM materials WHERE id_material = :id_material");
        $stmt->execute(['id_material' => $id_material]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
      }
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'Esta materia prima no se puede eliminar, esta configurada a un producto';

      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
