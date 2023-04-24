<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsProcessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Consultar si existe el proceso del prodcuto en la BD
    public function findProductProcess($dataProductProcess, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_product_process FROM products_process 
                                      WHERE id_product = :id_product AND id_process = :id_process 
                                      AND id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataProductProcess['idProduct'],
            'id_process' => $dataProductProcess['idProcess'],
            'id_machine' => $dataProductProcess['idMachine'],
            'id_company' => $id_company
        ]);
        $findProductProcess = $stmt->fetch($connection::FETCH_ASSOC);

        return $findProductProcess;
    }
}
