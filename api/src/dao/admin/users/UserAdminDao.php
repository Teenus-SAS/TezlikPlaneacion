<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UserAdminDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllUser()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT u.id_user, u.firstname, u.lastname, u.position, u.email, c.id_company, c.company 
                                      FROM users u
                                        INNER JOIN admin_companies c ON c.id_company = u.id_company");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
        return $users;
    }

    public function findAllUserAdmins()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM admin_users");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
        return $users;
    }

    public function findUserAdmin()
    {
        session_start();
        $email = $_SESSION['email'];
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM admin_users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $admin = $stmt->fetch($connection::FETCH_ASSOC);
        return $admin;
    }

    public function insertUserAdmin($dataUser, $newPass)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $pass = password_hash($newPass, PASSWORD_DEFAULT);

            $stmt = $connection->prepare("INSERT INTO admin_users (firstname, lastname, email, password) 
                                          VALUES (:firstname, :lastname, :email, :pass)");
            $stmt->execute([
                'firstname' => $dataUser['firstname'],
                'lastname' => $dataUser['lastname'],
                'email' => $dataUser['email'],
                'pass' => $pass
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Email duplicado. Ingrese una nuevo email';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateUser($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();

        try {

            $stmt = $connection->prepare("UPDATE admin_users SET firstname = :firstname, lastname = :lastname, email = :email
                                          WHERE id_admin = :id_admin");
            $stmt->execute([
                'firstname' => $dataUser['firstname'],
                'lastname' => $dataUser['lastname'],
                'id_admin' => $dataUser['idAdmin'],
                'email' => $dataUser['email']
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Email duplicado. Ingrese una nuevo email';

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteUser($id_admin)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("DELETE FROM admin_users WHERE id_admin = :id_admin");
            $stmt->execute(['id_admin' => $id_admin]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
