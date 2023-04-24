<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;


class SendMakeEmailDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }


    public function SendEmailCode($code, $user)
    {
        $name = $user['firstname'];

        $msg = "Hola $name\r\n
                Si estas tratando de iniciar sesion en Tezlik. \r\n
                Ingresa el siguiente código para completar el inicio de sesión:\r\n
                <h4>$code</h4>";

        $resp = array('to' => array($user['email']), 'subject' => 'Código De Verificación', 'body' => $msg, 'ccHeader' => null);

        return $resp;
    }

    public function SendEmailPassword($email, $password)
    {
        // the message
        $msg = "Hola,\r\n
            Recientemente solicitó recordar su contraseña por lo que para mayor seguridad creamos una nueva. Para ingresar a Tezlik puede hacerlo con:\r\n
            · Nombre de usuario: $email
            · Contraseña: $password
             
            Las contraseñas generadas a través de nuestra plataforma son muy seguras solo se envían al correo electrónico del contacto de la cuenta.\r\n
            Si le preocupa la seguridad de la cuenta o sospecha que alguien está intentando obtener acceso no autorizado, puede estar 
            seguro que las contraseñas son generadas aleatoriamente, sin embargo, le recomendamos ingresar a la plataforma con la nueva clave y cambiarla por una nueva.\r\n
        
            Saludos,\r\n
        
            Equipo de Soporte Tezlik";

        $resp = array('to' => array($email), 'subject' => 'Nuevo Password', 'body' => $msg, 'ccHeader' => null);
        return $resp;
    }
}
