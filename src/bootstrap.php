<?php
namespace Explt13\Nosmi;

use Explt13\Nosmi\Base\App;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App();
$app->bootstrap();
$app->run();