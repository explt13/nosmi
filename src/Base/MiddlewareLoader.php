<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Exceptions\InvalidTypeException;
use Explt13\Nosmi\Interfaces\MiddlewareInterface;

class MiddlewareLoader extends ModuleLoader
{
    private const FOLDER = 'APP_MIDDLEWARES';

    protected function loadProvider(string $provider): void
    {
        $object = $this->dependency_manager->getDependency($this->namespace . $provider);

        if (!$object instanceof MiddlewareInterface) {
            throw new InvalidTypeException(
                MiddlewareInterface::class,
                $object::class
            );
        }

        $object->run();
    }

    protected function getFolderName(): string
    {
        return self::FOLDER;
    }
}