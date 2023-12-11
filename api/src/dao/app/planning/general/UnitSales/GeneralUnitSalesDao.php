<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralUnitSalesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsUnitSalesByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product FROM products p
                                      INNER JOIN plan_unit_sales u ON u.id_product = p.id_product 
                                      WHERE p.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $products));
        return $products;
    }

    public function findSales($dataSale, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_unit_sales FROM plan_unit_sales
                                  WHERE id_product = :id_product AND id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataSale['idProduct'],
            'id_company' => $id_company
        ]);
        $findSales = $stmt->fetch($connection::FETCH_ASSOC);
        return $findSales;
    }

    public function findSaleDaysByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM sale_days WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $findSales = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $findSales;
    }

    public function findSaleDays($dataSale, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM sale_days WHERE year = :year AND month = :month AND id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company,
            'year' => $dataSale['year'],
            'month' => $dataSale['month']
        ]);
        $findSales = $stmt->fetch($connection::FETCH_ASSOC);
        return $findSales;
    }

    public function insertSaleDaysByCompany($dataSale, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO sale_days (id_company, days, month, year) VALUES (:id_company, :days, :month, :year)");
            $stmt->execute([
                'id_company' => $id_company,
                'days' => $dataSale['days'],
                'month' => $dataSale['month'],
                'year' => $dataSale['year']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }

    public function updateSaleDays($dataSale)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE sale_days SET days = :days, month = :month, year = :year WHERE id_sale_day = :id_sale_day");
            $stmt->execute([
                'id_sale_day' => $dataSale['idSaleDay'],
                'days' => $dataSale['days'],
                'month' => $dataSale['month'],
                'year' => $dataSale['year']
            ]);
        } catch (\Exception $e) {
            return array('info' => true, 'message' => $e->getMessage());
        }
    }
}
