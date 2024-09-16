<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UsersOfficesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllUsersOfficesById($id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT u.id_user, u.firstname, u.lastname, u.email
                                      FROM offices_users uof
                                      INNER JOIN users u ON u.id_user = uof.id_user_deliver
                                      WHERE uof.id_order = :id_order");
        $stmt->execute([
            'id_order' => $id_order
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetch($connection::FETCH_ASSOC);
        return $users;
    }

    public function saveUserDeliverOffices($id_company, $id_order, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO offices_users (id_company, id_order, id_user_deliver)
                                          VALUES (:id_company, :id_order, :id_user_deliver)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_user_deliver' => $id_user,
                'id_order' => $id_order,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteUserOffice($id_user_office)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM offices_users WHERE id_user_office = :id_user_office");
            $stmt->execute(['id_user_office' => $id_user_office]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM offices_users WHERE id_user_office = :id_user_office");
                $stmt->execute(['id_user_office' => $id_user_office]);
            }
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
