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
                                            stu.delivery_store,
                                            stu.delivery_pending,
                                            mi.id_material,
                                            mi.quantity AS quantity_material,
                                            -- Nueva subconsulta para evitar duplicados en la suma de reserved
                                                IFNULL((
                                                    SELECT SUM(DISTINCT pg_inner.quantity * pm_inner.quantity)
                                                    FROM products_materials pm_inner
                                                    INNER JOIN programming pg_inner ON pg_inner.id_product = pm_inner.id_product
                                                    WHERE pg_inner.id_programming = stu.id_programming
                                                    AND pm_inner.id_material = stu.id_material
                                                ), 0) AS reserved
                                      FROM store_users stu
                                        INNER JOIN users u ON u.id_user = stu.id_user_delivered
                                        INNER JOIN inv_materials mi ON mi.id_material = stu.id_material
                                      WHERE stu.id_programming = :id_programming AND stu.id_material = :id_material
                                      GROUP BY stu.id_user_store");
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

    public function updateUserDeliveredMaterial(
        $dataStore
    ) {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE store_users SET delivery_store = :delivery_store, delivery_pending = :delivery_pending
                                          WHERE id_user_store = :id_user_store");
            $stmt->execute([
                'delivery_store' => $dataStore['delivery_store'],
                'delivery_pending' => $dataStore['delivery_pending'],
                'id_user_store' => $dataStore['id_user_store'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
