<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Interfaces\LightMiddlewareInterface;
use Explt13\Nosmi\Interfaces\LightRequestHandlerInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareDispatcher implements LightRequestHandlerInterface
{
    /**
     * @param string[] $middleware_list - the array of middleware class names
     */
    protected array $middleware_list;
    protected LightMiddlewareInterface $final_handler;

    public function __construct(array $middleware_list, LightMiddlewareInterface $final_handler)
    {
        $this->middleware_list = $middleware_list;
        $this->final_handler = $final_handler;    
    }

    public function handle(ServerRequestInterface $request): LightResponseInterface
    {
        if (empty($this->middleware_list)) {
            return $this->final_handler->process($request, $this);
        }
        
        
        $middleware_class = array_shift($this->middleware_list);
        /**
         * @var LightMiddlewareInterface $middleware
         */
        $middleware = new $middleware_class;

        return $middleware->process($request, $this);
    }
}