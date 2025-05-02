<?php

use Explt13\Nosmi\Base\App;

require_once dirname(__FILE__, 5)."/vendor/autoload.php";

$app = new App();
$app->bootstrap(__DIR__ . '/../config/.env');
$app->run();