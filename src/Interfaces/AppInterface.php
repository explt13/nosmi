<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface AppInterface
{
    /**
     * Use a middleware in the application.
     *
     * @param MiddlewareInterface $middleware The middleware to use.
     * @return static
     */
    public function use(MiddlewareInterface $middleware): static;

    /**
     * Bootstrap the application with the given configuration.
     *
     * @param string $config_path The path to the configuration file or directory.
     * @return static
     */
    public function bootstrap(string $config_path): static;

    /**
     * Run the application.
     *
     * @return void
     */
    public function run(): void;
}