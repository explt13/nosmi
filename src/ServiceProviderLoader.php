<?php

namespace Explt13\Nosmi;

use Explt13\Nosmi\interfaces\ContainerInterface;

class ServiceProviderLoader
{
    private array $providers;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $providers = array_map(fn($item) => preg_replace('/\.php/', '', $item), 
                    array_values(array_filter(scandir(APP . '/providers/'), fn ($dirItem) => is_file(APP . '/providers/' . $dirItem))));
        $this->providers = $providers;
    }
    
    public function load()
    {
        foreach ($this->providers as $provider)
        {
            $className = "Surfsail\\providers\\" . $provider;
            $object = $this->container->get($className);
            $object->register();
        }
    }
}