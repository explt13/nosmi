<?php

use Explt13\Nosmi\Base\App;
use Explt13\Nosmi\Middleware\AuthorizationMiddleware;
use Explt13\Nosmi\Middleware\CorsMiddleware;
use Explt13\Nosmi\Middleware\ErrorHandlerMiddleware;

require_once dirname(__FILE__, 5)."/vendor/autoload.php";

$app = new App();
$app->bootstrap(__DIR__ . '/../config/.env')
    ->use(ErrorHandlerMiddleware::class)
    ->use(CorsMiddleware::class)
    ->use(AuthorizationMiddleware::class)
    ->run();
