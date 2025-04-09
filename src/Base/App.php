<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Base\ServiceProviderLoader;
use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Routing\Router;

class App
{
    public static Registry $registry;
    private ContainerInterface $container;

    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    public function bootstrap(): void
    {
        session_start();
        ErrorHandler::getInstance();
        $container_dependencies = require_once CONF . '/dependencies.php';
        $this->container->init($container_dependencies);
        self::$registry = Registry::getInstance();
        self::$registry->setParams(require_once CONF . '/params.php');
        $serviceLoader = $this->container->get(ServiceProviderLoader::class);
        $serviceLoader->load();
    }

    public function run(): void
    {
        $router = $this->container->get(Router::class);
        $router->dispatch($_SERVER['QUERY_STRING']);
    }
}
