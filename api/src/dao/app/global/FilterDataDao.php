<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FilterDataDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function filterArray($array, $callback)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                $result[] = $value;
            }
        }
        return $result;
    }

    public function filterDuplicateArray($array, $key)
    {
        $tempArray = array();
        $result = array();

        foreach ($array as $subArray) {
            $value = $subArray[$key];

            if (!isset($tempArray[$value])) {
                $tempArray[$value] = true;
                $result[] = $subArray;
            }
        }

        return $result;
    }
}
