<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CompanyDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findDataCompanyByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM admin_companies WHERE id_company = :id_company;");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $company = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("company", array('company' => $company));
    return $company;
  }

  public function insertCompany($dataCompany)
  {
    $connection = Connection::getInstance()->getConnection();
    try {
      $stmt = $connection->prepare("INSERT INTO admin_companies (company, state, city, country, address, 
                                                telephone, nit, logo, created_at, creador)
                                    VALUES (:company, :state, :city, :country, :address, 
                                                :telephone, :nit, :logo, :created_at, :creador)");
      $stmt->execute([

        'company' => ucfirst(strtolower(trim($dataCompany['company']))),
        'state' => ucfirst(strtolower(trim($dataCompany['state']))),
        'country' => ucfirst(strtolower(trim($dataCompany['country']))),
        'city' => ucfirst(strtolower(trim($dataCompany['city']))),
        'address' => ucfirst(strtolower(trim($dataCompany['address']))),
        'telephone' => trim($dataCompany['telephone']),
        'nit' => trim($dataCompany['nit']),
        'logo' => trim($dataCompany['logo']),
        'created_at' => $dataCompany['createAt'],
        'creador' => $dataCompany['creador']

      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

      $stmt = $connection->prepare("SELECT MAX(id_company) AS id_company FROM admin_companies");
      $stmt->execute();
      $id_company = $stmt->fetch($connection::FETCH_ASSOC);
      return $id_company;
    } catch (\Exception $e) {
      $message = $e->getMessage();
      if ($e->getCode() == 23000)
        $message = 'Nit duplicado. Ingrese una nuevo Nit';

      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function updateCompany($dataCompany)
  {
    $connection = Connection::getInstance()->getConnection();
    try {
      session_start();
      $id_company = $_SESSION['id_company'];

      $stmt = $connection->prepare("UPDATE admin_companies SET company = :company, state = :state, city = :city, country = :country, address = :address, 
                                                  telephone = :telephone, nit = :nit, logo = :logo, created_at = :created_at, creador =:creador
                                    WHERE id_company = :id_company");
      $stmt->execute([

        'company' => ucfirst(strtolower(trim($dataCompany['company']))),
        'state' => ucfirst(strtolower(trim($dataCompany['state']))),
        'country' => ucfirst(strtolower(trim($dataCompany['country']))),
        'city' => ucfirst(strtolower(trim($dataCompany['city']))),
        'address' => ucfirst(strtolower(trim($dataCompany['address']))),
        'telephone' => trim($dataCompany['telephone']),
        'nit' => trim($dataCompany['nit']),
        'logo' => trim($dataCompany['logo']),
        'created_at' => $dataCompany['createAt'],
        'creador' => $dataCompany['creador'],
        'id_company' => $id_company

      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT * FROM admin_companies WHERE id_company = :id_company");
    $stmt->execute(['id_company' => $id_company]);
    $rows = $stmt->rowCount();

    if ($rows > 0) {
      $stmt = $connection->prepare("DELETE FROM admin_companies WHERE id_company = :id_company");
      $stmt->execute(['id_company' => $id_company]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
  }
}
