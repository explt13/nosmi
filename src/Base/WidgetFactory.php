<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Interfaces\WidgetInterface;

class WidgetFactory
{
    /**
     * Creates widget from classname
     * @param string $widget_classname The classname or interface name of the widget
     * @return WidgetInterface
     */
    function create(string $widget_classname): WidgetInterface
    {
        $container = Container::getInstance();
        $widget = $container->get($widget_classname);
        return $widget;
    }
}