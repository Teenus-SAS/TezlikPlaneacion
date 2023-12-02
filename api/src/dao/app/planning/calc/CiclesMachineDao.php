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
                                      FROM plan_cicles_machine cm
                                        LEFT JOIN plan_program_machines pm ON pm.id_machine = cm.id_machine
                                      WHERE cm.id_cicles_machine = :id_cicles_machine");
        $stmt->execute([
            'id_cicles_machine' => $id_cicles_machine
        ]);
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }

    public function calcUnitsMonth($id_cicles_machine)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT IFNULL(cm.units_turn * IFNULL((SELECT CASE MONTH(CURDATE())
                                                WHEN 1 THEN january WHEN 2 THEN february WHEN 3 THEN march WHEN 4 THEN april WHEN 5 THEN may
                                                WHEN 6 THEN june WHEN 7 THEN july WHEN 8 THEN august WHEN 9 THEN september WHEN 11 THEN november
                                                WHEN 12 THEN december END AS current_month_value FROM plan_program_machines WHERE id_machine = cm.id_machine), 0), 0) AS units_month
                                      FROM plan_cicles_machine cm
                                      WHERE cm.id_cicles_machine = :id_cicles_machine");
        $stmt->execute([
            'id_cicles_machine' => $id_cicles_machine
        ]);
        $machine = $stmt->fetch($connection::FETCH_ASSOC);
        return $machine;
    }
}
