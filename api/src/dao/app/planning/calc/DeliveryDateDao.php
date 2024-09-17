<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DeliveryDateDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcDeliveryDate($dataOrder)
    {
        $connection = Connection::getInstance()->getConnection();

        for ($i = 1; $i < 8; $i++) {
            $stmt = $connection->prepare("SELECT IF(ELT(WEEKDAY(DATE_SUB(o.max_date, INTERVAL {$i} DAY)) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo') = mc.dia_entrega, 
                                                DATE_SUB(o.max_date, INTERVAL {$i} DAY), 0) AS delivery_date
                                            FROM orders o
                                            INNER JOIN plan_inv_molds mc ON mc.id_cliente = o.id_client
                                            WHERE o.id_order = :id_order;");
            $stmt->execute([
                'id_order' => $dataOrder['idOrder'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            $deliveryDate = $stmt->fetch($connection::FETCH_ASSOC);
            if ($deliveryDate['delivery_date'] != 0) break;
        }
        $this->updateDeliveryDate($dataOrder, $deliveryDate['delivery_date']);
    }

    public function updateDeliveryDate($dataOrder, $deliveryDate)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE orders SET delivery_date = :delivery_date WHERE id_order = :id_order");
            $stmt->execute([
                'id_order' => $dataOrder['idOrder'],
                'delivery_date' => $deliveryDate
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
