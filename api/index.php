<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/AutoloaderSourceCode.php';

$app = AppFactory::create();
$app->setBasePath('/api');

/* Admin */

// Companies
require_once('../api/src/routes/admin/companies/routeCompanies.php');
require_once('../api/src/routes/admin/companies/routeCompaniesLicense.php');
require_once('../api/src/routes/admin/companies/routeUsersAllowed.php');
require_once('../api/src/routes/admin/companies/routeCompanyUsers.php');

// Plan
require_once('../api/src/routes/admin/plans/routePlans.php');
require_once('../api/src/routes/admin/plans/routePlanAccess.php');

// Login
require_once('../api/src/routes/admin/login/routeLastLoginsUsers.php');

// Products
require_once('../api/src/routes/admin/products/routeQuantityProducts.php');

// Notifications
require_once('../api/src/routes/admin/notifications/routeNotifications.php');
// Users
require_once('../api/src/routes/admin/users/routeActiveUsers.php');
require_once('../api/src/routes/admin/users/routeCloseSessionUsers.php');
require_once('../api/src/routes/admin/users/routeUserAdmin.php');
require_once('../api/src/routes/app/login/routeInactiveUser.php');

// Units
require_once('../api/src/routes/admin/units/routeUnits.php');
// Magnitudes
require_once('../api/src/routes/admin/magnitude/routeMagnitude.php');

// Dashboard
require_once('../api/src/routes/admin/dashboard/routeDashboardGeneral.php');


/* Global */
require_once('../api/src/routes/app/global/routeCompany.php');
require_once('../api/src/routes/app/global/routeDoubleFactor.php');

// Profile
require_once('../api/src/routes/app/global/routeProfile.php');

/* Login */
require_once('../api/src/routes/app/login/routeLogin.php');
require_once('../api/src/routes/app/login/routepassUser.php');


/* User */
require_once('../api/src/routes/app/users/routeGeneralUserAccess.php');
require_once('../api/src/routes/app/users/routeUsers.php');
require_once('../api/src/routes/app/users/routeQuantityUsers.php');
require_once('../api/src/routes/app/users/routeUsersStatus.php');

/* App Planning */
// Dashboard
require_once('../api/src/routes/app/planning/dashboard/routeDashboard.php');
// Basic
require_once('../api/src/routes/app/planning/basic/routeInvMolds.php');
require_once('../api/src/routes/app/planning/basic/routeMachines.php');
require_once('../api/src/routes/app/planning/basic/routeMaterials.php');
require_once('../api/src/routes/app/planning/basic/routeProducts.php');
require_once('../api/src/routes/app/planning/basic/routeProductsMeasures.php');
require_once('../api/src/routes/app/planning/basic/routeAreas.php');
require_once('../api/src/routes/app/planning/basic/routePayroll.php');
require_once('../api/src/routes/app/planning/basic/routeProcess.php');
require_once('../api/src/routes/app/planning/basic/routeRequisitions.php');
// Classification
require_once('../api/src/routes/app/planning/classification/routeClassification.php');
// Config
require_once('../api/src/routes/app/planning/config/routeProductsMaterials.php');
require_once('../api/src/routes/app/planning/config/routeProductsInProcess.php');
require_once('../api/src/routes/app/planning/config/routeProductsProcess.php');
require_once('../api/src/routes/app/planning/config/routePlanning_machines.php');
require_once('../api/src/routes/app/planning/config/routePlanCiclesMachine.php');
require_once('../api/src/routes/app/planning/config/routePStock.php');
require_once('../api/src/routes/app/planning/config/routeRMStock.php');
require_once('../api/src/routes/app/planning/config/routeCalender.php');
// General
require_once('../api/src/routes/app/planning/general/routeCategories.php');
require_once('../api/src/routes/app/planning/general/routeUnitsSales.php');
require_once('../api/src/routes/app/planning/general/routeInventoryABC.php');
require_once('../api/src/routes/app/planning/general/routeSeller.php');
// Administrador
require_once('../api/src/routes/app/planning/admin/routeUserAccess.php');
require_once('../api/src/routes/app/planning/admin/routeClients.php');
require_once('../api/src/routes/app/planning/admin/routeOrderTypes.php');

// Inventario
require_once('../api/src/routes/app/planning/inventory/routeInventory.php');
// Pedidos
require_once('../api/src/routes/app/planning/order/routeOrders.php');
// Programa
require_once('../api/src/routes/app/planning/program/routeProgramming.php');
require_once('../api/src/routes/app/planning/program/routeConsolidated.php');
// Despachos
require_once('../api/src/routes/app/planning/offices/routeOffices.php');
// Explosion de Materiales
require_once('../api/src/routes/app/planning/explosionMaterials/routeExplosionMaterial.php');
require_once('../api/src/routes/app/planning/productionOrder/routeProductionOrder.php');
require_once('../api/src/routes/app/planning/store/routeStore.php');

$app->run();
