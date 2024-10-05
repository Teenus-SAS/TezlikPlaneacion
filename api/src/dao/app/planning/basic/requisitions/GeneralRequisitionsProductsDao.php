<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralRequisitionsProductsDao
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

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            r.id_requisition_product, 
                                            r.num_order,
                                            r.id_product, 
                                            p.reference, 
                                            p.product AS description, 
                                            r.creation_date, 
                                            r.application_date, 
                                            r.delivery_date, 
                                            r.quantity_requested, 
                                            r.quantity_required, 
                                            r.purchase_order, 
                                            r.admission_date, 
                                            'UND' AS abbreviation, 
                                            IFNULL(r.id_provider, 0) AS id_provider, 
                                            IFNULL(c.client, '') AS provider,
                                            IFNULL(ur.id_user, 0) AS id_user_requisition, 
                                            IFNULL(ur.firstname, '') AS firstname_requisition, 
                                            IFNULL(ur.lastname, '') AS lastname_requisition,
                                            IFNULL(last_user.id_user_deliver, 0) AS id_user_deliver, 
                                            IFNULL(last_user.firstname_deliver, '') AS firstname_deliver, 
                                            IFNULL(last_user.lastname_deliver, '') AS lastname_deliver
                                      FROM requisitions_products r
                                            INNER JOIN products p ON p.id_product = r.id_product
                                            LEFT JOIN third_parties c ON c.id_client = r.id_provider
                                            LEFT JOIN users ur ON ur.id_user = r.id_user_requisition
                                            -- Subconsulta para obtener el último usuario de entrega
                                            LEFT JOIN (
                                                SELECT cur.id_requisition_product, curd.id_user AS id_user_deliver, curd.firstname AS firstname_deliver, curd.lastname AS lastname_deliver
                                                FROM requisitions_products_users cur
                                                INNER JOIN users curd ON curd.id_user = cur.id_user_deliver
                                                WHERE cur.id_user_requisition = (
                                                    SELECT MAX(cur_inner.id_user_requisition)
                                                    FROM requisitions_products_users cur_inner
                                                    WHERE cur_inner.id_requisition_product = cur.id_requisition_product
                                                )
                                            ) AS last_user ON last_user.id_requisition_product = r.id_requisition_product
                                      WHERE r.id_company = :id_company AND (r.admission_date IS NULL OR MONTH(r.admission_date) = MONTH(CURRENT_DATE))
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
        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            r.id_requisition_product, 
                                            r.num_order,
                                            r.id_product, 
                                            p.reference, 
                                            p.product AS description, 
                                            r.creation_date, 
                                            r.application_date, 
                                            r.delivery_date, 
                                            r.quantity_requested, 
                                            r.quantity_required, 
                                            r.purchase_order, 
                                            r.admission_date, 
                                            'UND' AS abbreviation, 
                                            IFNULL(r.id_provider, 0) AS id_provider, 
                                            IFNULL(c.client, '') AS provider,
                                            IFNULL(ur.id_user, 0) AS id_user_requisition, 
                                            IFNULL(ur.firstname, '') AS firstname_requisition, 
                                            IFNULL(ur.lastname, '') AS lastname_requisition,
                                            IFNULL(last_user.id_user_deliver, 0) AS id_user_deliver, 
                                            IFNULL(last_user.firstname_deliver, '') AS firstname_deliver, 
                                            IFNULL(last_user.lastname_deliver, '') AS lastname_deliver
                                      FROM requisitions_products r
                                            INNER JOIN products p ON p.id_product = r.id_product
                                            LEFT JOIN third_parties c ON c.id_client = r.id_provider
                                            LEFT JOIN users ur ON ur.id_user = r.id_user_requisition
                                            -- Subconsulta para obtener el último usuario de entrega
                                            LEFT JOIN (
                                                SELECT cur.id_requisition_product, curd.id_user AS id_user_deliver, curd.firstname AS firstname_deliver, curd.lastname AS lastname_deliver
                                                FROM requisitions_products_users cur
                                                INNER JOIN users curd ON curd.id_user = cur.id_user_deliver
                                                WHERE cur.id_user_requisition = (
                                                    SELECT MAX(cur_inner.id_user_requisition)
                                                    FROM requisitions_products_users cur_inner
                                                    WHERE cur_inner.id_requisition_product = cur.id_requisition_product
                                                )
                                            ) AS last_user ON last_user.id_requisition_product = r.id_requisition_product
                                      WHERE r.id_company = :id_company AND (r.application_date BETWEEN :min_date AND :max_date)
                                      ORDER BY r.admission_date ASC");
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

        $stmt = $connection->prepare("SELECT * FROM requisitions_products
                                      WHERE id_product = :id_product 
                                      AND id_provider = :id_provicer
                                      AND application_date = '0000-00-00'
                                      AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataRequisition['idProduct'],
            'id_provider' => $dataRequisition['idProvider'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $requisition = $stmt->fetch($connection::FETCH_ASSOC);
        return $requisition;
    }

    public function findRequisitionByApplicationDate($id_product)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM requisitions_products WHERE id_product = :id_product AND application_date = '0000-00-00'");
        $stmt->execute([
            'id_product' => $id_product
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
            $stmt = $connection->prepare("INSERT INTO requisitions_products (id_company, num_order, id_product, id_provider, application_date, delivery_date, quantity_required, purchase_order) 
                                          VALUES (:id_company, :num_order, :id_product, :id_provider, :application_date, :delivery_date, :quantity_required, :purchase_order)");
            $stmt->execute([
                'id_company' => $id_company,
                'num_order' => $dataRequisition['numOrder'],
                'id_product' => $dataRequisition['idProduct'],
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
            $stmt = $connection->prepare("UPDATE requisitions_products SET num_order = :num_order, id_product = :id_product, id_provider = :id_provider, application_date = :application_date, 
                                                                  delivery_date = :delivery_date, quantity_required = :quantity_required, purchase_order = :purchase_order
                                    WHERE id_requisition_product = :id_requisition");
            $stmt->execute([
                'id_requisition' => $dataRequisition['idRequisition'],
                'num_order' => $dataRequisition['numOrder'],
                'id_product' => $dataRequisition['idproduct'],
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
            $stmt = $connection->prepare("UPDATE requisitions_products SET admission_date = :admission_date WHERE id_requisition_product = :id_requisition");
            $stmt->execute([
                'id_requisition_product' => $dataRequisition['idRequisition'],
                'admission_date' => $dataRequisition['date'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function clearDataRequisition($id_requisition_product)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE requisitions_products SET application_date = '0000-00-00', delivery_date = '0000-00-00', quantity_requested = 0, purchase_order = '', id_user_requisition = 0
                                          WHERE id_requisition_product = :id_requisition_product");
            $stmt->execute([
                'id_requisition_product' => $id_requisition_product,
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

        $stmt = $connection->prepare("DELETE FROM requisitions_products
                                      WHERE application_date = '0000-00-00' AND delivery_date = '0000-00-00' AND purchase_order = ''");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $molds = $stmt->fetch($connection::FETCH_ASSOC);
        return $molds;
    }
}
