<?php

use TezlikPlaneacion\dao\passUserDao;
use TezlikPlaneacion\dao\SendMakeEmailDao;
use TezlikPlaneacion\dao\SendEmailDao;

$passUserDao = new passUserDao();
$sendMakeEmailDao = new SendMakeEmailDao();
$sendEmailDao = new SendEmailDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Change Password */

$app->post('/changePassword', function (Request $request, Response $response, $args) use ($passUserDao) {
    session_start();

    if (isset($_SESSION['idUser'])) {
        $id = $_SESSION['idUser'];

        $parsedBody = $request->getParsedBody();
        $usersChangePassword = $passUserDao->ChangePasswordUser($id, $parsedBody["inputNewPass"]);

        if ($usersChangePassword == null)
            $resp = array('success' => true, 'message' => 'Cambio de Password correcto');
        else
            $resp = array('error' => true, 'message' => 'Hubo un problema, intente nuevamente');
    } else
        $resp = array('error' => true, 'message' => 'Usuario no autorizado');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

/* Forgot Password */

$app->post('/forgotPassword', function (Request $request, Response $response, $args) use (
    $passUserDao,
    $sendEmailDao,
    $sendMakeEmailDao
) {
    $parsedBody = $request->getParsedBody();
    $email = trim($parsedBody["data"]);

    $passwordTemp = $passUserDao->forgotPasswordUser($email);

    if ($passwordTemp == null)
        $resp = array('success' => true, 'message' => 'La contraseña fue enviada al email suministrado exitosamente');
    else {
        $dataEmail = $sendMakeEmailDao->SendEmailPassword($email, $passwordTemp);
        $email =  $sendEmailDao->SendEmail($dataEmail, 'soporteTezlik@tezliksoftware.com.co', 'SoporteTezlik');

        if ($email == null)
            $resp = array('success' => true, 'message' => "La contraseña fue enviada al email suministrado exitosamente.");
        else if (isset($email['info']))
            $resp = array('info' => true, 'message' => $email['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras enviaba la información. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
