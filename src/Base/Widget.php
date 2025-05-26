<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Interfaces\WidgetInterface;

abstract class Widget implements WidgetInterface
{
    protected ?string $tpl = null;

    public function render(): string
    {
        if (is_null($this->tpl)) {
            throw FileNotFoundException::withMessage('Cannot find template for widget: ' . static::class);
        }
        ob_start();
        require $this->tpl;
        return ob_get_clean();
    }
}