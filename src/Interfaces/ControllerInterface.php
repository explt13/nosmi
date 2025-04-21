<?php
namespace Explt13\Nosmi\Interfaces;

use Explt13\Nosmi\Base\LightFactory;

interface ControllerInterface
{
    public function init(
        LightRequestInterface $request,
        LightRouteInterface $route,
        LightResponseInterface $response,
        ModelInterface $model,
        ViewInterface $view,
    );
}