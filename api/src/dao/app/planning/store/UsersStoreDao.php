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

    public function findAllUserStoreById($id_programming, $id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            pom.id_prod_order_material, 
                                            pom.id_programming, 
                                            m.id_material, 
                                            m.reference, 
                                            m.material, 
                                            IFNULL(mi.quantity, 0) AS quantity_material,
                                            IFNULL(mi.delivery_date, '0000-00-00 00:00:00') AS delivery_date,
                                            pom.quantity,
                                            IFNULL(last_user.id_user_delivered, 0) AS id_user_delivered,
                                            IFNULL(last_user.firstname_delivered, '') AS firstname_delivered,
                                            IFNULL(last_user.lastname_delivered, '') AS lastname_delivered
                                            IFNULL(last_user.email_delivered, '') AS email_delivered
                                      FROM prod_order_materials pom  
                                        -- Subconsulta para obtener el último usuario de entrega
                                        LEFT JOIN(
                                            SELECT cur.id_material,
                                                cur.id_programming,
                                                curd.id_user AS id_user_delivered,
                                                curd.firstname AS firstname_delivered,
                                                curd.lastname AS lastname_delivered,
                                                curd.email AS email_delivered,
                                            FROM store_users cur
                                            INNER JOIN users curd ON curd.id_user = cur.id_user_delivered 
                                            WHERE cur.id_material = (
                                                    SELECT MAX(cur_inner.id_material)
                                                    FROM store_users cur_inner
                                                    WHERE cur_inner.id_material = cur.id_material 
                                                    AND cur_inner.id_programming = cur.id_programming
                                            )
                                        ) AS last_user ON last_user.id_material = pom.id_material AND last_user.id_programming = pom.id_programming
                                      WHERE pom.id_programming = :id_programming AND pom.id_material = :id_material");
        $stmt->execute([
            'id_material' => $id_material,
            'id_programming' => $id_programming
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $users;
    }

    public function saveUserDeliveredMaterial($id_company, $id_material, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO store_users (id_company, id_material, id_user_delivered)
                                          VALUES (:id_company, :id_material, :id_user_delivered)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_user_delivered' => $id_user,
                'id_material' => $id_material,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
