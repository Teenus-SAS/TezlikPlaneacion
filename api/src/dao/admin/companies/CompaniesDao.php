<?php

namespace TezlikPlaneacion\dao;

use DateTime;
use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CompaniesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    public function findAllCompanies()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM admin_companies");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $companyData = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("AllCompanies", array('AllCompanies' => $companyData));

        return $companyData;
    }
    //Obtener todas las empresas activas / inactivas
    public function findAllCompaniesLicenses($stat)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT cp.id_company, cp.company, cp.state, cp.city, cp.country, cp.address,
                                      cp.telephone, cp.nit, cp.logo, cp.created_at FROM admin_companies cp 
                                      INNER JOIN admin_companies_licenses cl ON cp.id_company = cl.id_company 
                                      WHERE cl.license_status = :stat");
        $stmt->execute(['stat' => $stat]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $companyData = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("AllCompanies", array('AllCompanies' => $companyData));

        return $companyData;
    }

    //Agregar Empresa
    public function addCompany($dataCompany)
    {
        $connection = Connection::getInstance()->getConnection();
        try {

            $stmt = $connection->prepare("INSERT INTO admin_companies (company, state, city, country, address, telephone, nit)
                                          VALUES (:company, :state, :city, :country, :address, :telephone, :nit)");
            $stmt->execute([
                'company' => $dataCompany['company'],              'state' => $dataCompany['companyState'],
                'city' => $dataCompany['companyCity'],             'country' => $dataCompany['companyCountry'],
                'address' => $dataCompany['companyAddress'],       'telephone' => $dataCompany['companyTel'],
                'nit' => $dataCompany['companyNIT']
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    //Actualizar Empresa
    public function updateCompany($dataCompany)
    {
        $connection = Connection::getInstance()->getConnection();
        try {

            $stmt = $connection->prepare("UPDATE admin_companies SET company = :company, state = :state, city = :city,
                                          country = :country, address = :address, telephone = :telephone, nit = :nit
                                          WHERE id_company = :id_company");
            $stmt->execute([
                'company' => $dataCompany['company'],              'state' => $dataCompany['companyState'],
                'city' => $dataCompany['companyCity'],             'country' => $dataCompany['companyCountry'],
                'address' => $dataCompany['companyAddress'],       'telephone' => $dataCompany['companyTel'],
                'nit' => $dataCompany['companyNIT'],               'id_company' => $dataCompany['idCompany']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
