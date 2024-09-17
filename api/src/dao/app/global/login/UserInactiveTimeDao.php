<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

require_once __DIR__ . '/StatusActiveUserDao.php';

class UserInactiveTimeDao extends StatusActiveUserDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findSession()
  {
    if (!isset($_SESSION['idUser']) && !isset($_SESSION['case']))
      return 1;
    else {
      $id_user = $_SESSION['idUser'];
      $case = $_SESSION['case'];
      $connection = Connection::getInstance()->getConnection();
      if ($case == 1) {
        $stmt = $connection->prepare("SELECT session_active FROM users WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);
      } else if ($case == 2) {
        $stmt = $connection->prepare("SELECT session_active FROM admin_users WHERE id_admin = :id_admin");
        $stmt->execute(['id_admin' => $id_user]);
      }
    }
    $session = $stmt->fetch($connection::FETCH_ASSOC);
    return $session;
  }
}
