<?php

namespace Explt13\Nosmi\Base;

use DirectoryIterator;
use Explt13\Nosmi\Exceptions\ConfigParameterNotSetException;
use Explt13\Nosmi\Exceptions\InvalidResourceException;
use Explt13\Nosmi\Exceptions\InvalidTypeException;
use Explt13\Nosmi\Exceptions\ResourceReadException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\FileValidatorInterface;
use Explt13\Nosmi\Interfaces\ServiceProviderInterface;
use Explt13\Nosmi\Utils\Namespacer;

class ServiceProviderLoader
{
    private string $providers_folder;
    private string $providers_namespace;
    private ContainerInterface $container;
    private ConfigInterface $config;
    private FileValidatorInterface $file_validator;
    private Namespacer $namespacer;

    public function __construct(
        ContainerInterface $container,
        ConfigInterface $config,
        Namespacer $namespacer,
        FileValidatorInterface $file_validator,
    )
    {
        $this->namespacer = $namespacer;
        $this->config = $config;
        $this->file_validator = $file_validator;
        $this->container = $container;
    }
    
    public function load(): void
    {
        if (is_null($this->config->get('APP_ROOT'))) {
            throw new ConfigParameterNotSetException('APP_ROOT');
        }
        $src_folder = $this->config->get('APP_SRC') ?? $this->config->get('APP_ROOT') . '/src';
        if (!$this->file_validator->isDir($src_folder)) {
            throw InvalidResourceException::withMessage('The specified providers folder is not a valid directory: ' . $src_folder);
        }
        if (!$this->file_validator->isReadable($src_folder)) {
            throw new ResourceReadException($this->providers_folder);            
        }

        $this->providers_folder = $this->config->get('APP_PROVIDERS') ?? $src_folder . '/providers';

        // Get the PSR-4 namespace for providers
        $this->providers_namespace = $this->namespacer->generateNamespace($this->providers_folder);
        
        if (!$this->file_validator->isDir($this->providers_folder)) {
            throw InvalidResourceException::withMessage('The specified providers folder is not a valid directory: ' . $this->providers_folder);
        }
        if (!$this->file_validator->isReadable($this->providers_folder)) {
            throw new ResourceReadException($this->providers_folder);            
        }

        foreach (new DirectoryIterator($this->providers_folder) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                $this->loadProvider($fileInfo->getBasename('.php'));
            }
        }
    }

    private function loadProvider(string $provider): void
    {
        $object = $this->container->get($this->providers_namespace . $provider);

        if (!$object instanceof ServiceProviderInterface) {
            throw new InvalidTypeException(
                ServiceProviderInterface::class,
                $object::class
            );
        }

        $object->boot();
    }
}