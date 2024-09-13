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
        $stmt = $connection->prepare("SELECT r.id_requisition, r.id_material, m.reference, m.material, r.creation_date, r.application_date, r.delivery_date, r.quantity_requested, r.quantity_required, r.purchase_order, 
                                             r.admission_date, cu.abbreviation, IFNULL(r.id_provider, 0) AS id_provider, IFNULL(c.client, '') AS provider,
                                             IFNULL(ur.id_user, 0) AS id_user_requisition, IFNULL(ur.firstname, '') AS firstname_requisition, IFNULL(ur.lastname, '') AS lastname_requisition, IFNULL(urd.id_user, 0) AS id_user_deliver, IFNULL(urd.firstname, '') AS firstname_deliver, IFNULL(urd.lastname, '') AS lastname_deliver
                                      FROM requisitions r
                                        INNER JOIN materials m ON m.id_material = r.id_material 
                                        INNER JOIN convert_units cu ON cu.id_unit = m.unit
                                        LEFT JOIN plan_clients c ON c.id_client = r.id_provider
                                        LEFT JOIN users ur ON ur.id_user = r.id_user_requisition
                                        LEFT JOIN users urd ON urd.id_user = r.id_user_deliver
                                      WHERE r.id_company = :id_company 
                                      AND (r.admission_date IS NULL OR MONTH(r.admission_date) = MONTH(CURRENT_DATE))
                                      ORDER BY r.admission_date ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    public function findAllMinAndMaxRequisitionByCompany($min_date, $max_date, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT r.id_requisition, r.id_material, m.reference, m.material, r.creation_date, r.application_date, r.delivery_date, r.quantity_requested, r.quantity_required, 
                                             r.purchase_order, r.admission_date, cu.abbreviation, IFNULL(r.id_provider, 0) AS id_provider, IFNULL(c.client, '') AS provider,
                                             IFNULL(ur.id_user, 0) AS id_user_requisition, IFNULL(ur.firstname, '') AS firstname_requisition, IFNULL(ur.lastname, '') AS lastname_requisition
                                      FROM requisitions r
                                        INNER JOIN materials m ON m.id_material = r.id_material
                                        INNER JOIN convert_units cu ON cu.id_unit = m.unit
                                        LEFT JOIN plan_clients c ON c.id_client = r.id_provider
                                        LEFT JOIN users ur ON ur.id_user = r.id_user_requisition
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

        $stmt = $connection->prepare("SELECT * FROM requisitions WHERE id_material = :id_material AND id_company = :id_company");
        $stmt->execute([
            'id_material' => $dataRequisition['idMaterial'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $molds = $stmt->fetch($connection::FETCH_ASSOC);
        return $molds;
    }

    public function findRequisitionByApplicationDate($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM requisitions WHERE id_material = :id_material AND application_date = '0000-00-00'");
        $stmt->execute([
            'id_material' => $id_material
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $molds = $stmt->fetch($connection::FETCH_ASSOC);
        return $molds;
    }

    /* Insertar requisicion Auto*/
    public function insertRequisitionAutoByCompany($dataRequisition, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO requisitions (id_company, id_material, id_provider, application_date, delivery_date, quantity_required, purchase_order) 
                                          VALUES (:id_company, :id_material, :id_provider, :application_date, :delivery_date, :quantity_required, :purchase_order)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_material' => $dataRequisition['idMaterial'],
                'id_provider' => $dataRequisition['idProvider'],
                'application_date' => $dataRequisition['applicationDate'],
                'delivery_date' => $dataRequisition['deliveryDate'],
                'quantity_required' => $dataRequisition['requiredQuantity'],
                'purchase_order' => $dataRequisition['purchaseOrder']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    /* Actualizar requisicion Auto*/
    public function updateRequisitionAuto($dataRequisition)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE requisitions SET id_material = :id_material, id_provider = :id_provider, application_date = :application_date, 
                                                                  delivery_date = :delivery_date, quantity_required = :quantity_required, purchase_order = :purchase_order
                                    WHERE id_requisition = :id_requisition");
            $stmt->execute([
                'id_requisition' => $dataRequisition['idRequisition'],
                'id_material' => $dataRequisition['idMaterial'],
                'id_provider' => $dataRequisition['idProvider'],
                'application_date' => $dataRequisition['applicationDate'],
                'delivery_date' => $dataRequisition['deliveryDate'],
                'quantity_required' => $dataRequisition['requiredQuantity'],
                'purchase_order' => $dataRequisition['purchaseOrder']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateDateRequisition($dataRequisition)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE requisitions SET admission_date = :admission_date WHERE id_requisition = :id_requisition");
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

    public function saveUserDeliverRequisition($id_company, $id_requisition, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO users_requisitions (id_company, id_requisition, id_user_deliver)
                                          VALUES (:id_company, :id_requisition, :id_user_deliver)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_user' => $id_user,
                'id_requisition' => $id_requisition,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function clearDataRequisition($id_requisition)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE requisitions SET application_date = '0000-00-00', delivery_date = '0000-00-00', quantity_requested = 0, purchase_order = '', id_user_requisition = 0
                                          WHERE id_requisition = :id_requisition");
            $stmt->execute([
                'id_requisition' => $id_requisition,
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteAllRequisitionPending()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("DELETE FROM requisitions 
                                      WHERE application_date = '0000-00-00' AND delivery_date = '0000-00-00' AND purchase_order = ''");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $molds = $stmt->fetch($connection::FETCH_ASSOC);
        return $molds;
    }
}
