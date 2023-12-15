<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/router.php");

// Login
get('/', '/index.php');

//Global
get('/forgot-pass', '/global/views/login/forgot-password.php');
get('/reset-pass', '/global/views/login/reset-password.php');

/* ADMIN */
//Navbar
get('/admin', '/admin/index.php');
get('/admin/companies', '/admin/views/companies/companies.php');
get('/admin/companies-licences', '/admin/views/companies/companiesLicenses.php');
get('/admin/users-log', '/admin/views/users/usersLog.php');
get('/admin/notifications', '/admin/views/notifications/notifications.php');
get('/admin/companies-user', '/admin/views/companies/companyUsers.php');
//Header
get('/admin/users-admins', '/admin/views/users/usersAdmins.php');
get('/admin/users', '/admin/views/users/users.php');
get('/admin/plans', '/admin/views/plans/plans.php');
get('/admin/magnitudes', '/admin/views/magnitudes/magnitudes.php');
get('/admin/units', '/admin/views/units/units.php');
// get('/admin/binnacle', '/admin/views/binnacle/binnacle.php');
get('/admin/profile', '/admin/views/perfil/perfil.php');

/* PLANNING */
get('/planning', '/planning/index.php');
get('/planning/inventory', '/planning/views/inventory/inventory.php');
get('/planning/orders', '/planning/views/orders/orders.php');
get('/planning/offices', '/planning/views/offices/offices.php');
get('/planning/programming', '/planning/views/program/programming/programming.php');
get('/planning/consolidated', '/planning/views/program/consolidated/consolidated.php');
get('/planning/explosion-materials', '/planning/views/explosionMaterials/explosionMaterials.php');
get('/planning/production-order', '/planning/views/productionOrder/productionOrder.php');
get('/planning/store', '/planning/views/store/store.php');
//Basic
get('/planning/molds', '/planning/views/basic/invMolds.php');
get('/planning/products', '/planning/views/basic/createProducts.php');
get('/planning/materials', '/planning/views/basic/createRawMaterials.php');
get('/planning/machines', '/planning/views/basic/createMachines.php');
get('/planning/process', '/planning/views/basic/createProcess.php');
get('/planning/requisitions', '/planning/views/basic/requisitions.php');
//Config
get('/planning/product-materials', '/planning/views/config/productMaterials.php');
get('/planning/product-process', '/planning/views/config/productProcess.php');
// get('/planning/cicles-machines', '/planning/views/config/planCiclesMachine.php');
get('/planning/planning-machines', '/planning/views/config/planningMachines.php');
get('/planning/stock', '/planning/views/config/stock.php');
//General
get('/planning/categories', '/planning/views/general/categories.php');
get('/planning/sales', '/planning/views/general/sales.php');
get('/planning/inventoryABC', '/planning/views/general/inventoryABC.php');
//Admin
get('/planning/clients', '/planning/views/admin/clients.php');
get('/planning/order-types', '/planning/views/admin/order_types.php');
get('/planning/users', '/planning/views/admin/users.php');
get('/planning/profile', '/planning/views/perfil/perfil.php');
