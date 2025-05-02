<?php

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use Explt13\Nosmi\AppConfig\ConfigValidator;
use Explt13\Nosmi\Base\App;
use Explt13\Nosmi\Base\Controller;
use Explt13\Nosmi\Base\ControllerFactory;
use Explt13\Nosmi\Base\Db;
use Explt13\Nosmi\Base\ErrorHandler;
use Explt13\Nosmi\Base\Model;
use Explt13\Nosmi\Base\RequestPipeline;
use Explt13\Nosmi\Base\View;
use Explt13\Nosmi\Base\Widget;
use Explt13\Nosmi\Cache\Cache;
use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Http\Request;
use Explt13\Nosmi\Http\Response;
use Explt13\Nosmi\Http\ServerRequest;
use Explt13\Nosmi\Interfaces\AppInterface;
use Explt13\Nosmi\Interfaces\CacheInterface;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ConfigLoaderInterface;
use Explt13\Nosmi\Interfaces\ConfigValidatorInterface;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\ControllerFactoryInterface;
use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Interfaces\HttpFactoryInterface;
use Explt13\Nosmi\Interfaces\LightRequestHandlerInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\LogFormatterInterface;
use Explt13\Nosmi\Interfaces\LoggerInterface;
use Explt13\Nosmi\Interfaces\MiddlewareFactoryInterface;
use Explt13\Nosmi\Interfaces\MiddlewareRegistryInterface;
use Explt13\Nosmi\Interfaces\ModelInterface;
use Explt13\Nosmi\Interfaces\RequestPipelineInterface;
use Explt13\Nosmi\Interfaces\RouterInterface;
use Explt13\Nosmi\Interfaces\ViewInterface;
use Explt13\Nosmi\Logging\DefaultFormatter;
use Explt13\Nosmi\Logging\Logger;
use Explt13\Nosmi\Logging\LogStatus;
use Explt13\Nosmi\Middleware\MiddlewareDispatcher;
use Explt13\Nosmi\Middleware\MiddlewareFactory;
use Explt13\Nosmi\Middleware\MiddlewareRegistry;
use Explt13\Nosmi\Routing\Route;
use Explt13\Nosmi\Routing\Router;
use Explt13\Nosmi\Utils\Debug;
use Explt13\Nosmi\Utils\Types;
use Explt13\Nosmi\Utils\Utils;
use Explt13\Nosmi\Validators\ClassValidator;
use Explt13\Nosmi\Validators\ContainerValidator;
use Explt13\Nosmi\Validators\FileValidator;

return [
    // App Config
    ConfigInterface::class              =>  AppConfig::class,
    ConfigLoaderInterface::class        =>  ConfigLoader::class,
    ConfigValidatorInterface::class     =>  ConfigValidator::class,

    // Dependencies
    DependencyManagerInterface::class   =>  DependencyManager::class,
    ContainerInterface::class           =>  Container::class,
    
    // Logs
    LoggerInterface::class              =>  Logger::class,
    LogFormatterInterface::class        =>  DefaultFormatter::class,
    LogStatus::class                    =>  LogStatus::class,
    
    //Middleware
    MiddlewareRegistryInterface::class  =>  MiddlewareRegistry::class,
    LightRequestHandlerInterface::class =>  MiddlewareDispatcher::class,
    MiddlewareFactoryInterface::class   =>  MiddlewareFactory::class,

    // Http
    LightServerRequestInterface::class  =>  ServerRequest::class,
    LightRequestInterface::class        =>  Request::class,
    LightResponseInterface::class       =>  Response::class,
    HttpFactoryInterface::class         =>  HttpFactory::class,
    
    // Routing
    LightRouteInterface::class          =>  Route::class,
    RouterInterface::class              =>  Router::class,

    // Base
    AppInterface::class                 =>  App::class,
    ControllerInterface::class          =>  Controller::class,
    Db::class                           =>  Db::class,
    ErrorHandler::class                 =>  ErrorHandler::class,
    ModelInterface::class               =>  Model::class,
    ViewInterface::class                =>  View::class,
    CacheInterface::class               =>  Cache::class,
    Widget::class                       =>  Widget::class,
    ControllerFactoryInterface::class   =>  ControllerFactory::class,
    RequestPipelineInterface::class     =>  RequestPipeline::class,

    // Utils
    Debug::class                        =>  Debug::class,
    Types::class                        =>  Types::class,
    Utils::class                        =>  Utils::class,

    // Validators
    ClassValidator::class               =>  ClassValidator::class,
    ContainerValidator::class           =>  ContainerValidator::class,
    FileValidator::class                =>  FileValidator::class,
];