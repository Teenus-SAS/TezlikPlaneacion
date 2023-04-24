<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class StatusActiveUserDao
{
  private $logger;

  public function __construct()
  {
    //$this->logger = new Logger(self::class);
    //$this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findSessionUser($id_user)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT session_active FROM users WHERE id_user = :id_user");
    $stmt->execute(['id_user' => $id_user]);
    $session = $stmt->fetch($connection::FETCH_ASSOC);
    return $session;
  }

  /* Actualizar estado de sesion de Usuario */
  public function changeStatusUserLogin()
  {
    $id_user = $_SESSION['idUser'];
    $case = $_SESSION['case'];

    $connection = Connection::getInstance()->getConnection();

    if ($case == 1) {
      // $stmt = $connection->prepare("SELECT session_active FROM users WHERE id_user = :id_user");
      // $stmt->execute(['id_user' => $id_user]);
      // $session = $stmt->fetch($connection::FETCH_ASSOC);
      $session = $this->findSessionUser($id_user);
      $session = $session['session_active'];

      ($session == 1 ? $session = 0 : $session == 0) ? $session = 1 : $session;

      $stmt = $connection->prepare("UPDATE users SET session_active = :session_active WHERE id_user = :id_user");
      $stmt->execute(['session_active' => $session, 'id_user' => $id_user]);
    } else if ($case == 2) {
      $stmt = $connection->prepare("SELECT session_active FROM admins WHERE id_admin = :id_admin");
      $stmt->execute(['id_admin' => $id_user]);
      $session = $stmt->fetch($connection::FETCH_ASSOC);
      $session = $session['session_active'];

      ($session == 1 ? $session = 0 : $session == 0) ? $session = 1 : $session;

      $stmt = $connection->prepare("UPDATE admins SET session_active = :session_active WHERE id_admin = :id_admin");
      $stmt->execute(['session_active' => $session, 'id_admin' => $id_user]);
    }
  }
}
