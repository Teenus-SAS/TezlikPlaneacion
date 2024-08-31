<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralSellersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findSeller($dataSeller, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM sellers WHERE email = :email AND id_company = :id_company");
        $stmt->execute([
            'email' => trim($dataSeller['email']),
            'id_company' => $id_company,
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $seller = $stmt->fetch($connection::FETCH_ASSOC);
        return $seller;
    }

    public function findInternalSeller($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM sellers WHERE status = 1 AND id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $seller = $stmt->fetch($connection::FETCH_ASSOC);
        return $seller;
    }

    public function changeStatusSellerByCompany($id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE sellers SET status = :status WHERE id_company = :id_company");
            $stmt->execute([
                'id_company' => $id_company,
                'status' => 0
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function changeStatusSeller($id_seller, $status)
    {
        try {
            $connection = Connection::getInstance()->getConnection();
            $stmt = $connection->prepare("UPDATE sellers SET status = :status WHERE id_seller = :id_seller");
            $stmt->execute([
                'id_seller' => $id_seller,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
