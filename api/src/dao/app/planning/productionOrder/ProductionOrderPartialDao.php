<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductionOrderPartialDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllOPPartialByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT po.id_part_deliv, po.id_programming, po.start_date, po.end_date, po.operator, u.firstname, u.lastname, po.waste, po.partial_quantity
                                      FROM prod_order_part_deliv po
                                        INNER JOIN users u ON u.id_user = po.operator
                                      WHERE po.id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findAllOPPartialById($id_programming, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT po.id_part_deliv, po.id_programming, po.start_date, po.end_date, po.operator, u.firstname, u.lastname, po.waste, po.partial_quantity
                                      FROM prod_order_part_deliv po
                                        INNER JOIN users u ON u.id_user = po.operator
                                      WHERE po.id_programming = :id_programming AND po.id_company = :id_company");
        $stmt->execute([
            'id_programming' => $id_programming,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function insertOPPartialByCompany($dataProgramming, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO prod_order_part_deliv (id_company, id_programming, start_date, end_date, operator, waste, partial_quantity)
                                          VALUES (:id_company, :id_programming, :start_date, :end_date, :operator, :waste, :partial_quantity)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_programming' => $dataProgramming['idProgramming'],
                'start_date' => $dataProgramming['startDate'],
                'end_date' => $dataProgramming['endDate'],
                'operator' => $dataProgramming['operator'],
                'waste' => $dataProgramming['waste'],
                'partial_quantity' => $dataProgramming['partialQuantity']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
