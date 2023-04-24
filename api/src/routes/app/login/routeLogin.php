<?php

use TezlikPlaneacion\dao\AutenticationUserDao;
use TezlikPlaneacion\dao\GeneralUserAccessDao;
use TezlikPlaneacion\dao\LicenseCompanyDao;
use TezlikPlaneacion\dao\StatusActiveUserDao;
use TezlikPlaneacion\dao\GenerateCodeDao;
use TezlikPlaneacion\dao\SendEmailDao;
use TezlikPlaneacion\dao\LastLoginDao;

$licenseDao = new LicenseCompanyDao();
$autenticationDao = new AutenticationUserDao();
$statusActiveUserDao = new StatusActiveUserDao();
$generateCodeDao = new GenerateCodeDao();
$sendEmailDao = new SendEmailDao();
$lastLoginDao = new LastLoginDao();
$userAccessDao = new GeneralUserAccessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Autenticación */

$app->post('/userAutentication', function (Request $request, Response $response, $args) use ($autenticationDao, $licenseDao, $statusActiveUserDao, $lastLoginDao, $userAccessDao) {
    $parsedBody = $request->getParsedBody();

    $user = $parsedBody["validation-email"];
    $password = $parsedBody["validation-password"];
    $user = $autenticationDao->findByEmail($user);

    $resp = array();

    /* Usuario sn datos */
    if ($user == null) {
        $resp = array('error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    /* Valida el password del usuario */
    if (!password_verify($password, $user['password'])) {
        $resp = array('error' => true, 'message' => 'Usuario y/o password incorrectos, valide nuevamente');
        $response->getBody()->write(json_encode($resp));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    if (empty($user['rol'])) {

        /* valide licenciamiento empresa */

        $dataCompany = $licenseDao->findLicenseCompany($user['id_company']);

        $today = date('Y-m-d');
        $licenseDay = $dataCompany['license_end'];
        $today < $licenseDay ? $license = 1 : $license = 0;

        if ($license == 0) {
            $resp = array('error' => true, 'message' => 'Su licencia ha caducado, lo invitamos a comunicarse');
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        /* Validar que el usuario es activo */

        if ($user['active'] != 1) {
            $resp = array('error' => true, 'message' => 'Usuario Inactivo, valide con el administrador');
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        /* Valida la session del usuario */

        if ($user['session_active'] != 0) {
            $resp = array('error' => true, 'message' => 'Usuario logeado, cierre la sesión para abrir una nueva');
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        /* Nueva session user */
        session_start();
        $_SESSION['active'] = true;
        $_SESSION['idUser'] = $user['id_user'];
        $_SESSION['case'] = 1;
        $_SESSION['name'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['rol'] = $user["id_rols"];
        $_SESSION['id_company'] = $user['id_company'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION["time"] = microtime(true);
        $_SESSION['plan'] = $dataCompany['plan'];

        // Guardar accesos de usario 
        $userAccessDao->setGeneralAccess($user['id_user']);

        // Validar licencia 
        $location = '../../planning/';
    } else {
        /* Nueva session admin*/
        session_start();
        $_SESSION['active'] = true;
        $_SESSION['idUser'] = $user['id_admin'];
        $_SESSION['case'] = 2;
        $_SESSION['name'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION["time"] = microtime(true);

        $location = '../../admin/';
    }

    /* Actualizar metodo ultimo logueo */
    $lastLoginDao->findLastLogin();

    /* Genera codigo */
    //$code = $generateCodeDao->GenerateCode();
    //$_SESSION["code"] = $code;

    /* Envio el codigo por email */
    //$sendEmailDao->SendEmailCode($code, $user);

    /* Modificar el estado de la sesion del usuario en BD */
    $statusActiveUserDao->changeStatusUserLogin();

    $resp = array('success' => true, 'message' => 'Ingresar código', 'location' => $location);
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

/* Logout */
$app->get('/logout', function (Request $request, Response $response, $args) use ($statusActiveUserDao) {
    session_start();
    $statusActiveUserDao->changeStatusUserLogin();
    session_destroy();
    $response->getBody()->write(json_encode("1", JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
