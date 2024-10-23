<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UsersStoreDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllStoreByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM store_users 
                                      WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $store = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $store;
    }

    public function findAllUserStoreById($id_programming, $id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            stu.id_user_store, 
                                            u.id_user, 
                                            u.firstname, 
                                            u.lastname, 
                                            u.email, 
                                            stu.delivery_store
                                      FROM store_users stu
                                        INNER JOIN users u ON u.id_user = stu.id_user_delivered
                                      WHERE stu.id_programming = :id_programming AND stu.id_material = :id_material");
        $stmt->execute([
            'id_programming' => $id_programming,
            'id_material' => $id_material
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $users;
    }

    public function saveUserDeliveredMaterial(
        $id_company,
        $id_user,
        $dataStore
    ) {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO store_users (id_company, id_programming, id_material, delivery_store, delivery_pending, id_user_delivered)
                                          VALUES (:id_company, :id_programming, :id_material, :delivery_store, :delivery_pending, :id_user_delivered)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_programming' => $dataStore['idProgramming'],
                'id_material' => $dataStore['idMaterial'],
                'delivery_store' => $dataStore['delivered'],
                'delivery_pending' => $dataStore['pending'],
                'id_user_delivered' => $id_user,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
