<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Interfaces\WidgetInterface;

abstract class Widget implements WidgetInterface
{
    private ?string $tpl = null;
    public function __construct(string $path_to_tpl)
    {
        $this->tpl = $path_to_tpl;
    }

    public function render(): string
    {
        if (is_null($this->tpl)) {
            throw FileNotFoundException::withMessage('Cannot find template for widget: ' . static::class);
        }
        ob_start();
        require_once $this->tpl;
        return ob_get_clean();
    }
}