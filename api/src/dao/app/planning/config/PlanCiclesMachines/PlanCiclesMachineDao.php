<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class PlanCiclesMachineDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllPlanCiclesMachine($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            pcm.id_cicles_machine,
                                            pcm.id_product,
                                            p.reference,
                                            p.product,
                                            pcm.id_process,
                                            IFNULL(pc.process, '') AS process,
                                            pcm.id_machine,
                                            m.machine,
                                            pcm.cicles_hour,
                                            pcm.units_turn,
                                            pcm.units_month,
                                            pcm.route,
                                           -- IFNULL(am.id_alternal_machine, 0) AS id_alternal_machine,
                                            IFNULL(am.id_machine, 0) AS id_alternal_machine,
                                            IFNULL(am.cicles_hour, 0) AS alternal_cicles_hour,
                                            IFNULL(am.units_turn, 0) AS alternal_units_turn,
                                            IFNULL(am.units_month, 0) AS alternal_units_month
                                      FROM machine_cicles pcm
                                        INNER JOIN machines m ON m.id_machine = pcm.id_machine
                                        INNER JOIN products p ON p.id_product = pcm.id_product
                                        LEFT JOIN process pc ON pc.id_process = pcm.id_process
                                        LEFT JOIN alternal_machines am ON am.id_cicles_machine = pcm.id_cicles_machine
                                      WHERE pcm.id_company = :id_company
                                      ORDER BY pcm.route ASC");
        $stmt->execute(['id_company' => $id_company]);
        $planCiclesMachines = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $planCiclesMachines;
    }

    public function addPlanCiclesMachines($dataCiclesMachine, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $ciclesHour = str_replace('.', '', $dataCiclesMachine['ciclesHour']);

        try {
            $stmt = $connection->prepare("INSERT INTO machine_cicles (id_product, id_process, id_machine, id_company, cicles_hour) 
                                          VALUES(:id_product, :id_process, :id_machine, :id_company, :cicles_hour)");
            $stmt->execute([
                'id_product' => $dataCiclesMachine['idProduct'],
                'id_process' => $dataCiclesMachine['idProcess'],
                'id_machine' => $dataCiclesMachine['idMachine'],
                'id_company' => $id_company,
                'cicles_hour' => $ciclesHour
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updatePlanCiclesMachine($dataCiclesMachine)
    {
        $connection = Connection::getInstance()->getConnection();

        $ciclesHour = str_replace('.', '', $dataCiclesMachine['ciclesHour']);

        try {
            $stmt = $connection->prepare("UPDATE machine_cicles SET id_product = :id_product, id_process = :id_process, id_machine = :id_machine, cicles_hour = :cicles_hour 
                                          WHERE id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'id_cicles_machine' => $dataCiclesMachine['idCiclesMachine'],
                'id_product' => $dataCiclesMachine['idProduct'],
                'id_process' => $dataCiclesMachine['idProcess'],
                'id_machine' => $dataCiclesMachine['idMachine'],
                'cicles_hour' => $ciclesHour
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deletePlanCiclesMachine($id_cicles_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM machine_cicles WHERE id_cicles_machine = :id_cicles_machine");
        $stmt->execute(['id_cicles_machine' => $id_cicles_machine]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM machine_cicles WHERE id_cicles_machine = :id_cicles_machine");
            $stmt->execute(['id_cicles_machine' => $id_cicles_machine]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
