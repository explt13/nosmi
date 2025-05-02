<?php

// requiring an autoload

use Explt13\Nosmi\Base\App;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();
$app->bootstrap('');
$app->run();