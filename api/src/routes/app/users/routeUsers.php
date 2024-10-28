<?php

use TezlikPlaneacion\dao\UsersDao;
// Cantidad de usuarios
use TezlikPlaneacion\dao\QuantityUsersDao;
use TezlikPlaneacion\dao\GenerateCodeDao;
use TezlikPlaneacion\dao\UserAccessDao;
use TezlikPlaneacion\dao\SendEmailDao;
use TezlikPlaneacion\dao\SendMakeEmailDao;

$userDao = new UsersDao();
$generateCodeDao = new GenerateCodeDao();
$makeEmailDao = new SendMakeEmailDao();
$sendEmailDao = new SendEmailDao();
$quantityUsersDao = new QuantityUsersDao();
$planningAccessUserDao = new UserAccessDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/* Consulta todos */

$app->get('/users', function (Request $request, Response $response, $args) use ($userDao) {
    session_start();
    $company = $_SESSION['id_company'];
    $users = $userDao->findAllusersByCompany($company);
    $response->getBody()->write(json_encode($users, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/user', function (Request $request, Response $response, $args) use ($userDao) {
    session_start();
    $email = $_SESSION['email'];
    $users = $userDao->findUser($email);
    $response->getBody()->write(json_encode($users, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

/* Insertar usuario */

$app->post('/addUser', function (Request $request, Response $response, $args) use (
    $userDao,
    $generateCodeDao,
    $makeEmailDao,
    $sendEmailDao,
    $quantityUsersDao,
    $planningAccessUserDao
) {
    session_start();
    //data
    $dataUser = $request->getParsedBody();

    !isset($_SESSION['id_company']) ? $id_company = $dataUser['company'] : $id_company = $_SESSION['id_company'];

    //selecciona quantity_user de admin_companies_licenses que tengan el id_company
    $quantityAllowsUsers = $quantityUsersDao->quantityUsersAllows($id_company);

    //obtener cantidad de usuarios creados con el id_company
    $quantityCreatedUsers = $quantityUsersDao->quantityUsersCreated($id_company);


    if ($quantityAllowsUsers['quantity_user'] <= $quantityCreatedUsers['quantity_users'])
        $resp = array('error' => true, 'message' => 'Cantidad de usuarios maxima alcanzada');
    else {
        if (empty($dataUser['nameUser']) && empty($dataUser['lastnameUser']) && empty($dataUser['emailUser'])) {
            $resp = array('error' => true, 'message' => 'Complete todos los datos');
            exit();
        }

        $users = $userDao->findUser($dataUser['emailUser']);

        if ($users == false) {
            $email = $_SESSION['email'];
            $name = $_SESSION['name'];

            $newPass = $generateCodeDao->GenerateCode();

            // Se envia email con usuario(email) y contraseña
            // $dataEmail = $makeEmailDao->SendEmailPassword($dataUser['emailUser'], $newPass);

            // $sendEmail = $sendEmailDao->sendEmail($dataEmail, $email, $name);

            // if ($sendEmail == null) {
            $pass = password_hash($newPass, PASSWORD_DEFAULT);

            /* Almacena el usuario */
            $users = $userDao->saveUser($dataUser, $pass, $id_company);
            // }

            if ($users == null) {
                $user = $userDao->findUser($dataUser['emailUser']);
                $dataUser['idUser'] = $user['id_user'];

                /* Almacena los accesos 
                if (isset($dataUser['factoryLoad']) && isset($dataUser['programsMachine'])) {
                    $usersAccess = $costAccessUserDao->insertUserAccessByUser($dataUser);
                    $usersAccess = $planningAccessUserDao->insertUserAccessByUser($dataUser);
                } else if (isset($dataUser['factoryLoad'])) $usersAccess = $costAccessUserDao->insertUserAccessByUser($dataUser);
                else if (isset($dataUser['programsMachine'])) */
                $usersAccess = $planningAccessUserDao->insertUserAccessByUser($dataUser);
            }
        } else $users = 1;


        if ($users == 1) {
            $resp = array('error' => true, 'message' => 'El email ya se encuentra registrado. Intente con uno nuevo');
        } elseif ($users == null && $usersAccess == null) {
            $resp = array('success' => true, 'message' => 'Usuario creado correctamente');
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras almacenaba la información. Intente nuevamente');
        }
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateUser', function (Request $request, Response $response, $args) use ($userDao, $planningAccessUserDao) {
    session_start();
    $dataUser = $request->getParsedBody();

    !isset($_SESSION['id_company']) ? $id_company = $dataUser['company'] : $id_company = $_SESSION['id_company'];

    $files = $request->getUploadedFiles();

    if (empty($dataUser['nameUser']) && empty($dataUser['lastnameUser'])) {
        $resp = array('error' => true, 'message' => 'Ingrese sus Nombres y Apellidos completos');
    } else {
        $users = $userDao->updateUser($dataUser, null);
        $usersAccess = $planningAccessUserDao->insertUserAccessByUser($dataUser);
        /*if (empty($dataUser['avatar'])) {
            // Actualizar los accesos 
            if (isset($dataUser['factoryLoad'])) $usersAccess = $costAccessUserDao->insertUserAccessByUser($dataUser, $id_company);
            if (isset($dataUser['programsMachine'])) 
            !isset($usersAccess) ? $usersAccess = null : $usersAccess; 
        } else {
            foreach ($files as $file) {
                $name = $file->getClientFilename();
                $name = explode(".", $name);
                $ext = array_pop($name);
                $ext = strtolower($ext);
                if (empty($ext)) {
                    $path = null;
                } else {
                    if (!in_array($ext, ["jpeg", "jpg", "png"])) {
                        $resp = array('error' => true, 'message' => 'La imagen cargada no es valida');
                        $response->getBody()->write(json_encode($resp));
                        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
                    } else {
                        $file->moveTo("../app/assets/images/avatars/" . $name[0] . '.' . $ext);
                        $path = "../../../app/assets/images/avatars/" . $name[0] . '.' . $ext;
                        $users = $userDao->updateUser($dataUser, $path);
                        // Actualizar los accesos
                        if (isset($dataUser['factoryLoad'])) $usersAccess = $costAccessUserDao->updateUserAccessByUsers($dataUser);
                        if (isset($dataUser['programsMachine'])) $usersAccess = $planningAccessUserDao->updateUserAccessByUsers($dataUser);
                        // Creacion carpeta de la img
                        $path = "../../../app/assets/images/avatars/44";
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                    }
                }
            }
        } */
    }
    if ($users == null && $usersAccess == null)
        $resp = array('success' => true, 'message' => 'Usuario actualizado correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error, Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/deleteUser', function (Request $request, Response $response, $args) use ($userDao, $planningAccessUserDao) {
    $dataUser = $request->getParsedBody();
    session_start();
    $idUser = $_SESSION['idUser'];

    if ($dataUser['idUser'] != $idUser) {
        $usersAccess = $planningAccessUserDao->deleteUserAccess($dataUser);
        /* if (isset($dataUser['factoryLoad'])) $usersAccess = $costAccessUserDao->deleteUserAccess($dataUser);
        if (isset($dataUser['programsMachine'])) 
        !isset($usersAccess) ? $usersAccess = null : $usersAccess; */

        if ($usersAccess == null)
            $users = $userDao->deleteUser($dataUser);

        if ($users == null && $usersAccess == null)
            $resp = array('success' => true, 'message' => 'Usuario eliminado correctamente');
        else if (isset($users['info']))
            $resp = array('info' => true, 'message' => $users['message']);
        else
            $resp = array('error' => true, 'message' => 'No fue posible eliminar el usuario, Intente nuevamente');
    } else {
        $resp = array('error' => true, 'message' => 'No es posible eliminar este usuario');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
