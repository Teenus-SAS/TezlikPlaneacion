<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralUserAccessDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findUserAccess($id_company, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();
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
                                                IFNULL(usa.requisition, 0) AS requisition, 
                                            -- Accesos (Configuración)
                                                IFNULL(usa.products_material, 0) AS products_material, 
                                                IFNULL(usa.programs_machine, 0) AS programs_machine, 
                                                IFNULL(usa.cicles_machine, 0) AS cicles_machine, 
                                                IFNULL(usa.stock, 0) AS stock,  
                                            -- Accesos (General)
                                                IFNULL(usa.client, 0) AS client, 
                                                IFNULL(usa.sale, 0) AS sale, 
                                                IFNULL(usa.inventory_abc, 0) AS inventory_abc, 
                                            -- Accesos (Administrador)
                                                IFNULL(usa.user, 0) AS user, 
                                            -- Accesos (Navegación)
                                                IFNULL(usa.inventory, 0) AS inventory, 
                                                IFNULL(usa.plan_order, 0) AS plan_order,
                                                IFNULL(usa.program, 0) AS program, 
                                                IFNULL(usa.explosion_of_material, 0) AS explosion_of_material, 
                                                IFNULL(usa.office, 0) AS office, 
                                                IFNULL(usa.production_order, 0) AS production_order, 
                                                IFNULL(usa.store, 0) AS store
                                      FROM users us
                                        LEFT JOIN planning_user_access usa ON us.id_user = usa.id_user
                                      WHERE us.id_company = :id_company AND us.id_user = :id_user");
        $stmt->execute(['id_company' => $id_company, 'id_user' => $id_user]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $users = $stmt->fetch($connection::FETCH_ASSOC);
        $this->logger->notice("usuarios Obtenidos", array('usuarios' => $users));
        return $users;
    }

    public function findUserAccessByUser($id_user)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                            -- Información Usuario
                                                u.firstname, 
                                                u.lastname, 
                                                u.email,
                                            -- Accesos (Basicos)
                                                IFNULL(pua.create_product, 0) AS planning_product, 
                                                IFNULL(pua.create_material, 0) AS planning_material, 
                                                IFNULL(pua.create_machine, 0) AS planning_machine, 
                                                IFNULL(pua.requisition, 0) AS requisition,
                                            -- Accesos (Configuración)
                                                IFNULL(pua.products_material, 0) AS planning_products_material, 
                                                IFNULL(pua.programs_machine, 0) AS programs_machine, 
                                                IFNULL(pua.cicles_machine, 0) AS cicles_machine, 
                                                IFNULL(pua.stock, 0) AS stock, 
                                            -- Accesos (General)
                                                IFNULL(pua.client, 0) AS client, 
                                                IFNULL(pua.sale, 0) AS sale, 
                                                IFNULL(pua.inventory_abc, 0) AS inventory_abc, 
                                            -- Accesos (Administrador)
                                                IFNULL(pua.user, 0) AS planning_user,
                                            -- Accesos (Navegación)
                                                IFNULL(pua.production_order, 0) AS production_order, 
                                                IFNULL(pua.store, 0) AS store,
                                                IFNULL(pua.inventory, 0) AS inventory, 
                                                IFNULL(pua.plan_order, 0) AS plan_order,
                                                IFNULL(pua.program, 0) AS program, 
                                                IFNULL(pua.explosion_of_material, 0) AS explosion_of_material, 
                                                IFNULL(pua.office, 0) AS office,
                                            -- Accesos (Plan)
                                                pa.plan_order AS plan_planning_order, 
                                                pa.plan_inventory AS plan_planning_inventory, 
                                                pa.plan_program AS plan_planning_program,
                                                pa.plan_explosion_of_material AS plan_planning_explosion_of_material, 
                                                pa.plan_production_order, 
                                                pa.plan_store, 
                                                pa.plan_office AS plan_planning_office    
                                      FROM users u
                                        LEFT JOIN planning_user_access pua ON pua.id_user = u.id_user
                                        INNER JOIN companies_licenses cl ON cl.id_company = u.id_company
                                        INNER JOIN plans_access pa ON pa.id_plan = cl.plan
                                      WHERE u.id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);
        $userAccess = $stmt->fetch($connection::FETCH_ASSOC);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $this->logger->notice("usuario Obtenido", array('usuario' => $userAccess));
        return $userAccess;
    }

    public function setGeneralAccess($id_user)
    {
        $userAccess = $this->findUserAccessByUser($id_user);

        $_SESSION['planning_product'] = $userAccess['planning_product'];
        $_SESSION['planning_material'] = $userAccess['planning_material'];
        $_SESSION['planning_machine'] = $userAccess['planning_machine'];
        $_SESSION['requisition'] = $userAccess['requisition'];
        $_SESSION['planning_products_material'] = $userAccess['planning_products_material'];
        $_SESSION['programs_machine'] = $userAccess['programs_machine'];
        $_SESSION['cicles_machine'] = $userAccess['cicles_machine'];
        $_SESSION['stock'] = $userAccess['stock'];
        $_SESSION['client'] = $userAccess['client'];
        $_SESSION['sale'] = $userAccess['sale'];
        $_SESSION['inventory_abc'] = $userAccess['inventory_abc'];
        $_SESSION['planning_user'] = $userAccess['planning_user'];
        $_SESSION['inventory'] = $userAccess['inventory'];
        $_SESSION['plan_order'] = $userAccess['plan_order'];
        $_SESSION['program'] = $userAccess['program'];
        $_SESSION['explosion_of_material'] = $userAccess['explosion_of_material'];
        $_SESSION['production_order'] = $userAccess['production_order'];
        $_SESSION['store'] = $userAccess['store'];
        $_SESSION['office'] = $userAccess['office'];
        $_SESSION['plan_planning_order'] = $userAccess['plan_planning_order'];
        $_SESSION['plan_planning_inventory'] = $userAccess['plan_planning_inventory'];
        $_SESSION['plan_planning_program'] = $userAccess['plan_planning_program'];
        $_SESSION['plan_planning_explosion_of_material'] = $userAccess['plan_planning_explosion_of_material'];
        $_SESSION['plan_production_order'] = $userAccess['plan_production_order'];
        $_SESSION['plan_store'] = $userAccess['plan_store'];
        $_SESSION['plan_planning_office'] = $userAccess['plan_planning_office'];
    }
}
