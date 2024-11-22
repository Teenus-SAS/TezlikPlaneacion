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
    $stmt = $connection->prepare("SELECT
                                    -- Columnas
                                      m.id_material,
                                      m.reference,
                                      m.material,
                                      m.material AS descript,
                                      mg.id_magnitude,
                                      mg.magnitude,
                                      u.id_unit,
                                      m.grammage,
                                      u.unit,
                                      u.abbreviation,
                                      mi.quantity,
                                      mi.reserved,
                                      mi.minimum_stock,
                                      mi.transit,
                                      mi.days,
                                      m.id_material_type,
                                      IFNULL(mt.material_type, '') AS material_type,
                                      m.cost,
                                      IFNULL(p.id_product, 0) AS origin
                                  FROM materials m
                                    INNER JOIN inv_materials mi ON mi.id_material = m.id_material
                                    LEFT JOIN materials_type mt ON mt.id_material_type = m.id_material_type
                                    LEFT JOIN products p ON p.reference = m.reference AND p.product = m.material AND p.origin = 1
                                    INNER JOIN admin_units u ON u.id_unit = m.unit
                                    INNER JOIN admin_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                  WHERE m.id_company = :id_company
                                  ORDER BY m.reference ASC");
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

    try {
      $stmt = $connection->prepare("INSERT INTO materials (id_company, id_material_type, reference, material, unit, cost) 
                                      VALUES(:id_company, :id_material_type, :reference, :material, :unit, :cost)");
      $stmt->execute([
        'id_company' => $id_company,
        'id_material_type' => $dataMaterial['idMaterialType'],
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
        'unit' => $dataMaterial['unit'],
        'cost' => $dataMaterial['costMaterial'],
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

    try {
      $stmt = $connection->prepare("UPDATE materials SET id_material_type = :id_material_type, reference = :reference, material = :material, unit = :unit, cost = :cost
                                    WHERE id_material = :id_material");
      $stmt->execute([
        'id_material' => $dataMaterial['idMaterial'],
        'id_material_type' => $dataMaterial['idMaterialType'],
        'reference' => trim($dataMaterial['refRawMaterial']),
        'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
        'unit' => $dataMaterial['unit'],
        'cost' => $dataMaterial['costMaterial']
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
