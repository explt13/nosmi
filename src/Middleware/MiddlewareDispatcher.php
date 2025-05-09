<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\LightMiddlewareInterface;
use Explt13\Nosmi\Interfaces\LightRequestHandlerInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareDispatcher implements LightRequestHandlerInterface
{
    /**
     * @param string[] $middleware_list the array of middleware class names
     */
    protected array $middleware_list;
    protected ControllerInterface $controller;

    public function __construct(array $middleware_list, ControllerInterface $controller)
    {
        $this->middleware_list = $middleware_list;
        $this->controller = $controller;    
    }

    public function handle(ServerRequestInterface $request): LightResponseInterface
    {
        if (empty($this->middleware_list)) {
            return $this->controller->processRequest($request);
        }
        
         /**
         * @var LightMiddlewareInterface $middleware
         */
        $middleware = array_shift($this->middleware_list);
        
        return $middleware->process($request, $this);
    }
}