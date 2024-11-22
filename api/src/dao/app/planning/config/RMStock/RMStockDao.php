<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class RMStockDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllStockByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT
                                            s.id_stock_material,
                                            s.id_material,
                                            m.reference,
                                            m.material,
                                            IFNULL(mg.id_magnitude, 0) AS id_magnitude,
                                            IFNULL(mg.magnitude, '') AS magnitude,
                                            IFNULL(u.id_unit, 0) AS id_unit,
                                            IFNULL(u.unit, '') AS unit,
                                            IFNULL(u.abbreviation, '') AS abbreviation,
                                            IFNULL(s.id_provider, 0) AS id_provider,
                                            IFNULL(c.id_client, '') AS id_client,
                                            IFNULL(c.client, '') AS client,
                                            m.unit,
                                            mi.quantity,
                                            s.min_term,
                                            s.max_term,
                                            s.min_quantity,
                                            (s.max_term - s.min_term) AS average
                                      FROM inv_stock_materials s
                                          INNER JOIN materials m ON m.id_material = s.id_material
                                          INNER JOIN inv_materials mi ON mi.id_material = s.id_material
                                          LEFT JOIN admin_units u ON u.id_unit = m.unit
                                          LEFT JOIN admin_magnitudes mg ON mg.id_magnitude = u.id_magnitude
                                          LEFT JOIN third_parties c ON c.id_client = s.id_provider
                                      WHERE s.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $stock = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("stock", array('stock' => $stock));
        return $stock;
    }

    public function insertStockByCompany($dataStock, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO inv_stock_materials (id_company, id_material, id_provider, min_term, max_term, min_quantity) 
                                          VALUES (:id_company, :id_material, :id_provider, :min_term, :max_term, :min_quantity)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_material' => $dataStock['idMaterial'],
                'id_provider' => $dataStock['idProvider'],
                'min_term' => $dataStock['min'],
                'max_term' => $dataStock['max'],
                'min_quantity' => $dataStock['quantity']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateStock($dataStock)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE inv_stock_materials SET id_material = :id_material, id_provider = :id_provider, min_term = :min_term, max_term = :max_term, min_quantity = :min_quantity 
                                          WHERE id_stock_material = :id_stock_material");
            $stmt->execute([
                'id_stock_material' => $dataStock['idStock'],
                'id_material' => $dataStock['idMaterial'],
                'id_provider' => $dataStock['idProvider'],
                'min_term' => $dataStock['min'],
                'max_term' => $dataStock['max'],
                'min_quantity' => $dataStock['quantity']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    // public function deletestock($id_stock)
    // {
    //     $connection = Connection::getInstance()->getConnection();
    //     try {
    //         $stmt = $connection->prepare("SELECT * FROM stock WHERE id_stock = :id_stock");
    //         $stmt->execute(['id_stock' => $id_stock]);
    //         $rows = $stmt->rowCount();

    //         if ($rows > 0) {
    //             $stmt = $connection->prepare("DELETE FROM stock WHERE id_stock = :id_stock");
    //             $stmt->execute(['id_stock' => $id_stock]);
    //             $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    //         }
    //     } catch (\Exception $e) {
    //         $message = $e->getMessage();

    //         if ($e->getCode() == 23000)
    //             $message = 'Proceso asociado a un producto/nomina. Imposible Eliminar';

    //         $error = array('info' => true, 'message' => $message);
    //         return $error;
    //     }
    // }
}
