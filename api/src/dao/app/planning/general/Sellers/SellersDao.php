<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class SellersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllSellersByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM sellers
                                      WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);

        $sellers = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $sellers;
    }

    public function insertSellerByCompany($dataSeller, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO sellers (id_company, firstname, lastname, email, status)
                                          VALUES (:id_company, :firstname, :lastname, :email, :status)");
            $stmt->execute([
                'id_company' => $id_company,
                'firstname' => strtoupper(trim($dataSeller['firstname'])),
                'lastname' => strtoupper(trim($dataSeller['lastname'])),
                'email' => $dataSeller['email'],
                'status' => 1
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateSeller($dataSeller)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE sellers SET firstname = :firstname, lastname = :lastname, email = :email
                                          WHERE id_seller = :id_seller");
            $stmt->execute([
                'id_seller' => $dataSeller['idSeller'],
                'firstname' => strtoupper(trim($dataSeller['firstname'])),
                'lastname' => strtoupper(trim($dataSeller['lastname'])),
                'email' => $dataSeller['email'],
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deleteSeller($id_seller)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM sellers WHERE id_seller = :id_seller");
            $stmt->execute(['id_seller' => $id_seller]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM sellers WHERE id_seller = :id_seller");
                $stmt->execute(['id_seller' => $id_seller]);
            }
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
