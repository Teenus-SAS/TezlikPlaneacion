<?php

namespace TezlikPlaneacion\dao;

use DateTime;
use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LicenseCompanyDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findLicenseCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * , CASE WHEN cl.license_end > CURRENT_DATE THEN TIMESTAMPDIFF(DAY, CURRENT_DATE, license_end) ELSE 0 END license_days
                                        FROM companies c
                                        INNER JOIN companies_licenses cl ON cl.id_company = c.id_company
                                      WHERE c.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $dataCompany = $stmt->fetch($connection::FETCH_ASSOC);

        return $dataCompany;
    }

    public function insertLicenseCompanyByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $licenseStart = date('Y-m-d');
            $licenseEnd = date("Y-m-d", strtotime($licenseStart . "+ 30 day"));

            $stmt = $connection->prepare("INSERT INTO companies_licenses (id_company, license_start, quantity_user, license_status)
                                          VALUES (:id_company, :license_start, :quantity_user, :license_status)");
            $stmt->execute([
                'id_company' => $id_company,
                'license_start' => $licenseStart,
                'license_end' => $licenseEnd,
                'quantity_user' => 1,
                'license_status' => 1
            ]);

            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateLicenseCompany($dataLicenseCompany)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("UPDATE companies_licenses SET license_start = :license_start, license_end = :license_end, quantity_user = :quantity_user, license_status = :license_status
                                          WHERE id_company_license = :id_company_license");
            $stmt->execute([
                'id_company_license' => $dataLicenseCompany['idCompanyLicense'],
                'license_start' => $dataLicenseCompany['licenseStart'],
                'license_end' => $dataLicenseCompany['licenseEnd'],
                'quantity_user' => $dataLicenseCompany['quantityUser'],
                'license_status' => 1
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteLicenseCompany($id_company_license)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM companies_licenses WHERE id_company_license = :id_company_license");
        $stmt->execute(['id_company_license' => $id_company_license]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM companies_licenses WHERE id_company_license = :id_company_license");
            $stmt->execute(['id_company_license' => $id_company_license]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
