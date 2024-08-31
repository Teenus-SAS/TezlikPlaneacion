<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class AreaDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllAreasByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM plan_areas
                                      WHERE id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);

        $areas = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $areas;
    }

    public function insertAreaByCompany($dataArea, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO plan_areas (id_company, area) 
                                          VALUES (:id_company, :area)");
            $stmt->execute([
                'id_company' => $id_company,
                'area' => strtoupper(trim($dataArea['area'])),
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateArea($dataArea)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE plan_areas SET area = :area
                                          WHERE id_plan_area = :id_plan_area");
            $stmt->execute([
                'id_plan_area' => $dataArea['idArea'],
                'area' => strtoupper(trim($dataArea['area'])),
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deleteArea($id_plan_area)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("SELECT * FROM plan_areas WHERE id_plan_area = :id_plan_area");
            $stmt->execute(['id_plan_area' => $id_plan_area]);
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                $stmt = $connection->prepare("DELETE FROM plan_areas WHERE id_plan_area = :id_plan_area");
                $stmt->execute(['id_plan_area' => $id_plan_area]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($e->getCode() == 23000)
                $message = 'Area asociada a un proceso';

            return ['info' => true, 'message' => $message];
        }
    }
}
