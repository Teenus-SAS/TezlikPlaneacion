<?php

use TezlikPlaneacion\dao\CompanyDao;
use TezlikPlaneacion\dao\UsersDao;
use TezlikPlaneacion\dao\licenseCompanyDao;

$companyDao = new CompanyDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/company', function (Request $request, Response $response, $args) use ($companyDao) {
    session_start();
    $id_company = $_SESSION['id_company'];
    $company = $companyDao->findDataCompanyByCompany($id_company);
    $response->getBody()->write(json_encode($company, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/addCompany', function (Request $request, Response $response, $args) use ($companyDao) {
    $data = $request->getParsedBody();

    if (
        empty($data['company']) && empty($data['state']) &&
        empty($data['city']) && empty($data['country']) && empty($data['address']) &&
        empty($data['telephone']) && empty($data['nit']) && empty($data['creador'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese los todos datos');
    else {

        $userDao = new UsersDao();
        $licenseCompanyDao = new LicenseCompanyDao();

        $id_company = $companyDao->insertCompany($data);
        $userDao->saveUser($data, $id_company);
        $licenseCompany = $licenseCompanyDao->insertLicenseCompanyByCompany($data, $id_company);

        if ($licenseCompany == null)
            $resp = array('success' => true, 'message' => 'Compañia ingresada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Error al crear Empresa. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateCompany', function (Request $request, Response $response, $args) use ($companyDao) {
    $dataCompany = $request->getParsedBody();

    if (
        empty($dataCompany['nameCommercial']) && empty($dataCompany['company']) && empty($dataCompany['state']) &&
        empty($dataCompany['city']) && empty($dataCompany['country']) && empty($dataCompany['address']) &&
        empty($dataCompany['telephone']) && empty($dataCompany['nit']) && empty($dataCompany['creador'])
    )
        $resp = array('error' => true, 'message' => 'Ingrese todos los datos a actualizar');
    else {
        $company = $companyDao->updateCompany($dataCompany);
        if ($company == null)
            $resp = array('success' => true, 'message' => 'Compañia actualizada correctamente');
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras actualizaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteCompany/{id_company}', function (Request $request, Response $response, $args) use ($companyDao) {
    $company = $companyDao->deleteCompany($args['id_company']);

    if ($company == null)
        $resp = array('success' => true, 'message' => 'Compañia eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la compañia, existe información asociada a ella');
    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
