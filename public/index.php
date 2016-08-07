<?php

const SILEX_ROOT = __DIR__.'../';
require_once SILEX_ROOT.'/vendor/autoload.php';

use App\App;

$app = new App();
// Here we go!
$app->run();
