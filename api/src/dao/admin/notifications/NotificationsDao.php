<?php

namespace TezlikPlaneacion\dao;

use Exception;
use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class NotificationsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllNotifications()
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT n.id_notification, n.id_company, n.description, IFNULL(c.company, 'Todas')AS company, n.date_notification, n.check_notification
                                        FROM admin_notifications n
                                        LEFT JOIN admin_companies c ON c.id_company = n.id_company;");
        $stmt->execute();
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $notifications = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("notifications", array('notifications' => $notifications));
        return $notifications;
    }

    public function findRecentNotification($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT n.id_notification, n.description, n.id_company, n.date_notification, n.check_notification, c.logo
                                      FROM admin_notifications n
                                        LEFT JOIN admin_companies c ON c.id_company = n.id_company
                                      WHERE n.id_company IN(0, :id_company) ORDER BY n.date_notification DESC;");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $notifications = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $notifications;
    }

    public function insertNotification($dataNotifications)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO admin_notifications (id_company, description, check_notification) 
                                          VALUES (:id_company, :descr, :check_notification)");
            $stmt->execute([
                'id_company' => $dataNotifications['company'],
                'descr' => $dataNotifications['description'],
                'check_notification' => 1
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateNotification($dataNotifications)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE admin_notifications SET id_company = :id_company, description = :descr
                                          WHERE id_notification = :id_notification");
            $stmt->execute([
                'id_company' => $dataNotifications['company'],
                'descr' => $dataNotifications['description'],
                'id_notification' => $dataNotifications['idNotification']
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateCheckNotification($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE admin_notifications SET check_notification = :check_notification
                                          WHERE id_company IN (0, :id_company)");
            $stmt->execute([
                'check_notification' => 0,
                'id_company' => $id_company
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function deleteNotification($id_notification)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM admin_notifications WHERE id_notification = :id_notification");
        $stmt->execute(['id_notification' => $id_notification]);
        $row = $stmt->rowCount();

        if ($row > 0) {
            $stmt = $connection->prepare("DELETE FROM admin_notifications WHERE id_notification = :id_notification");
            $stmt->execute(['id_notification' => $id_notification]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }
}
