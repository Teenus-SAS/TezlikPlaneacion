<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DashboardGeneralsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Obtener todos los Productos
    public function findAllProducts()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(id_product) AS products FROM products");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("ProductsCount", array('ProductsCount' => $products));
        return $products;
    }

    // Obtener cantidad de empresas activas
    public function findAllCompanies()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(id_company) AS companies FROM companies");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $companies = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("CompaniesCount", array('CompaniesCount' => $companies));
        return $companies;
    }

    // Obtener todos los usuarios
    public function findAllUsers()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(id_user) AS users FROM users");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("UsersCount", array('UsersCount' => $users));
        return $users;
    }

    // Obtener todos los Usuarios en sesión
    public function findAllActiveUsersSession()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(id_user) AS users_session FROM users WHERE session_active = 1");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $usersSessionActive = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("UsersSessionActiveCount", array('UsersSessionActiveCount' => $usersSessionActive));
        return $usersSessionActive;
    }
}
