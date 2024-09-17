<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MachinesDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllMachinesByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT m.id_machine, m.machine, pcm.cicles_hour, COUNT(DISTINCT py.id_plan_payroll) AS employees
                                  FROM machines m
                                   LEFT JOIN machine_cicles pcm ON pcm.id_machine = m.id_machine 
                                   LEFT JOIN plan_payroll py ON py.id_machine = m.id_machine AND py.status = 1
                                  WHERE m.id_company = :id_company
                                  GROUP BY m.id_machine;");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

    $machines = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("machines", array('machines' => $machines));
    return $machines;
  }

  /* Insertar maquina */
  public function insertMachinesByCompany($dataMachine, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("INSERT INTO machines (id_company ,machine) 
                                    VALUES (:id_company ,:machine)");
      $stmt->execute([
        'id_company' => $id_company,
        'machine' => strtoupper(trim($dataMachine['machine']))
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'Maquina duplicada. Ingrese una nueva maquina';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar maquina */
  public function updateMachine($dataMachine)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("UPDATE machines SET machine = :machine WHERE id_machine = :id_machine");
      $stmt->execute([
        'id_machine' => $dataMachine['idMachine'],
        'machine' => strtoupper(trim($dataMachine['machine']))
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteMachine($id_machine)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("SELECT * FROM machines WHERE id_machine = :id_machine");
      $stmt->execute(['id_machine' => $id_machine]);
      $rows = $stmt->rowCount();

      if ($rows > 0) {
        $stmt = $connection->prepare("DELETE FROM machines WHERE id_machine = :id_machine");
        $stmt->execute(['id_machine' => $id_machine]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
      }
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'Maquina asociada a un proceso. No es posible eliminar';

      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
