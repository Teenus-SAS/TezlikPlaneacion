<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductionOrderDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }
    public function findAllProductionOrder($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                            -- Programación
                                                pg.id_programming, 
                                                pg.num_production, 
                                                pg.quantity AS quantity_programming,
                                                pg.flag_cancel,
                                                pg.min_date AS min_date_programming, 
                                                HOUR(pg.min_date) AS min_hour, 
                                                pg.max_date AS max_date_programming, 
                                                HOUR(pg.max_date) AS max_hour,
                                            -- Pedido
                                                o.id_order, 
                                                o.num_order, 
                                                o.date_order, 
                                                o.min_date AS min_date_order, 
                                                o.max_date AS max_date_order, 
                                                o.original_quantity AS quantity_order, 
                                                o.accumulated_quantity, 
                                                ps.status,
                                            -- Producto
                                                p.id_product, 
                                                p.reference, 
                                                p.product, 
                                                IFNULL(pms.width, 0) AS width, 
                                                IFNULL(pms.high, 0) AS high, 
                                                IFNULL(pms.length, 0) AS length, 
                                                IFNULL(pms.useful_length, 0) AS useful_length, 
                                                IFNULL(pms.total_width, 0) AS total_width, 
                                                IFNULL(pms.window, 0) AS window,
                                            -- Maquinas
                                                m.id_machine, 
                                                m.machine, 
                                                pm.hour_start,
                                            -- Procesos
                                                pc.id_process, 
                                                pc.process, 
                                            -- Cliente
                                                c.client, 
                                                c.img        
                                      FROM programming pg
                                        INNER JOIN plan_orders o ON o.id_order = pg.id_order
                                        INNER JOIN products p ON p.id_product = pg.id_product
                                        LEFT JOIN products_measures pms ON pms.id_product = pg.id_product
                                        INNER JOIN machines m ON m.id_machine = pg.id_machine
                                        INNER JOIN plan_clients c ON c.id_client = o.id_client
                                        INNER JOIN plan_program_machines pm ON pm.id_machine = pg.id_machine
                                        INNER JOIN plan_cicles_machine pcm ON pcm.id_product = pg.id_product AND pcm.id_machine = pg.id_machine
                                        INNER JOIN process pc ON pc.id_process = pcm.id_process
                                        INNER JOIN plan_status ps ON ps.id_status = o.status
                                      WHERE pg.status = 1 AND pg.id_company = :id_company
                                        ORDER BY pg.id_programming ASC");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }
}
