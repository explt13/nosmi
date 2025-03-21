<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Interfaces\WidgetInterface;

abstract class Widget implements WidgetInterface
{
    protected string $tpl;
    public function __construct(?string $tpl)
    {
        $widget_lowercase = strtolower(preg_replace('#.*\\\#', '', static::class));
        $tpl_path = APP . "/widgets/{$widget_lowercase}/tpl/";
        if (is_null($tpl)) {
            $this->tpl = $tpl_path . $widget_lowercase . "_tpl.php";
        } else {
            $this->tpl = $tpl_path . $tpl . '.php';
        }
    }
    public function render()
    {
        require_once $this->tpl;
    }
}