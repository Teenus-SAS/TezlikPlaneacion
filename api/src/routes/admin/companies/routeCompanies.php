<?php

use TezlikPlaneacion\dao\CompaniesDao;
use TezlikPlaneacion\dao\CompaniesLicenseDao;
use TezlikPlaneacion\dao\FilesDao;
use TezlikPlaneacion\dao\LastDataDao;

$companiesDao = new CompaniesDao();
$FilesDao = new FilesDao();
$lastDataDao = new LastDataDao();
$companiesLicDao = new CompaniesLicenseDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/allCompanies', function (Request $request, Response $response, $args) use ($companiesDao) {
    $resp = $companiesDao->findAllCompanies();
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

//Datos de empresas activas
$app->get('/companies/{stat}', function (Request $request, Response $response, $args) use ($companiesDao) {
    $resp = $companiesDao->findAllCompaniesLicenses($args['stat']);
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

//Nueva Empresa
$app->post('/addNewCompany', function (Request $request, Response $response, $args) use ($companiesDao, $lastDataDao, $FilesDao, $companiesLicDao) {
    $dataCompany = $request->getParsedBody();
    /*Agregar datos a companies */
    $company = $companiesDao->addCompany($dataCompany);

    $lastId = $lastDataDao->findLastCompany();
    if (sizeof($_FILES) > 0) {
        $FilesDao->logoCompany($lastId['idCompany']);
    }
    /*Agregar datos a companies licenses */
    $company = $companiesLicDao->addLicense($dataCompany, $lastId['idCompany']);


    if ($company == null)
        $resp = array('success' => true, 'message' => 'Datos de Empresa agregados correctamente');
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error al ingresar la informacion. Intente nuevamente');


    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});


//Actualizar Empresa
$app->post('/updateDataCompany', function (Request $request, Response $response, $args) use ($companiesDao, $FilesDao) {
    $dataCompany = $request->getParsedBody();
    $company = $companiesDao->updateCompany($dataCompany);

    if (sizeof($_FILES) > 0)
        $FilesDao->logoCompany($dataCompany['idCompany']);

    if ($company == null) {
        $resp = array('success' => true, 'message' => 'Datos de Empresa actualizados correctamente');
    } else {
        $resp = array('error' => true, 'message' => 'Ocurrio un error al actualizar la licencia. Intente nuevamente');
    }

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});
