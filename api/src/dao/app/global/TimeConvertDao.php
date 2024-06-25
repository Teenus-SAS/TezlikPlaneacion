<?php

namespace TezlikPlaneacion\dao;

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

    public function calculateHourEnd($hourStart, $hoursDay)
    {
        // Separar la parte de la hora y el indicador AM/PM
        list($time, $ampm) = explode(' ', $hourStart);
        list($hours, $minutes) = explode(':', $time);

        // Convertir horas y minutos a enteros
        $hours = (int)$hours;
        $minutes = (int)$minutes;

        // Convertir la hora a formato de 24 horas
        if ($ampm == 'PM' && $hours != 12) {
            $hours += 12;
        } elseif ($ampm == 'AM' && $hours == 12) {
            $hours = 0;
        }

        // Calcular la parte decimal de la hora inicial
        $initialHourDecimal = $hours + ($minutes / 60);

        // Sumar hoursDay
        $finalHourDecimal = $initialHourDecimal + $hoursDay;

        // Obtener horas y minutos finales
        $finalHours = (int)$finalHourDecimal;
        $finalMinutes = round(($finalHourDecimal - $finalHours) * 60);

        // Formatear minutos para asegurarse de que tienen dos dígitos
        $minutes = (int)$finalMinutes;
        $formattedFinalMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        // Formatear la hora final en el formato HH.MM
        $hourEnd = $finalHours . '.' . $formattedFinalMinutes;

        return $hourEnd;
    }
}
