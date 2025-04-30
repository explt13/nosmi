<?php

namespace Explt13\Nosmi\Interfaces;

/**
 * Interface AppInterface
 *
 * Defines the contract for an application class, including methods for
 * middleware usage, service registration, configuration bootstrapping,
 * and application execution.
 */

interface AppInterface
{
    /**
     * Use a middleware in the application.
     *
     * @param string $middleware The fully qualified class name of the middleware to use.
     * @return void
     */
    public function use(string $middleware): void;

    /**
     * Register a service in the application.
     *
     * @param string $service The fully qualified class name of the service to register.
     * @return void
     */
    public function registerService(string $service): void;

    /**
     * Bootstrap the application with the given configuration.
     *
     * @param string $config_path The path to the configuration file or directory.
     * @return void
     */
    public function bootstrap(string $config_path): void;

    /**
     * Run the application.
     *
     * @return void
     */
    public function run(): void;
}