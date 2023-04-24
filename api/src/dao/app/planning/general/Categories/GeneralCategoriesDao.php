<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCategoriesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllCategoriesByTypeCategories($typeCategory)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM plan_categories WHERE type_category = :type_category");
        $stmt->execute([
            'type_category' => strtoupper(trim($typeCategory))
        ]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $categories = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("categories", array('categories' => $categories));
        return $categories;
    }

    public function findCategory($dataCategory)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT id_category FROM plan_categories WHERE category = :category");
        $stmt->execute([
            'category' => strtoupper(trim($dataCategory['category']))
        ]);
        $findCategory = $stmt->fetch($connection::FETCH_ASSOC);
        return $findCategory;
    }
}
