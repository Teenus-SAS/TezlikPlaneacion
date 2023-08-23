<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class RequisitionsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllRequisitionByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT r.id_requisition, m.id_material, m.reference, m.material, r.application_date, r.delivery_date, r.quantity, r.purchase_order, r.admission_date
                                      FROM requisitons r
                                        INNER JOIN materials m ON m.id_material = r.id_product
                                      WHERE r.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    /* Insertar requisicion */
    public function insertRequisitionByCompany($dataRequisition, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO requisitons (id_company, id_product, application_date, delivery_date, quantity, purchase_order) 
                                          VALUES (:id_company, :id_product, :application_date, :delivery_date, :quantity, :purchase_order)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_product' => $dataRequisition['idMaterial'],
                'application_date' => $dataRequisition['applicationDate'],
                'delivery_date' => $dataRequisition['deliveryDate'],
                'quantity' => $dataRequisition['quantity'],
                'purchase_order' => $dataRequisition['purchaseOrder']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    /* Actualizar requisicion */
    public function updateRequisition($dataRequisition)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE requisitons SET id_product = :id_product, application_date = :application_date, delivery_date = :delivery_date, 
                                                              quantity = :quantity, purchase_order = :purchase_order
                                    WHERE id_requisition = :id_requisition");
            $stmt->execute([
                'id_requisition' => $dataRequisition['idRequisition'],
                'id_product' => $dataRequisition['idMaterial'],
                'application_date' => $dataRequisition['applicationDate'],
                'delivery_date' => $dataRequisition['deliveryDate'],
                'quantity' => $dataRequisition['quantity'],
                'purchase_order' => $dataRequisition['purchaseOrder']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteRequisition($id_requisition)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM requisitons WHERE id_requisition = :id_requisition");
            $stmt->execute(['id_requisition' => $id_requisition]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM requisitons WHERE id_requisition = :id_requisition");
                $stmt->execute(['id_requisition' => $id_requisition]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
