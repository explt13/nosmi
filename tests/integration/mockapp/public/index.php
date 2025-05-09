<?php

use Explt13\Nosmi\Base\App;
use Explt13\Nosmi\Middleware\AuthorizationMiddleware;
use Explt13\Nosmi\Middleware\CorsMiddleware;
use Explt13\Nosmi\Middleware\ErrorHandlerMiddleware;
use Explt13\Nosmi\Middleware\RateLimiterMiddleware;
use Tests\Integration\mockapp\src\middlewares\AuthorizationHandler;

require_once dirname(__FILE__, 5)."/vendor/autoload.php";

$app = new App();
$app->bootstrap(__DIR__ . '/../config/.env')
    ->use(new RateLimiterMiddleware)
    ->use(new CorsMiddleware)
    ->use(new AuthorizationMiddleware(new AuthorizationHandler))
    ->run();
