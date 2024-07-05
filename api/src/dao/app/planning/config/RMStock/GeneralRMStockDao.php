<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralRMStockDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findStock($dataStock)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM stock_materials WHERE id_material = :id_material AND id_provider = :id_provider");
        $stmt->execute([
            'id_material' => $dataStock['idMaterial'],
            'id_provider' => $dataStock['idProvider']
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $stock = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("stock", array('stock' => $stock));
        return $stock;
    }
}
