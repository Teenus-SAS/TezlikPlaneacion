<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GenerateCodeDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function GenerateCode()
    {
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz!#$%&/()=?¡*][_:;,.><";
        $longString = strlen($string);
        $code = "";
        $longCode = 12;

        for ($i = 1; $i <= $longCode; $i++) {
            $pos = rand(0, $longString - 1);
            $code .= substr($string, $pos, 1);
        }
        return $code;
    }
}
