<?php

use TezlikPlaneacion\dao\AutenticationUserDao;
use TezlikPlaneacion\dao\FilesDao;
use TezlikPlaneacion\dao\ProfileDao;

$profileDao = new ProfileDao();
$FilesDao = new FilesDao();
$usersDao = new AutenticationUserDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/updateProfile', function (Request $request, Response $response, $args) use ($profileDao, $FilesDao, $usersDao) {
    session_start();
    $dataUser = $request->getParsedBody();

    if ($dataUser['admin'] == 1) {
        $profile = $profileDao->updateProfileAdmin($dataUser);
        if (sizeof($_FILES) > 0) $FilesDao->avatarUserAdmin($dataUser['idUser']);
    } else {
        $id_company = $_SESSION['id_company'];
        $profile = $profileDao->updateProfile($dataUser);
        if (sizeof($_FILES) > 0) {
            if (isset($_FILES['avatar']))
                $FilesDao->avatarUser($dataUser['idUser'], $id_company);
            if (isset($_FILES['logo']))
                $FilesDao->logoCompany($id_company);
        }
    }

    if ($profile == null) {
        $user = $usersDao->findByEmail($dataUser['emailUser']);

        $_SESSION['name'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['avatar'] = $user['avatar'];

        $resp = array('success' => true, 'message' => 'Perfil actualizado correctamente', 'avatar' => $user['avatar']);
    } else if (isset($profile['info']))
        $resp = array('info' => true, 'message' => $profile['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
