<?php

use TezlikPlaneacion\dao\CalenderDao;
use TezlikPlaneacion\dao\GeneralCalenderDao;

$calenderDao = new CalenderDao();
$generalCalenderDao = new GeneralCalenderDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/calender', function (Request $request, Response $response, $args) use ($calenderDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $calender = $calenderDao->findAllCalenderByMonth($id_company);
    $response->getBody()->write(json_encode($calender, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/saveCalenderAuto', function (Request $request, Response $response, $args) use (
    $calenderDao,
    $generalCalenderDao
) {
    session_start();
    $id_company = $_SESSION['id_company'];

    // Obtener Dias del mes actual, con festivos o domingos
    $calender = $generalCalenderDao->getAllDaysActualMonth();

    for ($i = 0; $i < sizeof($calender); $i++) {

        $findCalender = $generalCalenderDao->findCalender($calender[$i], $id_company);

        if (!$findCalender) {
            $resolution = $calenderDao->addDaysMonth($calender[$i], $id_company);
        } else {
            $calender[$i]['idCalender'] = $findCalender['id_calender'];
            $resolution = $calenderDao->updateDaysMonth($calender[$i]);
        }

        if (isset($resolution['info'])) break;
    }

    if ($resolution == null) {
        $resp = ['success' => true, 'message' => 'Calendario del mes actual guardado correctamente'];
    } elseif (isset($resolution['info'])) {
        $resp = ['info' => true, 'message' => $resolution['message']];
    } else {
        $resp = ['error' => true, 'message' => 'Ocurrio un error al guardar la información. Intente nuevamente'];
    }

    $response->getBody()->write(json_encode($resp, JSON_NUMERIC_CHECK));
    return $response->withHeader('Content-Type', 'application/json');
});
