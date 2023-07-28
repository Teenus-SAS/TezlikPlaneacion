<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ClientsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllClientByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_clients WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $clients = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("clientes", array('clientes' => $clients));
        return $clients;
    }

    public function insertClient($dataClient, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO plan_clients (nit, client, address, phone, city, id_company) 
                                          VALUES (:nit, :client, :addr, :phone, :city, :id_company)");
            $stmt->execute([
                'id_company' => $id_company,
                'nit' => $dataClient['nit'],
                'client' => strtolower(trim($dataClient['client'])),
                'addr' => $dataClient['address'],
                'phone' => $dataClient['phone'],
                'city' => $dataClient['city'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Cliente duplicado. Ingrese una nuevo cliente';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateClient($dataClient)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE plan_clients SET nit = :nit, client = :client, address = :addr, phone = :phone, city = :city
                                          WHERE id_client = :id_client");
            $stmt->execute([
                'nit' => $dataClient['nit'],
                'client' => strtolower(trim($dataClient['client'])),
                'addr' => $dataClient['address'],
                'phone' => $dataClient['phone'],
                'city' => $dataClient['city'],
                'id_client' => $dataClient['idClient']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteClient($id_client)
    {
        $connection = Connection::getInstance()->getconnection();

        try {
            $stmt = $connection->prepare("SELECT * FROM plan_clients WHERE id_client = :id_client");
            $stmt->execute(['id_client' => $id_client]);
            $row = $stmt->rowCount();

            if ($row > 0) {
                $stmt = $connection->prepare("DELETE FROM plan_clients WHERE id_client = :id_client");
                $stmt->execute(['id_client' => $id_client]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
