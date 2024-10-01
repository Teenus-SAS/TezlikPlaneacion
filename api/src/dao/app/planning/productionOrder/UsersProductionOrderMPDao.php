<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UsersProductionOrderMPDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllUserOPMPById($id_prod_order_material_user)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT u.id_user, u.firstname, u.lastname, u.email
                                      FROM prod_order_materials_users us
                                      INNER JOIN users u ON u.id_user = us.id_user_receive
                                      WHERE us.id_prod_order_material_user = :id_prod_order_material_user");
        $stmt->execute([
            'id_prod_order_material_user' => $id_prod_order_material_user
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $users;
    }

    public function saveUserOPMP($id_company, $id_prod_order_material_user, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO prod_order_part_deliv_users (id_company, id_prod_order_material_user, id_user_receive)
                                          VALUES (:id_company, :id_prod_order_material_user, :id_user_receive)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_prod_order_material_user' => $id_prod_order_material_user,
                'id_user_receive' => $id_user,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
