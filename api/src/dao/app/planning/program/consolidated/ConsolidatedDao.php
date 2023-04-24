<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ConsolidatedDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findConsolidated($dataOrderTypes, $id_company)
    {
        $connection = Connection::getInstance()->getconnection();

        // Otener datos programa consolidado
        $stmt = $connection->prepare("SELECT o.num_order, p.reference, p.quantity, o.id_order_type, ot.order_type, ROUND(IFNULL((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12, 0)) AS average_month,
                                             ROUND(IFNULL(p.quantity/(((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12)/4/7), 0)) AS inventory_days, 0 AS week_minimum_stock, 0 AS produce_ajusted
                                      FROM products p
                                      INNER JOIN plan_orders o ON o.id_product = p.id_product
                                      LEFT JOIN products_price_history pph ON pph.id_product = p.id_product
                                      INNER JOIN plan_orders_types ot ON ot.id_order_type = o.id_order_type
                                      WHERE o.id_company = :id_company;");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $consolidated = $stmt->fetchAll($connection::FETCH_ASSOC);

        // Obtener tipos de pedidos en la base de datos
        for ($i = 0; $i < sizeof($dataOrderTypes); $i++) {
            $stmt = $connection->prepare("SELECT o.num_order, p.reference, p.quantity, IF(o.id_order_type = :id_order_type, 
                                                 (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) AS order_type    
                                          FROM products p
                                          INNER JOIN plan_orders o ON o.id_product = p.id_product
                                          LEFT JOIN products_price_history pph ON pph.id_product = p.id_product        
                                          WHERE o.id_company = :id_company;");
            $stmt->execute([
                'id_order_type' => $dataOrderTypes[$i]['id_order_type'],
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

            $orderTypes = $stmt->fetchAll($connection::FETCH_ASSOC);

            for ($j = 0; $j < sizeof($orderTypes); $j++) {
                $consolidated[$j]["name_order_type-{$i}"] = $dataOrderTypes[$i]['order_type'];
                $consolidated[$j]["order_type-{$i}"] = $orderTypes[$j]['order_type'];
            }
        }
        for ($i = 0; $i < sizeof($consolidated); $i++) {
            $consolidated[$i]['total_orders'] = 0;
            for ($j = 0; $j < sizeof($dataOrderTypes); $j++) {
                $consolidated[$i]['total_orders'] =  $consolidated[$i]['total_orders'] + $consolidated[$i]["order_type-{$j}"];
            }
        }

        return $consolidated;
    }

    public function calcConsolidated($week, $id_company, $dataOrderTypes)
    {
        $connection = Connection::getInstance()->getconnection();

        $orderTypes = "";
        for ($i = 0; $i < sizeof($dataOrderTypes); $i++) {
            $orderType = "IF(o.id_order_type = {$dataOrderTypes[$i]['id_order_type']}, (SELECT COUNT(id_order) FROM plan_orders WHERE id_product = o.id_product), 0) +";
            $orderTypes = $orderType . $orderTypes;
        }

        $orderTypes = substr($orderTypes, 0, -1);

        $stmt = $connection->prepare("SELECT o.num_order, p.reference, ROUND(IFNULL(((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12)/4*:week,0)) AS week_minimum_stock,
                                             ROUND(IFNULL(((pph.january + pph.february + pph.march + pph.april + pph.may + pph.june + pph.july + pph.august + pph.september + pph.october + pph.november + pph.december)/12)/4*:week + (:order_types) - p.quantity, 0)) AS produce_ajusted
                                      FROM products p
                                      INNER JOIN plan_orders o ON o.id_product = p.id_product
                                      LEFT JOIN products_price_history pph ON pph.id_product = p.id_product        
                                      WHERE o.id_company = :id_company;");
        $stmt->execute([
            'order_types' => $orderTypes,
            'week' => $week,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $consolidated = $stmt->fetchAll($connection::FETCH_ASSOC);


        $dataConsolidated = $this->findConsolidated($dataOrderTypes, $id_company);

        for ($i = 0; $i < sizeof($dataConsolidated); $i++) {
            if (
                $dataConsolidated[$i]['num_order'] == $consolidated[$i]['num_order'] && $dataConsolidated[$i]['reference'] == $consolidated[$i]['reference']
            ) {
                $dataConsolidated[$i]['week_minimum_stock'] = $consolidated[$i]['week_minimum_stock'];
                $dataConsolidated[$i]['produce_ajusted'] = $consolidated[$i]['produce_ajusted'];
            }
        }

        return $dataConsolidated;
    }
}
