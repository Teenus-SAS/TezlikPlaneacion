<?php

use TezlikPlaneacion\dao\NotificationsDao;

$notificationsDao = new NotificationsDao();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/notifications', function (Request $request, Response $response, $args) use ($notificationsDao) {
    $notifications = $notificationsDao->findAllNotifications();
    $response->getBody()->write(json_encode($notifications));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/recentNotification', function (Request $request, Response $response, $args) use ($notificationsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    !$id_company ? $id_company = '' : $id_company;

    $notifications = $notificationsDao->findRecentNotification($id_company);
    $response->getBody()->write(json_encode($notifications));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/addNotification', function (Request $request, Response $response, $args) use ($notificationsDao) {
    $dataNotifications = $request->getParsedBody();

    /*    if (empty($dataNotifications['description']) || empty($dataNotifications['company']))
        $resp = array('error' => true, 'message' => 'Ingrese todos los campos');
    else {*/
    $notifications = $notificationsDao->insertNotification($dataNotifications);

    if ($notifications == null)
        $resp = array('success' => true, 'message' => 'Notificacion ingresada correctamente');
    else if (isset($notifications['info']))
        $resp = array('info' => true, 'message' => $notifications['message']);
    else
        $resp = array('error' => true, 'message' => 'Ocurrio un error mientras ingresaba la información. Intente nuevamente');

    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->post('/updateNotification', function (Request $request, Response $response, $args) use ($notificationsDao) {
    $dataNotifications = $request->getParsedBody();

    if (empty($dataNotifications['description']) || empty($dataNotifications['company']))
        $resp = array('error' => true, 'message' => 'No hubo algun cambio');
    else {
        $notifications = $notificationsDao->updateNotification($dataNotifications);

        if ($notifications == null)
            $resp = array('success' => true, 'message' => 'Notificacion modificada correctamente');
        else if (isset($notifications['info']))
            $resp = array('info' => true, 'message' => $notifications['message']);
        else
            $resp = array('error' => true, 'message' => 'Ocurrio un error mientras modificaba la información. Intente nuevamente');
    }
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/updateCheckNotification', function (Request $request, Response $response, $args) use ($notificationsDao) {
    session_start();
    $id_company = $_SESSION['id_company'];

    $notifications = $notificationsDao->updateCheckNotification($id_company);

    if ($notifications == null)
        $resp = array('success' => true, 'message' => 'Notificaciones limpiadas correctamente');
    else if (isset($notifications['info']))
        $resp = array('info' => true, 'message' => $notifications['message']);
    else
        $resp = array('success' => true, 'message' => 'No se pudo limpiar las notificaciones. Intente nuevamente');
    $response->getBody()->write(json_encode($resp));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
});

$app->get('/deleteNotification/{id_notification}', function (Request $request, Response $response, $args) use ($notificationsDao) {
    $notifications = $notificationsDao->deleteNotification($args['id_notification']);

    if ($notifications == null)
        $resp = array('success' => true, 'message' => 'Notificacion eliminada correctamente');
    else
        $resp = array('error' => true, 'message' => 'No es posible eliminar la notificación, existe información asociada a ella');

    $response->getBody()->write(json_encode($resp));
    return $response->withHeader('Content-Type', 'application/json');
});
