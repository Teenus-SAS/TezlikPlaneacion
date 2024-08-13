<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralSellersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findSeller($dataSeller, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM sellers WHERE firstname = :firstname AND lastname = :lastname 
                                      AND email = :email AND id_company = :id_company");
        $stmt->execute([
            'firstname' => strtoupper(trim($dataSeller['firstname'])),
            'lastname' => strtoupper(trim($dataSeller['lastname'])),
            'email' => trim($dataSeller['email']),
            'id_company' => $id_company,
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $seller = $stmt->fetch($connection::FETCH_ASSOC);
        return $seller;
    }
}
