<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralClientsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findClient($dataClient, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_clients WHERE nit = :nit AND id_company = :id_company -- AND type_client = :type_client");
        $stmt->execute([
            'nit' => trim($dataClient['nit']),
            'id_company' => $id_company,
            //'type_client' => $type
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $client = $stmt->fetch($connection::FETCH_ASSOC);
        return $client;
    }

    public function findClientsByNitAndName($dataClient)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_clients 
                                      WHERE nit = :nit OR client = :client");
        $stmt->execute([
            'nit' => trim($dataClient['nit']),
            'client' => strtoupper(trim($dataClient['client'])),
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $client = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $client;
    }

    public function findClientByName($dataClient, $id_company, $type)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_clients WHERE client = :client AND id_company = :id_company AND type_client = :type_client");
        $stmt->execute([
            'client' => strtoupper(trim($dataClient['client'])),
            'id_company' => $id_company,
            'type_client' => $type
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $client = $stmt->fetch($connection::FETCH_ASSOC);
        return $client;
    }

    public function findInternalClient($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_clients WHERE status = 1 AND id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $client = $stmt->fetch($connection::FETCH_ASSOC);
        return $client;
    }

    public function changeStatusClientByCompany($id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE plan_clients SET status = :status WHERE id_company = :id_company");
            $stmt->execute([
                'id_company' => $id_company,
                'status' => 0
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function changeStatusClient($id_client, $status)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE plan_clients SET status = :status WHERE id_client = :id_client");
            $stmt->execute([
                'id_client' => $id_client,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function changeTypeClient($id_client, $type)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE plan_clients SET type_client = :type_client WHERE id_client = :id_client");
            $stmt->execute([
                'id_client' => $id_client,
                'type_client' => $type
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
