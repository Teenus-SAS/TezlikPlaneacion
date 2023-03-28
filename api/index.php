<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/AutoloaderSourceCode.php';

$app = AppFactory::create();
$app->setBasePath('/api');

require_once('../api/src/routes/admin/companies/routeCompanies.php');

$app->run();
