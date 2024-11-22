<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class AutenticationUserDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findByEmail($dataUser, $op)
  {
    $connection = Connection::getInstance()->getConnection();

    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $connection->prepare($sql);
    $stmt->execute(['email' => $dataUser]);
    $user = $stmt->fetch($connection::FETCH_ASSOC);

    if ($op == 2 && !$user) {
      $stmt = $connection->prepare("SELECT * FROM admin_users WHERE email = :email");
      $stmt->execute(['email' => $dataUser]);
      $user = $stmt->fetch($connection::FETCH_ASSOC);
      $user['rol'] = 'admin';
    }

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    $this->logger->notice("usuarios Obtenidos", array('usuarios' => $user));
    return $user;
  }

  /* public function checkUserAdmin($dataUser)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM admin_users WHERE email = :email");
    $stmt->execute(['email' => $dataUser]);
    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    $user = $stmt->fetch($connection::FETCH_ASSOC);

    return $user;
  } */
}
