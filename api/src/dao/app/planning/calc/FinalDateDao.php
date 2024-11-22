<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FinalDateDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcFinalDateAndHourByProgramming($id_programming)
    {
        $connection = Connection::getInstance()->getConnection();

        /* 
            $stmt = $connection->prepare("SELECT DATE_ADD(dm.start_dat, INTERVAL((:quantity * (pp.enlistment_time + pp.operation_time))/60) HOUR) AS final_date 
                                        FROM products p 
                                        INNER JOIN products_process pp ON pp.id_product = pp.id_product 
                                        INNER JOIN dates_machines dm ON dm.id_machine = pp.id_machine 
                                        WHERE dm.id_product = :id_product AND dm.id_machine = :id_machine AND dm.id_company = :id_company 
                                        AND p.id_product IN (SELECT id_product FROM products WHERE id_product = :id_product);");
            $stmt->execute([
                'quantity' => $dataMachine['quantity'],
                'id_product' => $dataMachine['idProduct'],
                'id_machine' => $dataMachine['idMachine'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            $finalDate = $stmt->fetch($connection::FETCH_ASSOC);
            return $finalDate; 
        */
        try {
            $stmt = $connection->prepare("SELECT 
            IFNULL(((IFNULL((IFNULL(o.original_quantity, 0) / IFNULL(cm.cicles_hour, 0)), 0)) - (FLOOR(IFNULL((IFNULL(o.original_quantity, 0) / IFNULL(cm.cicles_hour, 0)) / IFNULL(pm.hours_day, 0), 0)) * IFNULL(pm.hours_day, 0)) + IF((SELECT id_programming FROM programming WHERE id_machine = pg.id_machine AND id_programming <= pg.id_programming LIMIT 1) = pg.id_programming, 0, (SELECT HOUR(pg.max_date) FROM programming WHERE id_machine = pg.id_machine AND id_programming < pg.id_programming LIMIT 1))), 0) AS final_hour,
            DATE_ADD(pg.min_date, INTERVAL IFNULL((IFNULL(o.original_quantity, 0) / IFNULL(cm.cicles_hour, 0)) / IFNULL(pm.hours_day, 0), 0) DAY) AS final_date, 
            IF((SELECT id_programming FROM programming WHERE id_machine = pg.id_machine AND id_programming <= pg.id_programming LIMIT 1) = pg.id_programming, HOUR(pg.max_date), (SELECT HOUR(pg.max_date) FROM programming WHERE id_machine = pg.id_machine AND id_programming < pg.id_programming LIMIT 1)) AS last_hour
                                          FROM programming pg
                                            LEFT JOIN orders o ON o.id_order = pg.id_order
                                            LEFT JOIN machine_programs pm ON pm.id_machine = pg.id_machine
                                            LEFT JOIN machine_cicles cm ON cm.id_product = pg.id_product AND cm.id_machine = pg.id_machine
                                          WHERE pg.id_programming = :id_programming");
            $stmt->execute(['id_programming' => $id_programming]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            $programming = $stmt->fetch($connection::FETCH_ASSOC);

            return $programming;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
