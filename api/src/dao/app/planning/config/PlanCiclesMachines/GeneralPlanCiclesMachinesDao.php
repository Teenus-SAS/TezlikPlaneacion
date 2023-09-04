<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralPlanCiclesMachinesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Buscar si existe en la BD
    public function findPlanCiclesMachine($dataCiclesMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT *  FROM plan_cicles_machine
                                      WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataCiclesMachine['idProduct'],
            'id_company' => $id_company
        ]);
        $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }

    public function findPlanCiclesMachineByProductAndMachine($id_product, $id_machine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT *  FROM plan_cicles_machine
                                      WHERE id_product = :id_product AND id_machine = :id_machine AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $id_product,
            'id_machine' => $id_machine,
            'id_company' => $id_company
        ]);
        $planCiclesMachine = $stmt->fetch($connection::FETCH_ASSOC);
        return $planCiclesMachine;
    }
}
