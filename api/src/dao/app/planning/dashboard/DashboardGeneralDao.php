<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DashboardGeneralDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findClassificationByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT SUM(CASE WHEN classification = 'A' THEN 1 ELSE 0 END) AS A, SUM(CASE WHEN classification = 'B' THEN 1 ELSE 0 END) AS B,
                                             SUM(CASE WHEN classification = 'C' THEN 1 ELSE 0 END) AS C
                                      FROM products
                                      WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $classification = $stmt->fetch($connection::FETCH_ASSOC);
        return $classification;
    }
}
