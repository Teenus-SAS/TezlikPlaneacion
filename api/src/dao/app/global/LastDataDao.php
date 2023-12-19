<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LastDataDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Login */
    public function findLastLogins()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT cp.company, us.firstname, us.lastname, us.last_login 
                                      FROM users us INNER JOIN companies cp ON cp.id_company = us.id_company
                                      WHERE us.session_active = 1 ORDER BY us.last_login DESC");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $lastLogs = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("Last Login", array('Last Login' => $lastLogs));

        return $lastLogs;
    }

    /* Compañia */
    public function findLastCompany()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_company) AS idCompany FROM companies");
        $stmt->execute();
        $lastId = $stmt->fetch($connection::FETCH_ASSOC);

        return $lastId;
    }

    /* Usuario */
    public function findLastInsertedUser($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_user) AS idUser FROM users u WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $user = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        return $user;
    }

    /* Productos */
    public function lastInsertedProductId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_product) AS id_product FROM products WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $id_product = $query->fetch($connection::FETCH_ASSOC);
        return $id_product;
    }

    public function findLastInsertedClient()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_client) AS id_client FROM plan_clients");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $client = $stmt->fetch($connection::FETCH_ASSOC);
        return $client;
    }

    public function findLastInsertedProgramming($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_programming) AS id_programming FROM programming WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findLastInsertedCiclesMachine($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_cicles_machine) AS id_cicles_machine FROM plan_cicles_machine WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findLastInsertedOrder($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_order) AS id_order FROM plan_orders WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        return $order;
    }
}
