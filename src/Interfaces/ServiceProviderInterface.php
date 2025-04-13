<?php

namespace Explt13\Nosmi\Interfaces;

interface ServiceProviderInterface
{
    /**
     * Boot services after registration.
     *
     * @return void
     */
    public function boot(): void;
}