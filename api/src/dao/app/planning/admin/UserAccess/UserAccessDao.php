<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class UserAccessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllUsersAccess($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        // $rol = $_SESSION['rol'];

        // if ($rol == 2) {
        $stmt = $connection->prepare("SELECT 
                                        -- Información Usuario
                                            us.id_user, 
                                            us.firstname, 
                                            us.lastname, 
                                            us.email, 
                                        -- Accesos (Basicos)
                                            IFNULL(usa.create_product, 0) AS create_product, 
                                            IFNULL(usa.create_material, 0) AS create_material, 
                                            IFNULL(usa.requisition, 0) AS requisition, 
                                            IFNULL(usa.create_machine, 0) AS create_machine,  
                                        -- Accesos (Configuración)
                                            IFNULL(usa.products_material, 0) AS products_material, 
                                            IFNULL(usa.programs_machine, 0) AS programs_machine, 
                                            IFNULL(usa.cicles_machine, 0) AS cicles_machine, 
                                            IFNULL(usa.stock, 0) AS stock, 
                                        -- Accesos (General)
                                            IFNULL(usa.sale, 0) AS sale, 
                                            IFNULL(usa.inv_abc, 0) AS inventory_abc, 
                                            IFNULL(usa.client, 0) AS client, 
                                        -- Accesos (Administrador)
                                            IFNULL(usa.user, 0) AS user, 
                                        -- Accesos (Navegación) 
                                            IFNULL(usa.inventory, 0) AS inventory, 
                                            IFNULL(usa.plan_order, 0) AS plan_order, 
                                            IFNULL(usa.program, 0) AS program, 
                                            IFNULL(usa.plan_load, 0) AS plan_load, 
                                            IFNULL(usa.explosion_of_material, 0) AS explosion_of_material, 
                                            IFNULL(usa.production_order, 0) AS production_order, 
                                            IFNULL(usa.office, 0) AS office, 
                                            IFNULL(usa.store, 0) AS store
                                        FROM users us
                                            LEFT JOIN users_access usa ON usa.id_user = us.id_user 
                                        WHERE us.id_company = :id_company");
        $stmt->execute(['id_company' => $id_company]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
        return $users;
        // }
    }

    public function insertUserAccessByUser($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("INSERT INTO users_access 
                                                      (
                                                        -- Informacion Usuario
                                                            id_user,
                                                        -- Accesos (Basicos)
                                                            create_product, 
                                                            create_material, 
                                                            create_machine,
                                                            requisition, 
                                                        -- Accesos (Configuración)
                                                            products_material, 
                                                            programs_machine, 
                                                            cicles_machine, 
                                                            stock, 
                                                        -- Accesos (General)
                                                            sale, 
                                                            inv_abc,
                                                            client,
                                                        -- Accesos (Administrador)
                                                            user,
                                                        -- Accesos (Navegación)
                                                            inventory, 
                                                            plan_order, 
                                                            program, 
                                                            explosion_of_material, 
                                                            production_order, 
                                                            office,                                                         
                                                            store
                                                      ) VALUES
                                                      (
                                                        -- Informacion Usuario
                                                            :id_user, 
                                                        -- Accesos (Basicos)
                                                            :create_product, 
                                                            :create_material, 
                                                            :create_machine,
                                                            :requisition,
                                                        -- Accesos (Configuración)
                                                            :products_material, 
                                                            :programs_machine, 
                                                            :cicles_machine, 
                                                            :stock, 
                                                        -- Accesos (General) 
                                                            :sale, 
                                                            :inventory_abc, 
                                                            :client,
                                                        -- Accesos (Administrador) 
                                                            :user, 
                                                        -- Accesos (Navegación)
                                                            :inventory, 
                                                            :plan_order, 
                                                            :program, 
                                                            :explosion_of_material, 
                                                            :production_order, 
                                                            :office,  
                                                            :store
                                                      )
                                        ");
            $stmt->execute([
                'id_user' => $dataUser['id_user'],                                      'inventory_abc' => $dataUser['inventoryABC'],
                'create_product' => $dataUser['planningCreateProduct'],                 'client' => $dataUser['client'],
                'create_material' => $dataUser['planningCreateMaterial'],               'user' => $dataUser['plannigUser'],
                'create_machine' => $dataUser['planningCreateMachine'],                 'inventory' => $dataUser['inventory'],
                'requisition' => $dataUser['requisition'],                              'plan_order' => $dataUser['order'],
                'products_material' => $dataUser['planningProductsMaterial'],           'program' => $dataUser['program'],
                'programs_machine' => $dataUser['programsMachine'],                     'explosion_of_material' => $dataUser['explosionOfMaterial'],
                'cicles_machine' => $dataUser['ciclesMachine'],                         'office' => $dataUser['office'],
                'stock' => $dataUser['stock'],                                          'production_order' => $dataUser['productionOrder'],
                'sale' => $dataUser['sale'],                                            'store' => $dataUser['store'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }

    public function updateUserAccessByUsers($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();
        /* Hacer un select
            Contar los usuarios
            si el usuario es > 1 no hacer nada
            de lo contrario realizar la actualizacion
         */
        $stmt = $connection->prepare("SELECT * FROM users_access");
        $stmt->execute();
        $rows = $stmt->rowCount();

        if ($rows > 1) {
            try {
                $stmt = $connection->prepare("UPDATE users_access SET 
                                                        -- Accesos (Basicos)
                                                            create_product = :create_product, 
                                                            create_material = :create_material, 
                                                            create_machine = :create_machine, 
                                                            requisition = :requisition, 
                                                        -- Accesos (Configuración)
                                                            products_material = :products_material, 
                                                            programs_machine = :programs_machine, 
                                                            cicles_machine = :cicles_machine, 
                                                            stock = :stock, 
                                                        -- Accesos (General)
                                                            sale = :sale, 
                                                            inv_abc = :inventory_abc, 
                                                            client = :client, 
                                                        -- Accesos (Administrador)
                                                            user = :user,
                                                        -- Accesos (Navegación)
                                                            inventory = :inventory, 
                                                            plan_order = :plan_order, 
                                                            program = :program, 
                                                            explosion_of_material = :explosion_of_material, 
                                                            production_order = :production_order, 
                                                            office = :office, 
                                                            store = :store
                                              WHERE id_user = :id_user");
                $stmt->execute([
                    'id_user' => $dataUser['id_user'],                                      'inventory_abc' => $dataUser['inventoryABC'],
                    'create_product' => $dataUser['planningCreateProduct'],                 'client' => $dataUser['client'],
                    'create_material' => $dataUser['planningCreateMaterial'],               'user' => $dataUser['plannigUser'],
                    'create_machine' => $dataUser['planningCreateMachine'],                 'inventory' => $dataUser['inventory'],
                    'requisition' => $dataUser['requisition'],                              'plan_order' => $dataUser['order'],
                    'products_material' => $dataUser['planningProductsMaterial'],           'program' => $dataUser['program'],
                    'programs_machine' => $dataUser['programsMachine'],                     'explosion_of_material' => $dataUser['explosionOfMaterial'],
                    'cicles_machine' => $dataUser['ciclesMachine'],                         'office' => $dataUser['office'],
                    'stock' => $dataUser['stock'],                                          'production_order' => $dataUser['productionOrder'],
                    'sale' => $dataUser['sale'],                                            'store' => $dataUser['store'],
                ]);
                $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $error = array('error' => true, 'message' => $message);
                return $error;
            }
        } else {
            return 1;
        }
    }

    public function deleteUserAccess($dataUser)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("DELETE FROM users_access WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $dataUser['id_user']]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
}
