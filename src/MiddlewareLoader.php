<?php

namespace Explt13\Nosmi;

use Explt13\Nosmi\Interfaces\ContainerInterface;

class MiddlewareLoader
{
    protected array $middlewares;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->load();
        $this->container = $container;
    }

    protected function load()
    {
        $dirfiles = array_map(fn($item) => preg_replace('/\.php/', '', $item), 
                    array_values(array_filter(scandir(APP . '/middlewares/'), fn ($dirItem) => is_file(APP . '/middlewares/' . $dirItem))));
        $this->middlewares = $dirfiles;
    }

    public function run()
    {
        foreach ($this->middlewares as $middleware) {
            $className = "Surfsail\\middlewares\\" . $middleware;
            $object = $this->container->get($className);
            $object->run();
        }
    }
}