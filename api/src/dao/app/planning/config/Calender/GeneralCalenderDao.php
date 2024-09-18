<?php

namespace TezlikPlaneacion\dao;

use DateTime;
use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralCalenderDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findCalender($dataCalender, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT * FROM machine_calender
                                      WHERE id_company = :id_company AND month = :month AND day = :day");
        $stmt->execute([
            'id_company' => $id_company,
            'month' => $dataCalender['month'],
            'day' => $dataCalender['day']
        ]);

        $calender = $stmt->fetch($connection::FETCH_ASSOC);
        return $calender;
    }

    public function getAllDaysActualMonth()
    {
        // Obtener el mes y año actual
        $year = date('Y');
        $month = date('m');

        // Obtener el número de días en el mes actual
        $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $daysOfMonth = [];

        // Recorrer cada día del mes
        for ($day = 1; $day <= $numDays; $day++) {
            // Crear una fecha con el día actual
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

            // Obtener el día de la semana (1 para Lunes, 7 para Domingo)
            $dayOfWeek = date('N', strtotime($date));
            $formatedMonth = sprintf('%02d', $month);
            $formatedDay = sprintf('%02d', $day);
            $dDate = $year . '-' . $formatedMonth . '-' . $formatedDay;
            $festive = 0;
            $festiveDate = $this->checkFestive($dDate);

            $festiveDate == true ? $festive = 1 : $festive;

            $job = 1;
            $dayOfWeek == 6 || $dayOfWeek == 7 || $festiveDate == true ? $job = 0 : $job;

            // Almacenar el día y el día de la semana en el array
            $daysOfMonth[] = [
                'day' => $day,
                'month' => $month,
                'workingDay' => $job,
                'festive' => $festive
            ];
        }

        return $daysOfMonth;
    }
    public function checkFestive($date)
    {
        $festives = [
            '2024-01-01', // Año Nuevo
            '2024-01-08', // Día de los Reyes Magos (trasladado)
            '2024-03-25', // Lunes Santo
            '2024-03-28', // Jueves Santo
            '2024-03-29', // Viernes Santo
            '2024-05-01', // Día del Trabajo
            '2024-05-13', // Ascensión del Señor (trasladado)
            '2024-06-03', // Corpus Christi (trasladado)
            '2024-06-10', // Sagrado Corazón (trasladado)
            '2024-07-01', // San Pedro y San Pablo (trasladado)
            '2024-07-20', // Día de la Independencia
            '2024-08-07', // Batalla de Boyacá
            '2024-08-19', // La Asunción de la Virgen (trasladado)
            '2024-10-14', // Día de la Raza (trasladado)
            '2024-11-04', // Todos los Santos (trasladado)
            '2024-11-11', // Independencia de Cartagena (trasladado)
            '2024-12-08', // Inmaculada Concepción
            '2024-12-25', // Navidad
        ];

        return in_array($date, $festives);
    }
}
