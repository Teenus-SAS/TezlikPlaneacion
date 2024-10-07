<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MaterialsComponentsUsersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function insertMaterialComponentUser($data, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO materials_components_users (id_company, id_programming, id_material, quantity)
                                          VALUES (:id_company, :id_programming, :id_material, :quantity)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_programming' => $data['idProgramming'],
                'id_material' => $data['idMaterial'],
                'quantity ' => $data['quantity']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
