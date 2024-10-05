<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UsersRequisitionsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllUsersRequesitionsMaterialsById($id_requisition_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT u.id_user, u.firstname, u.lastname, u.email
                                      FROM requisitions_materials_users ur
                                      INNER JOIN users u ON u.id_user = ur.id_user_deliver
                                      WHERE ur.id_requisition_material = :id_requisition_material");
        $stmt->execute([
            'id_requisition_material' => $id_requisition_material
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $users;
    }

    public function saveUserDeliverRequisitionMaterial($id_company, $id_requisition_material, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO requisitions_materials_users (id_company, id_requisition_material, id_user_deliver)
                                          VALUES (:id_company, :id_requisition_material, :id_user_deliver)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_user_deliver' => $id_user,
                'id_requisition_material' => $id_requisition_material,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function findAllUsersRequesitionsProductsById($id_requisition_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT u.id_user, u.firstname, u.lastname, u.email
                                      FROM requisitions_products_users ur
                                      INNER JOIN users u ON u.id_user = ur.id_user_deliver
                                      WHERE ur.id_requisition_product = :id_requisition_product");
        $stmt->execute([
            'id_requisition_product' => $id_requisition_product
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $users;
    }

    public function saveUserDeliverRequisitionproduct($id_company, $id_requisition_product, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO requisitions_products_users (id_company, id_requisition_product, id_user_deliver)
                                          VALUES (:id_company, :id_requisition_product, :id_user_deliver)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_user_deliver' => $id_user,
                'id_requisition_product' => $id_requisition_product,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
