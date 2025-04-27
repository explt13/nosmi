<?php
namespace Explt13\Nosmi\Interfaces;


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