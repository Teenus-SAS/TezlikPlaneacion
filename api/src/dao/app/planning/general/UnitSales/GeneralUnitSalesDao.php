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

    // public function findAllProductsUnitSalesByCompany($id_company)
    // {
    //     $connection = Connection::getInstance()->getConnection();
    //     $stmt = $connection->prepare("SELECT p.id_product FROM products p
    //                                   INNER JOIN sales_by_units u ON u.id_product = p.id_product 
    //                                   WHERE p.id_company = :id_company");
    //     $stmt->execute(['id_company' => $id_company]);

    //     $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    //     $products = $stmt->fetchAll($connection::FETCH_ASSOC);
    //     $this->logger->notice("products", array('products' => $products));
    //     return $products;
    // }

    public function findSales($dataSale, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_unit_sales FROM sales_by_units
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

        $stmt = $connection->prepare("SELECT *, 'false' AS new FROM sales_days WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $findSales = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $findSales;
    }

    public function findAllSaleDays()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT n AS id_sale_day, 0 days, n AS month, YEAR(CURDATE()) AS year, 'true' AS new
                                      FROM (SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL 
                                            SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL 
                                            SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL 
                                            SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) AS numbers");
        $stmt->execute();
        $findSales = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $findSales;
    }

    public function findSaleDays($dataSale, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM sales_days WHERE year = :year AND month = :month AND id_company = :id_company");
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

            $stmt = $connection->prepare("INSERT INTO sales_days (id_company, days, month, year) VALUES (:id_company, :days, :month, :year)");
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

            $stmt = $connection->prepare("UPDATE sales_days SET days = :days, month = :month, year = :year WHERE id_sale_day = :id_sale_day");
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

    public function deleteSaleByProduct($id_product)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM sales_by_units WHERE id_product = :id_product");
            $stmt->execute(['id_product' => $id_product]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM sales_by_units WHERE id_product = :id_product");
                $stmt->execute(['id_product' => $id_product]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
