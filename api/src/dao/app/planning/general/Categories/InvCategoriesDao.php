<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class invCategoriesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCategories()
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM plan_categories");
        $stmt->execute();

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $categories = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("categories", array('categories' => $categories));
        return $categories;
    }

    public function insertCategory($dataCategory)
    {
        $connection = Connection::getInstance()->getConnection();

        if ($dataCategory['typeCategory'] == 1) $dataCategory['typeCategory'] = 'Inventario';
        if ($dataCategory['typeCategory'] == 2) $dataCategory['typeCategory'] = 'Producto';

        try {
            $stmt = $connection->prepare("INSERT INTO plan_categories (category, type_category) VALUES (:category, :type_category)");
            $stmt->execute([
                'category' => strtoupper(trim($dataCategory['category'])),
                'type_category' => $dataCategory['typeCategory']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Categoría duplicada. Ingrese una nueva categoría';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateCategory($dataCategory)
    {
        $connection = Connection::getInstance()->getConnection();

        if ($dataCategory['typeCategory'] == 1) $dataCategory['typeCategory'] = 'Inventario';
        if ($dataCategory['typeCategory'] == 2) $dataCategory['typeCategory'] = 'Producto';

        try {
            $stmt = $connection->prepare("UPDATE plan_categories SET category = :category, type_category = :type_category 
                                          WHERE id_category = :id_category");
            $stmt->execute([
                'category' => strtoupper(trim($dataCategory['category'])),
                'type_category' => $dataCategory['typeCategory'],
                'id_category' => $dataCategory['idCategory'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteCategory($id_category)
    {
        $connection = Connection::getInstance()->getConnection();
        try {
            $stmt = $connection->prepare("SELECT * FROM plan_categories WHERE id_category = :id_category");
            $stmt->execute(['id_category' => $id_category]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM plan_categories WHERE id_category = :id_category");
                $stmt->execute(['id_category' => $id_category]);
            }
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getCode() == 23000)
                $message = 'Categoria asociada a un material o producto. Imposible Eliminar';
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
