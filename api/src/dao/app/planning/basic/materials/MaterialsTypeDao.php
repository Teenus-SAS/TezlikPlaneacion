<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MaterialsTypeDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllMaterialsTypeByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM materials_type
                                      WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("materials", array('materials' => $materials));
        return $materials;
    }

    public function findMaterialsType($dataMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM materials_type
                                      WHERE material_type = :material_type AND id_company = :id_company");
        $stmt->execute([
            'material_type' => trim(strtoupper($dataMaterial['materialType'])),
            'id_company' => $id_company
        ]);
        $material = $stmt->fetch($connection::FETCH_ASSOC);
        return $material;
    }
}
