<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CiclesMachineDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcUnitsTurn($id_cicles_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT (cm.cicles_hour * IFNULL(pm.hours_day, 0)) AS units_turn
                                      FROM machine_cicles cm
                                        LEFT JOIN machine_programs pm ON pm.id_machine = cm.id_machine
                                      WHERE cm.id_cicles_machine = :id_cicles_machine");
        $stmt->execute([
            'id_cicles_machine' => $id_cicles_machine
        ]);
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }

    public function calcUnitsMonth($data, $op)
    {
        $connection = Connection::getInstance()->getConnection();

        if ($op == 1) {
            $stmt = $connection->prepare("SELECT
                                            IFNULL
                                            (
                                                cm.units_turn * 
                                                IFNULL
                                                (
                                                    (
                                                        SELECT 
                                                        CASE MONTH(CURDATE())
                                                            WHEN 1 THEN january
                                                            WHEN 2 THEN february
                                                            WHEN 3 THEN march
                                                            WHEN 4 THEN april
                                                            WHEN 5 THEN may
                                                            WHEN 6 THEN june
                                                            WHEN 7 THEN july
                                                            WHEN 8 THEN august
                                                            WHEN 9 THEN september
                                                            WHEN 10 THEN october 
                                                            WHEN 11 THEN november
                                                            WHEN 12 THEN december
                                                        END AS current_month_value
                                                        FROM machine_programs
                                                        WHERE id_machine = cm.id_machine
                                                    ), 0
                                                ), 0
                                            ) AS units_month
                                      FROM machine_cicles cm
                                      WHERE cm.id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'id_cicles_machine' => $data['idCiclesMachine']
            ]);
        } else {
            $stmt = $connection->prepare("SELECT
                                            IFNULL
                                            (
                                                :units_turn * 
                                                IFNULL
                                                (
                                                    (
                                                        SELECT 
                                                        CASE MONTH(CURDATE())
                                                            WHEN 1 THEN january
                                                            WHEN 2 THEN february
                                                            WHEN 3 THEN march
                                                            WHEN 4 THEN april
                                                            WHEN 5 THEN may
                                                            WHEN 6 THEN june
                                                            WHEN 7 THEN july
                                                            WHEN 8 THEN august
                                                            WHEN 9 THEN september
                                                            WHEN 10 THEN october 
                                                            WHEN 11 THEN november
                                                            WHEN 12 THEN december
                                                        END AS current_month_value
                                                        FROM machine_programs
                                                        WHERE id_machine = cm.id_machine
                                                    ), 0
                                                ), 0
                                            ) AS units_month
                                         FROM machine_cicles cm
                                         WHERE cm.id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'id_cicles_machine' => $data['idCiclesMachine'],
                'units_turn' => $data['units_turn']
            ]);
        }
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }

    public function calcUnitsTurnAlternal($id_cicles_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT (am.cicles_hour * IFNULL(pm.hours_day, 0)) AS units_turn
                                      FROM alternal_machines am
                                        LEFT JOIN machine_programs pm ON pm.id_machine = am.id_machine
                                      WHERE am.id_cicles_machine = :id_cicles_machine");
        $stmt->execute([
            'id_cicles_machine' => $id_cicles_machine
        ]);
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }

    public function calcUnitsMonthAlternal($data, $op)
    {
        $connection = Connection::getInstance()->getConnection();

        if ($op == 1) {
            $stmt = $connection->prepare("SELECT 
                                            IFNULL
                                            (
                                                am.units_turn * 
                                                IFNULL
                                                (
                                                    (
                                                        SELECT 
                                                        CASE MONTH(CURDATE())
                                                            WHEN 1 THEN january
                                                            WHEN 2 THEN february
                                                            WHEN 3 THEN march
                                                            WHEN 4 THEN april
                                                            WHEN 5 THEN may
                                                            WHEN 6 THEN june
                                                            WHEN 7 THEN july
                                                            WHEN 8 THEN august
                                                            WHEN 9 THEN september
                                                            WHEN 10 THEN october 
                                                            WHEN 11 THEN november
                                                            WHEN 12 THEN december
                                                        END AS current_month_value
                                                        FROM machine_programs
                                                        WHERE id_machine = am.id_machine
                                                    ), 0
                                                ), 0
                                            ) AS units_month
                                          FROM alternal_machines am
                                          WHERE am.id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'id_cicles_machine' => $data['idCiclesMachine']
            ]);
        } else {
            $stmt = $connection->prepare("SELECT 
                                            IFNULL
                                            (
                                                :units_turn * 
                                                IFNULL
                                                (
                                                    (
                                                        SELECT 
                                                        CASE MONTH(CURDATE())
                                                            WHEN 1 THEN january
                                                            WHEN 2 THEN february
                                                            WHEN 3 THEN march
                                                            WHEN 4 THEN april
                                                            WHEN 5 THEN may
                                                            WHEN 6 THEN june
                                                            WHEN 7 THEN july
                                                            WHEN 8 THEN august
                                                            WHEN 9 THEN september
                                                            WHEN 10 THEN october 
                                                            WHEN 11 THEN november
                                                            WHEN 12 THEN december
                                                        END AS current_month_value
                                                        FROM machine_programs
                                                        WHERE id_machine = am.id_machine
                                                    ), 0
                                                ), 0
                                            ) AS units_month
                                          FROM alternal_machines am
                                          WHERE am.id_cicles_machine = :id_cicles_machine");
            $stmt->execute([
                'id_cicles_machine' => $data['idCiclesMachine'],
                'units_turn' => $data['units_turn']
            ]);
        }
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }
}
