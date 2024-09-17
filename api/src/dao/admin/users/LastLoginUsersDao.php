<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LastLoginUsersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    //Obtener ultimos logueos en orden de todos los usuarios
    public function loginUsers()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT cp.company, us.firstname, us.lastname, us.last_login, us.id_user 
                                      FROM admin_companies cp INNER JOIN users us ON cp.id_company = us.id_company 
                                      WHERE session_active = 1 ORDER BY us.last_login DESC");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $lastLog = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("Session Active Users", array('Session' => $lastLog));

        return $lastLog;
    }
}
