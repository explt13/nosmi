<?php

namespace Explt13\Nosmi\Interfaces;

interface ViewInterface
{
    /**
     * RENDER_AJAX return *content html* as a string, so it can be passed to the frontend
     */
    public const RENDER_AJAX = 1;
    
    /**
     * RENDER_SSR return null, echoes *content html* to the browser
     */
    public const RENDER_SSR = 2;

    /**
     * INCLUDE_LAYOUT includes layout, by default is enabled for RENDER_SSR
     */
    public const INCLUDE_LAYOUT = 4;

    public function render(string $view, array $data, int $render_options = self::RENDER_SSR | self::INCLUDE_LAYOUT): string|null;
}