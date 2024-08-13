<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FilesDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function avatarUser($id_user, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/users/' . $id_company;
        $allowTypes = array('jpg', 'jpeg', 'png');

        $image_name = str_replace(' ', '', $_FILES['avatar']['name']);
        $tmp_name   = $_FILES['avatar']['tmp_name'];
        $size       = $_FILES['avatar']['size'];
        $type       = $_FILES['avatar']['type'];
        $error      = $_FILES['avatar']['error'];

        /* Verifica si directorio esta creado y lo crea */
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $targetDir = '/assets/images/users/' . $id_company;
        $targetFilePath = $targetDir . '/' . $image_name;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowTypes)) {
            $sql = "UPDATE users SET avatar = :avatar WHERE id_user = :id_user AND id_company = :id_company";
            $query = $connection->prepare($sql);
            $query->execute([
                'avatar' => $targetFilePath,
                'id_user' => $id_user,
                'id_company' => $id_company
            ]);

            $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/users/' . $id_company;
            $targetFilePath = $targetDir . '/' . $image_name;

            move_uploaded_file($tmp_name, $targetFilePath);
        }
    }

    public function avatarUserAdmin($id_admin)
    {
        $connection = Connection::getInstance()->getConnection();
        $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/users/admin';
        $allowTypes = array('jpg', 'jpeg', 'png');

        $image_name = str_replace(' ', '', $_FILES['avatar']['name']);
        $tmp_name   = $_FILES['avatar']['tmp_name'];
        $size       = $_FILES['avatar']['size'];
        $type       = $_FILES['avatar']['type'];
        $error      = $_FILES['avatar']['error'];

        /* Verifica si directorio esta creado y lo crea */
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $targetDir = '/assets/images/users/admin';
        $targetFilePath = $targetDir . '/' . $image_name;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowTypes)) {
            $sql = "UPDATE admins SET avatar = :avatar WHERE id_admin = :id_admin";
            $query = $connection->prepare($sql);
            $query->execute([
                'avatar' => $targetFilePath,
                'id_admin' => $id_admin
            ]);

            $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/users/admin';
            $targetFilePath = $targetDir . '/' . $image_name;

            move_uploaded_file($tmp_name, $targetFilePath);
        }
    }

    public function logoCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/companies/' . $id_company;
        $allowTypes = array('jpg', 'jpeg', 'png');

        $image_name = str_replace(' ', '', $_FILES['logo']['name']);
        $tmp_name   = $_FILES['logo']['tmp_name'];
        $size       = $_FILES['logo']['size'];
        $type       = $_FILES['logo']['type'];
        $error      = $_FILES['logo']['error'];

        /* Verifica si directorio esta creado y lo crea */
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $targetDir = '/assets/images/companies/' . $id_company;
        $targetFilePath = $targetDir . '/' . $image_name;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowTypes)) {
            $sql = "UPDATE companies SET logo = :logo WHERE id_company = :id_company";
            $query = $connection->prepare($sql);
            $query->execute([
                'logo' => $targetFilePath,
                'id_company' => $id_company
            ]);

            $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/companies/' . $id_company;
            $targetFilePath = $targetDir . '/' . $image_name;

            move_uploaded_file($tmp_name, $targetFilePath);
        }
    }

    public function imageProduct($id_product, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/products/' . $id_company;
        $allowTypes = array('jpg', 'jpeg', 'png');

        $image_name = str_replace(' ', '', $_FILES['img']['name']);
        $tmp_name   = $_FILES['img']['tmp_name'];
        $size       = $_FILES['img']['size'];
        $type       = $_FILES['img']['type'];
        $error      = $_FILES['img']['error'];


        /* Verifica si directorio esta creado y lo crea */
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $targetDir = '/assets/images/products/' . $id_company;
        $targetFilePath = $targetDir . '/' . $image_name;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowTypes)) {
            $sql = "UPDATE products SET img = :img WHERE id_product = :id_product AND id_company = :id_company";
            $query = $connection->prepare($sql);
            $query->execute([
                'img' => $targetFilePath,
                'id_product' => $id_product,
                'id_company' => $id_company
            ]);

            $targetDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/assets/images/products/' . $id_company;
            $targetFilePath = $targetDir . '/' . $image_name;

            move_uploaded_file($tmp_name, $targetFilePath);
        }
    }

    public function imageClient($id_client, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $targetDir = dirname(dirname(dirname(__DIR__))) . '/assets/images/clients/' . $id_company;
        $allowTypes = array('jpg', 'jpeg', 'png');

        $image_name = str_replace(' ', '', $_FILES['img']['name']);
        $tmp_name   = $_FILES['img']['tmp_name'];
        $size       = $_FILES['img']['size'];
        $type       = $_FILES['img']['type'];
        $error      = $_FILES['img']['error'];

        /* Verifica si directorio esta creado y lo crea */
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $targetDir = '/api/src/assets/images/clients/' . $id_company;
        $targetFilePath = $targetDir . '/' . $image_name;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowTypes)) {
            $sql = "UPDATE plan_clients SET img = :img WHERE id_client = :id_client";
            $query = $connection->prepare($sql);
            $query->execute([
                'img' => $targetFilePath,
                'id_client' => $id_client
            ]);

            $targetDir = dirname(dirname(dirname(__DIR__))) . '/assets/images/clients/' . $id_company;
            $targetFilePath = $targetDir . '/' . $image_name;

            move_uploaded_file($tmp_name, $targetFilePath);
        }
    }

    public function avatarSeller($id_seller, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $targetDir = dirname(dirname(dirname(__DIR__))) . '/assets/images/sellers/' . $id_company;
        $allowTypes = array('jpg', 'jpeg', 'png');

        $image_name = str_replace(' ', '', $_FILES['avatar']['name']);
        $tmp_name   = $_FILES['avatar']['tmp_name'];
        $size       = $_FILES['avatar']['size'];
        $type       = $_FILES['avatar']['type'];
        $error      = $_FILES['avatar']['error'];

        /* Verifica si directorio esta creado y lo crea */
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $targetDir = '/assets/images/sellers/' . $id_company;
        $targetFilePath = $targetDir . '/' . $image_name;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowTypes)) {
            $sql = "UPDATE sellers SET avatar = :avatar WHERE id_seller = :id_seller";
            $query = $connection->prepare($sql);
            $query->execute([
                'avatar' => $targetFilePath,
                'id_seller' => $id_seller
            ]);

            $targetDir = dirname(dirname(dirname(__DIR__))) . '/assets/images/sellers/' . $id_company;
            $targetFilePath = $targetDir . '/' . $image_name;

            move_uploaded_file($tmp_name, $targetFilePath);
        }
    }
}
