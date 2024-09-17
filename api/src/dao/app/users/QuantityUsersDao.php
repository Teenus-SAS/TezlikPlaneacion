<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class QuantityUsersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /*Obtener cantidad para creacion de usuario permitidos*/

    public function quantityUsersAllows($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT quantity_user FROM admin_companies_licenses 
                                  WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $quantity_users_allows = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $this->logger->notice("usuario Obtenido", array('usuario' => $quantity_users_allows));
        return $quantity_users_allows;
    }

    /*Obtener cantidad de usuarios creados*/

    public function quantityUsersCreated($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(*) AS quantity_users FROM users WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $quantity_users_created = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $this->logger->notice("cantidad usuarios obtenidos", array('cantidad usuarios' => $quantity_users_created));
        return $quantity_users_created;
    }

    /*Obtener cantidad de usuarios activos*/
    /*Obtener cantidad de usuarios permitidos por empresa*/
}
