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
                                            IFNULL(usa.create_machine, 0) AS create_machine,  
                                            IFNULL(usa.payroll, 0) AS payroll, 
                                        -- Accesos (Configuración)
                                            IFNULL(usa.products_material, 0) AS products_material, 
                                            IFNULL(usa.programs_machine, 0) AS programs_machine, 
                                            IFNULL(usa.stock, 0) AS stock, 
                                            IFNULL(usa.calendar, 0) AS calendar, 
                                        -- Accesos (General)
                                            IFNULL(usa.client, 0) AS client, 
                                            IFNULL(usa.seller, 0) AS seller, 
                                            IFNULL(usa.sale, 0) AS sale, 
                                        -- Accesos (Administrador)
                                            IFNULL(usa.user, 0) AS user, 
                                        -- Accesos (Navegación) 
                                            IFNULL(usa.inventory, 0) AS inventory,
                                            IFNULL(usa.requisition, 0) AS requisition, 
                                            IFNULL(usa.plan_order, 0) AS plan_order, 
                                            IFNULL(usa.program, 0) AS program,
                                            IFNULL(usa.explosion_of_material, 0) AS explosion_of_material, 
                                            IFNULL(usa.production_order, 0) AS production_order, 
                                            IFNULL(usa.type_machine_op, 0) AS type_machine_op, 
                                            IFNULL(usa.op_to_store, 0) AS op_to_store, 
                                            IFNULL(usa.store, 0) AS store,
                                            IFNULL(usa.office, 0) AS office
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
                                                            create_machine,
                                                            payroll, 
                                                        -- Accesos (Configuración)
                                                            products_material, 
                                                            programs_machine, 
                                                            stock, 
                                                            calendar, 
                                                        -- Accesos (General)
                                                            client,
                                                            seller,
                                                            sale, 
                                                        -- Accesos (Administrador)
                                                            user,
                                                        -- Accesos (Navegación)
                                                            inventory, 
                                                            requisition,
                                                            plan_order, 
                                                            program,
                                                            explosion_of_material, 
                                                            production_order, 
                                                            type_machine_op,
                                                            op_to_store,                                                         
                                                            store,
                                                            office                                                         
                                                      ) VALUES
                                                      (
                                                        -- Informacion Usuario
                                                            :id_user,
                                                        -- Accesos (Basicos)
                                                            :create_product, 
                                                            :create_machine,
                                                            :payroll, 
                                                        -- Accesos (Configuración)
                                                            :products_material, 
                                                            :programs_machine, 
                                                            :stock, 
                                                            :calendar, 
                                                        -- Accesos (General)
                                                            :client,
                                                            :seller,
                                                            :sale, 
                                                        -- Accesos (Administrador)
                                                            :user,
                                                        -- Accesos (Navegación)
                                                            :inventory, 
                                                            :requisition,
                                                            :plan_order, 
                                                            :program, 
                                                            :explosion_of_material, 
                                                            :production_order, 
                                                            :type_machine_op,
                                                            :op_to_store,                                                         
                                                            :store,
                                                            :office
                                                      )");
            $stmt->execute([
                'id_user' => $dataUser['idUser'],
                'create_product' => $dataUser['planningCreateProduct'],
                'create_machine' => $dataUser['planningCreateMachine'],
                'payroll' => $dataUser['payroll'],
                'products_material' => $dataUser['planningProductsMaterial'],
                'programs_machine' => $dataUser['programsMachine'],
                'stock' => $dataUser['stock'],
                'calendar' => $dataUser['calendar'],
                'client' => $dataUser['client'],
                'seller' => $dataUser['seller'],
                'sale' => $dataUser['sale'],
                'user' => $dataUser['plannigUser'],
                'inventory' => $dataUser['inventory'],
                'requisition' => $dataUser['requisition'],
                'plan_order' => $dataUser['order'],
                'program' => $dataUser['program'],
                'explosion_of_material' => $dataUser['explosionOfMaterial'],
                'production_order' => $dataUser['productionOrder'],
                'type_machine_op' => $dataUser['typeMachineOP'],
                'op_to_store' => $dataUser['opToStore'],
                'store' => $dataUser['store'],
                'office' => $dataUser['office'],
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
                                                            create_machine = :create_machine,
                                                            payroll = :payroll, 
                                                        -- Accesos (Configuración)
                                                            products_material = :products_material, 
                                                            programs_machine = :programs_machine, 
                                                            stock = :stock, 
                                                            calendar = :calendar, 
                                                        -- Accesos (General)
                                                            client = :client,
                                                            seller = :seller,
                                                            sale = :sale, 
                                                        -- Accesos (Administrador)
                                                            user = :user,
                                                        -- Accesos (Navegación)
                                                            inventory = :inventory, 
                                                            requisition = :requisition,
                                                            plan_order = :plan_order, 
                                                            program = :program,
                                                            explosion_of_material = :explosion_of_material, 
                                                            production_order = :production_order, 
                                                            type_machine_op = :type_machine_op, 
                                                            op_to_store = :op_to_store,                                                         
                                                            store = :store,
                                                            office = :office
                                              WHERE id_user = :id_user");
                $stmt->execute([
                    'id_user' => $dataUser['idUser'],
                    'create_product' => $dataUser['planningCreateProduct'],
                    'create_machine' => $dataUser['planningCreateMachine'],
                    'payroll' => $dataUser['payroll'],
                    'products_material' => $dataUser['planningProductsMaterial'],
                    'programs_machine' => $dataUser['programsMachine'],
                    'stock' => $dataUser['stock'],
                    'calendar' => $dataUser['calendar'],
                    'client' => $dataUser['client'],
                    'seller' => $dataUser['seller'],
                    'sale' => $dataUser['sale'],
                    'user' => $dataUser['plannigUser'],
                    'inventory' => $dataUser['inventory'],
                    'requisition' => $dataUser['requisition'],
                    'plan_order' => $dataUser['order'],
                    'program' => $dataUser['program'],
                    'explosion_of_material' => $dataUser['explosionOfMaterial'],
                    'production_order' => $dataUser['productionOrder'],
                    'type_machine_op' => $dataUser['typeMachineOP'],
                    'op_to_store' => $dataUser['opToStore'],
                    'store' => $dataUser['store'],
                    'office' => $dataUser['office'],
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
        $stmt->execute(['id_user' => $dataUser['idUser']]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    }
}
