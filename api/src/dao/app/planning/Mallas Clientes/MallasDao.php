<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Slim\Psr7\Message;

class MallasDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findMalla($dataMalla)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM plan_malla_clientes WHERE id_cliente = :id_cliente");
        $stmt->execute([
            'id_cliente' => $dataMalla['idClient']
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $malla = $stmt->fetch($connection::FETCH_ASSOC);
        return $malla;
    }

    public function insertMallaCliente($dataMalla)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO plan_malla_clientes (id_cliente, dia_entrega) VALUES (:id_cliente, :dia_entrega)");
            $stmt->execute([
                'id_cliente' => $dataMalla['idClient'],
                'dia_entrega' => $dataMalla['deliveryDay']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateMallaCliente($dataMalla)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE plan_malla_clientes SET dia_entrega = :dia_entrega
                                          WHERE id = :id");
            $stmt->execute([
                'id' => $dataMalla['idMalla'],
                'dia_entrega' => $dataMalla['deliveryDay']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteMallaCliente($id)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM plan_malla_clientes WHERE id_mold = :id_mold");
        $stmt->execute(['id' => $id]);
        $row = $stmt->rowCount();

        if ($row > 0) {
            $stmt = $connection->prepare("DELETE FROM plan_malla_clientes WHERE id_mold = :id_mold");
            $stmt->execute(['id' => $id]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
