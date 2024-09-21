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

        $stmt = $connection->prepare("SELECT po.id_part_deliv, po.id_programming, p.id_product, p.reference, p.product, IFNULL(pi.quantity, 0) AS quantity_product, po.start_date, po.end_date, po.operator, u.firstname, u.lastname, 
                                             po.waste, po.partial_quantity, po.receive_date, IFNULL(ur.firstname, '') AS firstname_deliver, IFNULL(ur.lastname , '') AS lastname_deliver
                                      FROM prod_order_part_deliv po
                                        INNER JOIN users u ON u.id_user = po.operator
                                        INNER JOIN programming pg ON pg.id_programming = po.id_programming
                                        INNER JOIN products p ON p.id_product = pg.id_product
                                        LEFT JOIN inv_products pi ON pi.id_product = pg.id_product
                                        LEFT JOIN users ur ON ur.id_user = po.id_user_receive
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

    public function updateDateReceive($dataProgramming)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE prod_order_part_deliv SET receive_date = :receive_date WHERE id_part_deliv = :id_part_deliv");
            $stmt->execute([
                'id_part_deliv' => $dataProgramming['idPartDeliv'],
                'receive_date' => $dataProgramming['date'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
