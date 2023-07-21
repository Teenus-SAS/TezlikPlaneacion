<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Consultar si existe materia prima en la BD 
    public function findMaterial($dataMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_material FROM materials 
                                    WHERE reference = :reference 
                                    OR material = :material 
                                    AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataMaterial['refRawMaterial']),
            'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
            'id_company' => $id_company,
        ]);
        $findMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findMaterial;
    } */

    /* Consultar si existe materia prima en la BD */
    public function findMaterial($dataMaterial, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_material FROM materials 
                                        WHERE (reference = :reference OR material = :material) 
                                        AND id_company = :id_company");
        $stmt->execute([
            'reference' => trim($dataMaterial['refRawMaterial']),
            'material' => strtoupper(trim($dataMaterial['nameRawMaterial'])),
            'id_company' => $id_company,
        ]);
        $findMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findMaterial;
    }
}
