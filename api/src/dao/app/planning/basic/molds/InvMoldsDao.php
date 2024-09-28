<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class InvMoldsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllInvMold($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM inv_molds WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $molds = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("Moldes", array('Moldes' => $molds));
        return $molds;
    }

    public function findInvMold($dataMold, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM inv_molds 
                                      WHERE reference = :reference AND mold = :mold AND id_company = :id_company");
        $stmt->execute([
            'reference' => $dataMold['referenceMold'],
            'mold' => strtoupper(trim($dataMold['mold'])),
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $molds = $stmt->fetch($connection::FETCH_ASSOC);
        return $molds;
    }

    public function insertInvMoldByCompany($dataMold, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO inv_molds 
                                            (
                                                reference, 
                                                mold, 
                                                id_company, 
                                                cavity_total,
                                                cavity_available,
                                                blows_total,
                                                available,
                                                cicle_hour,
                                                status
                                            )
                                          VALUES 
                                            (
                                                :reference, 
                                                :mold, 
                                                :id_company, 
                                                :cavity_total,
                                                :cavity_available,
                                                :blows_total,
                                                :available,
                                                :cicle_hour,
                                                :status
                                            )");
            $stmt->execute([
                'reference' => $dataMold['referenceMold'],
                'mold' => strtoupper(trim($dataMold['mold'])),
                'id_company' => $id_company,
                'cavity_total' => $dataMold['cavityTotal'],
                'cavity_available' => $dataMold['cavityAvailable'],
                'blows_total' => $dataMold['blowsTotal'],
                'available' => $dataMold['available'],
                'cicle_hour' => $dataMold['cicleHour'],
                'status' => 1
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Molde duplicado. Ingrese una nuevo molde';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateInvMold($dataMold)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE inv_molds SET 
                                            reference = :reference,
                                            mold = :mold, 
                                            cavity_total = :cavity_total,
                                            cavity_available = :cavity_available,
                                            blows_total = :blows_total,
                                            available = :available,
                                            cicle_hour = :cicle_hour
                                          WHERE id_mold = :id_mold");
            $stmt->execute([
                'id_mold' => $dataMold['idMold'],
                'reference' => $dataMold['referenceMold'],
                'mold' => strtoupper(trim($dataMold['mold'])),
                'cavity_total' => $dataMold['cavityTotal'],
                'cavity_available' => $dataMold['cavityAvailable'],
                'blows_total' => $dataMold['blowsTotal'],
                'available' => $dataMold['available'],
                'cicle_hour' => $dataMold['cicleHour'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteInvMold($id_mold)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM inv_molds WHERE id_mold = :id_mold");
            $stmt->execute(['id_mold' => $id_mold]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM inv_molds WHERE id_mold = :id_mold");
                $stmt->execute(['id_mold' => $id_mold]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Molde asociado a un producto. Imposible Eliminar';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
