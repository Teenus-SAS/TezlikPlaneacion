<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralUnitSalesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findSales($dataSale, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_unit_sales FROM plan_unit_sales
                                  WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataSale['idProduct'],
            'id_company' => $id_company
        ]);
        $findSales = $stmt->fetch($connection::FETCH_ASSOC);
        return $findSales;
    }

    public function findSaleDaysByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $findSales = $stmt->fetch($connection::FETCH_ASSOC);
        return $findSales;
    }
}
