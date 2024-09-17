<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LotsProductsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcEconomicLot($dataProduct, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT ((m.assembly_production - (m.assembly_time / 60)) * pcm.cicles_hour) AS economic_lot 
                                      FROM products p 
                                        INNER JOIN machine_cicles pcm ON pcm.id_product = p.id_product 
                                        INNER JOIN plan_inv_molds m ON m.id_mold = p.id_mold 
                                      WHERE p.id_product = :id_product AND p.id_company = :id_company;");
        $stmt->execute([
            'id_product' => $dataProduct['idProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $economicLot = $stmt->fetch($connection::FETCH_ASSOC);
        return $economicLot;
    }
}
