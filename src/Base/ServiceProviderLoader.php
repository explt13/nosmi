<?php

namespace Explt13\Nosmi\Base;

use DirectoryIterator;
use Explt13\Nosmi\Exceptions\InvalidTypeException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Interfaces\ServiceProviderInterface;
use Explt13\Nosmi\Utils\Namespacer;

class ServiceProviderLoader
{
    protected const FOLDER = 'APP_PROVIDERS';
    protected string $namespace;
    protected DependencyManagerInterface $dependency_manager;
    protected ConfigInterface $config;
    protected Namespacer $namespacer;

    public function __construct(
        DependencyManagerInterface $dependency_manager,
        ConfigInterface $config,
        Namespacer $namespacer
    )
    {
        $this->dependency_manager = $dependency_manager;
        $this->config = $config;
        $this->namespacer = $namespacer;
    }

    public final function load(): void
    {
       
        $folder = $this->config->get(self::FOLDER);

        // Get the PSR-4 namespace for the folder
        $this->namespace = $this->namespacer->generateNamespace($folder);

        foreach (new DirectoryIterator($folder) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                $this->loadProvider($fileInfo->getBasename('.php'));
            }
        }
    }

    protected function loadProvider(string $provider): void
    {
        $object = $this->dependency_manager->getDependency($this->namespace . $provider);

        if (!$object instanceof ServiceProviderInterface) {
            throw new InvalidTypeException(
                ServiceProviderInterface::class,
                $object::class
            );
        }

        $object->boot();
    }
}