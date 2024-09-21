<?php

namespace TezlikPlaneacion\dao;

use DateInterval;
use DateTime;
use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class TimeConvertDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    // Convertir tiempo
    public function timeConverter($dataPMachines)
    {
        $dataPMachines['year'] = date('Y');
        $dataPMachines['hourStart'] = date("G.i", strtotime($dataPMachines['hourStart']));
        $dataPMachines['hourEnd'] = date("G.i", strtotime($dataPMachines['hourEnd']));

        return $dataPMachines;
    }

    public function calculateHourEnd($hourStart, $workShift, $hoursDay)
    {
        try {
            // Convertir la hora de inicio a formato 24 horas para manejarla con DateTime
            $startDateTime = DateTime::createFromFormat('g:i A', $hourStart);

            // Calcular la cantidad total de horas (hoursDay * workShift)
            $totalHours = $hoursDay * $workShift;

            // Crear un objeto DateInterval para sumar las horas
            $interval = new DateInterval('PT' . $totalHours . 'H');

            // Sumar las horas al objeto DateTime
            $startDateTime->add($interval);

            // Formatear la hora final en formato de 12 horas con AM/PM
            $hourEnd = $startDateTime->format('g:i A');

            return $hourEnd;
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }
}
