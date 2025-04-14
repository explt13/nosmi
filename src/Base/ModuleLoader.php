<?php

namespace Explt13\Nosmi\Base;

use DirectoryIterator;
use Explt13\Nosmi\Exceptions\InvalidTypeException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Interfaces\MiddlewareInterface;
use Explt13\Nosmi\Utils\Namespacer;
use PHPUnit\Event\TestRunner\ExtensionLoadedFromPhar;

abstract class ModuleLoader
{
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
       
        $folder = $this->config->get($this->getFolderName());

        // Get the PSR-4 namespace for the folder
        $this->namespace = $this->namespacer->generateNamespace($folder);

        foreach (new DirectoryIterator($folder) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                $this->loadProvider($fileInfo->getBasename('.php'));
            }
        }
    }

    abstract protected function loadProvider(string $provider): void;
    abstract protected function getFolderName(): string;
}