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
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz!#%&/()=?¡*][_:;,.><';
        $codeLength = 12;
        $code = '';

        for ($i = 0; $i < $codeLength; $i++) {
            $pos = random_int(0, strlen($string) - 1);
            $code .= $string[$pos];
        }

        return $code;
    }
}
