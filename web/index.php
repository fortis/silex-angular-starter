<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\App;
use App\RoutesLoader;

$app = new App();
$app['debug'] = TRUE;

$routesLoader = new RoutesLoader($app);
$routesLoader->bindRoutes();

// Here we go!
$app->run();
