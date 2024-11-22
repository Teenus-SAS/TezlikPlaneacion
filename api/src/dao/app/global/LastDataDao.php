<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LastDataDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Login */
    public function findLastLogins()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT cp.company, us.firstname, us.lastname, us.last_login 
                                      FROM users us INNER JOIN admin_companies cp ON cp.id_company = us.id_company
                                      WHERE us.session_active = 1 ORDER BY us.last_login DESC");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $lastLogs = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("Last Login", array('Last Login' => $lastLogs));

        return $lastLogs;
    }

    /* Compañia */
    public function findLastCompany()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_company) AS idCompany FROM admin_companies");
        $stmt->execute();
        $lastId = $stmt->fetch($connection::FETCH_ASSOC);

        return $lastId;
    }

    /* Usuario */
    public function findLastInsertedUser($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_user) AS idUser FROM users u WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $user = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        return $user;
    }

    /* Productos */
    public function lastInsertedProductId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_product) AS id_product FROM products WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $id_product = $query->fetch($connection::FETCH_ASSOC);
        return $id_product;
    }
    /* Material */
    public function lastInsertedMaterialId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_material) AS id_material FROM materials WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $material = $query->fetch($connection::FETCH_ASSOC);
        return $material;
    }
    /* Maquinas */
    public function lastInsertedMachineId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_machine) AS id_machine FROM machines WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $material = $query->fetch($connection::FETCH_ASSOC);
        return $material;
    }

    /* Nomina */
    public function lastInsertedPayrollId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_plan_payroll) AS id_plan_payroll FROM payroll WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $payroll = $query->fetch($connection::FETCH_ASSOC);
        return $payroll;
    }

    /* Requisicion */
    public function lastInsertedRequisitionId($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $sql = "SELECT MAX(id_requisition_material) AS id_requisition_material FROM requisitions_materials WHERE id_company = :id_company";
        $query = $connection->prepare($sql);
        $query->execute(['id_company' => $id_company]);
        $material = $query->fetch($connection::FETCH_ASSOC);
        return $material;
    }

    public function findLastInsertedClient()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_client) AS id_client FROM third_parties");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $client = $stmt->fetch($connection::FETCH_ASSOC);
        return $client;
    }

    public function findLastInsertedSeller()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_seller) AS id_seller FROM sellers");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $seller = $stmt->fetch($connection::FETCH_ASSOC);
        return $seller;
    }

    public function findLastInsertedProgramming($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_programming) AS id_programming FROM programming WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findLastInsertedCiclesMachine($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_cicles_machine) AS id_cicles_machine FROM machine_cicles WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetch($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findLastInsertedOrder($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_order) AS id_order FROM orders WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $order = $stmt->fetch($connection::FETCH_ASSOC);
        return $order;
    }

    public function findLastInsertedPMachine($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT MAX(id_program_machine) AS id_program_machine FROM machine_programs WHERE id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }
}
