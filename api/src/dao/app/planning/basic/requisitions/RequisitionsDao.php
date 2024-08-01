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
        $stmt = $connection->prepare("SELECT r.id_requisition, r.id_material, m.reference, m.material, r.application_date, r.delivery_date, r.quantity_requested, r.quantity_required, 
                                             r.purchase_order, r.admission_date, IFNULL(r.id_provider, 0) AS id_provider, IFNULL(c.client, '') AS provider, IFNULL(u.id_user, 0) AS id_user, IFNULL(u.firstname, '') AS firstname, IFNULL(u.lastname, '') AS lastname
                                      FROM requisitions r
                                        INNER JOIN materials m ON m.id_material = r.id_material
                                        LEFT JOIN plan_clients c ON c.id_client = r.id_provider
                                        LEFT JOIN users u ON u.id_user = r.id_user_requisition
                                      WHERE r.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    /* Insertar requisicion */
    public function insertRequisitionManualByCompany($dataRequisition, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO requisitions (id_company, id_material, id_provider, application_date, delivery_date, , quantity_requested, purchase_order, id_user_requisition) 
                                          VALUES (:id_company, :id_material, :id_provider, :application_date, :delivery_date, :quantity_requested, :purchase_order, :id_user)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_material' => $dataRequisition['idMaterial'],
                'id_provider' => $dataRequisition['idProvider'],
                'application_date' => $dataRequisition['applicationDate'],
                'delivery_date' => $dataRequisition['deliveryDate'],
                'quantity_requested' => $dataRequisition['requestedQuantity'],
                'purchase_order' => $dataRequisition['purchaseOrder'],
                'id_user' => $dataRequisition['idUser']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    /* Actualizar requisicion */
    public function updateRequisitionManual($dataRequisition)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE requisitions SET id_material = :id_material, id_provider = :id_provider, application_date = :application_date, 
                                                                  delivery_date = :delivery_date, quantity_requested = :quantity_requested, purchase_order = :purchase_order, id_user_requisition = :id_user
                                    WHERE id_requisition = :id_requisition");
            $stmt->execute([
                'id_requisition' => $dataRequisition['idRequisition'],
                'id_material' => $dataRequisition['idMaterial'],
                'id_provider' => $dataRequisition['idProvider'],
                'application_date' => $dataRequisition['applicationDate'],
                'delivery_date' => $dataRequisition['deliveryDate'],
                'quantity_requested' => $dataRequisition['requestedQuantity'],
                'purchase_order' => $dataRequisition['purchaseOrder'],
                'id_user' => $dataRequisition['idUser']
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
            $stmt = $connection->prepare("SELECT * FROM requisitions WHERE id_requisition = :id_requisition");
            $stmt->execute(['id_requisition' => $id_requisition]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM requisitions WHERE id_requisition = :id_requisition");
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
