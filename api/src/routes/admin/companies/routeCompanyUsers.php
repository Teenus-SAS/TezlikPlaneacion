<?php

use TezlikPlaneacion\dao\CompanyUsers;

$companyUsers = new CompanyUsers();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Obtener usuarios * empresa
$app->get('/companyUsers/{idCompany}', function (Request $request, Response $response, $args) use ($companyUsers) {
    $resp = $companyUsers->findCompanyUsers($args['idCompany']);
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


//Actualizar estado de usuarios * empresa
$app->post('/updateCompanyUsersStatus/{id_user}', function (Request $request, Response $response, $args) use ($companyUsers) {
    $sts = $companyUsers->userStatus($args['id_user']);
    $status = $sts['active'];

    if ($status == 1) {
        $licStatus = $companyUsers->updateCompanyUsersStatus(0, $args['id_user']);

        if ($licStatus == null) {
            $resp = array('success' => true, 'message' => 'Inactivo');
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');
        }
    }

    if ($status == 0) {
        $licStatus = $companyUsers->updateCompanyUsersStatus(1, $args['id_user']);

        if ($licStatus == null) {
            $resp = array('success' => true, 'message' => 'Activo');
        } else {
            $resp = array('error' => true, 'message' => 'Ocurrio un error. Intente nuevamente');
        }
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


// //Nueva Empresa
// $app->post('/addNewCompany', function (Request $request, Response $response, $args) use ($companyUsers) {
//     $dataCompany = $request->getParsedBody();
//     /*Agregar datos a companies */
//     $idcompany = $companiesDao->addCompany($dataCompany);
//     /*Agregar datos a companies licenses*/
//     $company = $companiesLicDao->addLicense($dataCompany, $idcompany['idCompany']);

//     if ($company == null) {
//         $resp = array('success' => true, 'message' => 'Datos de Empresa agregados correctamente');
//     } else {
//         $resp = array('error' => true, 'message' => 'Ocurrio un error al actualizar la licencia. Intente nuevamente');
//     }

//     $response->getBody()->write(json_encode($resp));
//     return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
// });
