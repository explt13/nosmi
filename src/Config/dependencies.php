<?php

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use Explt13\Nosmi\AppConfig\ConfigValidator;
use Explt13\Nosmi\Base\App;
use Explt13\Nosmi\Base\Controller;
use Explt13\Nosmi\Base\ControllerResolver;
use Explt13\Nosmi\Base\Db;
use Explt13\Nosmi\Base\ErrorHandler;
use Explt13\Nosmi\Base\Model;
use Explt13\Nosmi\Base\Registry;
use Explt13\Nosmi\Base\ServiceProviderLoader;
use Explt13\Nosmi\Base\View;
use Explt13\Nosmi\Base\Widget;
use Explt13\Nosmi\Cache;
use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Dependencies\ContainerValidator;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Interfaces\CacheInterface;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ConfigValidatorInterface;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\FileValidatorInterface;
use Explt13\Nosmi\Interfaces\LogFormatterInterface;
use Explt13\Nosmi\Interfaces\LogInterface;
use Explt13\Nosmi\Logging\Log;
use Explt13\Nosmi\Logging\LogFormatter;
use Explt13\Nosmi\Logging\LogFormatterModes;
use Explt13\Nosmi\Logging\LogStatus;
use Explt13\Nosmi\Middlewares\MiddlewareLoader;
use Explt13\Nosmi\Routing\Request;
use Explt13\Nosmi\Routing\RouteContext;
use Explt13\Nosmi\Routing\Router;
use Explt13\Nosmi\Validators\ClassValidator;
use Explt13\Nosmi\Validators\FileValidator;

return [
    ConfigInterface::class => AppConfig::class,
    ConfigLoader::class => ConfigLoader::class,
    ConfigValidatorInterface::class => ConfigValidator::class,
    ContainerInterface::class => Container::class,
    ContainerValidator::class => ContainerValidator::class,
    DependencyManager::class => DependencyManager::class,
    LogInterface::class => Log::class,
    LogFormatterInterface::class => LogFormatter::class,
    LogFormatterModes::class => LogFormatterModes::class,
    LogStatus::class => LogStatus::class,
    ClassValidator::class => ClassValidator::class,
    FileValidatorInterface::class => FileValidator::class,
    App::class => App::class,
    CacheInterface::class => Cache::class,
    MiddlewareLoader::class => MiddlewareLoader::class,
    Request::class => Request::class,
    RouteContext::class => RouteContext::class,
    Router::class => Router::class,
    Controller::class => Controller::class,
    ControllerResolver::class => ControllerResolver::class,
    Db::class => Db::class,
    ErrorHandler::class => ErrorHandler::class,
    Model::class => Model::class,
    Registry::class => Registry::class,
    ServiceProviderLoader::class => ServiceProviderLoader::class,
    View::class => View::class,
    Widget::class => Widget::class,
];