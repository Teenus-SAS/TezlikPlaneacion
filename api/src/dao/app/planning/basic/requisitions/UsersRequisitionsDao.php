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

    public function findAllUsersRequesitionsById($id_requisition)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT u.id_user, u.firstname, u.lastname, u.email
                                      FROM requisitions_users ur
                                      INNER JOIN users u ON u.id_user = ur.id_user_deliver
                                      WHERE ur.id_requisition = :id_requisition");
        $stmt->execute([
            'id_requisition' => $id_requisition
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $users;
    }

    public function saveUserDeliverRequisition($id_company, $id_requisition, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO requisitions_users (id_company, id_requisition, id_user_deliver)
                                          VALUES (:id_company, :id_requisition, :id_user_deliver)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_user_deliver' => $id_user,
                'id_requisition' => $id_requisition,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
