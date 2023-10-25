<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralRequisitionsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllActualRequisitionByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT r.id_requisition, r.id_material, m.reference, m.material, r.application_date, r.delivery_date, r.quantity, r.purchase_order, r.admission_date
                                      FROM requisitons r
                                        INNER JOIN materials m ON m.id_material = r.id_material
                                      WHERE r.id_company = :id_company 
                                      AND (r.admission_date IS NULL OR MONTH(r.admission_date) = MONTH(CURRENT_DATE))");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    public function findAllMinAndMaxRequisitionByCompany($min_date, $max_date, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT r.id_requisition, r.id_material, m.reference, m.material, r.application_date, r.delivery_date, r.quantity, r.purchase_order, r.admission_date
                                      FROM requisitons r
                                        INNER JOIN materials m ON m.id_material = r.id_material
                                      WHERE r.id_company = :id_company
                                      AND (r.application_date BETWEEN :min_date AND :max_date)");
        $stmt->execute([
            'id_company' => $id_company,
            'min_date' => $min_date,
            'max_date' => $max_date
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    public function findRequisition($dataRequisition, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM requisitons WHERE id_material = :id_material AND id_company = :id_company");
        $stmt->execute([
            'id_material' => $dataRequisition['idMaterial'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $molds = $stmt->fetch($connection::FETCH_ASSOC);
        return $molds;
    }

    public function updateDateRequisition($dataRequisition)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE requisitons SET admission_date = :admission_date WHERE id_requisition = :id_requisition");
            $stmt->execute([
                'id_requisition' => $dataRequisition['idRequisition'],
                'admission_date' => $dataRequisition['date'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
