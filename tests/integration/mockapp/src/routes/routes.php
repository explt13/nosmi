<?php

namespace Tests\Integration\mockapp\src\routes;

use Explt13\Nosmi\Routing\Route;
use Tests\Integration\mockapp\src\controllers\UserController;

// will be redirected to  (.)* index.php?$1  when using Apache so wont need for `/index.php` part
Route::get('/index.php/user/profile', UserController::class, "profile");
Route::get('/index.php/user/settings', UserController::class, "settings");