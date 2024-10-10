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

    public function findAllMaterialsComponentsByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM materials_components_users 
                                      WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $materials;
    }

    public function findAllMaterialsComponentsById($id_programming)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT
                                            mcu.id_materials_component_user,
                                            mcu.quantity,
                                            mcu.creation_date,
                                            u.id_user,
                                            u.firstname,
                                            u.lastname,
                                            u.email,
                                            m.id_material,
                                            m.reference,
                                            m.material
                                      FROM materials_components_users mcu
                                        INNER JOIN materials m ON m.id_material = mcu.id_material
                                        INNER JOIN users u ON u.id_user = mcu.id_user_accept
                                      WHERE mcu.id_programming = :id_programming AND mcu.id_material = :id_material");
        $stmt->execute([
            'id_programming' => $id_programming
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $materials = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $materials;
    }

    public function insertMaterialComponentUser($data, $id_user, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO materials_components_users (id_company, id_programming, id_material, quantity, id_user_accept)
                                          VALUES (:id_company, :id_programming, :id_material, :quantity, :id_user_accept)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_programming' => $data['idProgramming'],
                'id_material' => $data['idMaterial'],
                'quantity ' => $data['quantity'],
                'id_user_accept' => $id_user
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
