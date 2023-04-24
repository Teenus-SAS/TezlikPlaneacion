<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SendEmailDao extends PHPMailer
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function sendEmail($dataEmail, $email, $name)
    {
        require_once dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/env.php';

        try {
            $mail = new PHPMailer(true);

            //Server settings
            $mail->isSMTP();
            //$mail->SMTPDebug     = 1;
            $mail->Host          = $_ENV["smtpHost"];
            $mail->SMTPAuth      = true;
            $mail->Username     = $_ENV["smtpEmail"];
            $mail->Password     = $_ENV["smtpPass"];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            //$mail->SMTPSecure = "tls";
            $mail->Port          = $_ENV["smtpPort"];

            //Recipients
            $mail->setFrom($email, $name, '');
            $mail->FromName = $name;

            // Destinatario
            foreach ($dataEmail['to'] as $key => $value) {
                $mail->addAddress($value);
            }

            // if ($dataEmail['user'] != null)
            //     $mail->addAddress($dataEmail['user']);

            // Content
            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $dataEmail['subject'];
            $mail->Body    = sprintf($dataEmail['body']);

            // Asunto del correo
            if ($dataEmail['ccHeader'] != null)
                $mail->addCC($dataEmail['ccHeader']);

            // Texto alternativo
            //$mail->mailHeader = $dataEmail['header'];
            $mail->send();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
