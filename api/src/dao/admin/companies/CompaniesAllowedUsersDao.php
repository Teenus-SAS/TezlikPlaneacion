<?php

namespace TezlikPlaneacion\dao;

use DateTime;
use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CompaniesAllowedUsersDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    //Obtener cantidad de usuarios permitidos por empresas activas
    public function usersAllowed()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT cp.company, cl.quantity_user FROM companies cp 
                                      INNER JOIN companies_licenses cl ON cp.id_company = cl.id_company
                                      WHERE cl.license_status = 0");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $allowedData = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("licenses get", array('licenses' => $allowedData));

        return $allowedData;
    }


    //ACTUALIZAR CANTIDAD DE USUARIOS PERMITIDOS POR EMPRESA
    public function updateUsersAllowed($quantity_user, $id_company_license)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE companies_licenses SET quantity_user = :quantity_user
                                          WHERE id_company_license = :id_company_license");
            $stmt->execute([
                'id_company_license' => $id_company_license,
                'quantity_user' => $quantity_user
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
