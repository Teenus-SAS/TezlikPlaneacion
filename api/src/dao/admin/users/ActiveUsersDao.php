<?php

namespace TezlikPlaneacion\dao;

use DateTime;
use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ActiveUsersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    //OBTENER CANTIDAD DE USUARIOS GENERALES ACTIVOS
    public function usersStatus()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT COUNT(session_active)AS quantity FROM users WHERE session_active = 1");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $active = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("Session Active Users", array('licenses' => $active));

        return $active;
    }
}
