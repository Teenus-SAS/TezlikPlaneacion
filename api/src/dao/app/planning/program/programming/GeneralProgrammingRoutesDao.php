<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProgrammingRoutesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findProgrammingRoutesByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM programming_routes
                                      WHERE id_product = :id_product");
        $stmt->execute([
            'id_product' => $id_product
        ]);
        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findProgrammingRoutes($id_product, $id_order)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM programming_routes
                                      WHERE id_product = :id_product AND id_order = :id_order");
        $stmt->execute([
            'id_product' => $id_product,
            'id_order' => $id_order
        ]);
        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }
}
