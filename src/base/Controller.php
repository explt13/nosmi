<?php
namespace Explt13\Nosmi\base;

use Explt13\Nosmi\interfaces\CacheInterface;
use Explt13\Nosmi\Request;
use Explt13\Nosmi\RouteContext;

abstract class Controller
{
    protected RouteContext $route;
    protected CacheInterface $cache;
    private View $view;
    protected Request $request;

    public final function init(RouteContext $route, Request $request, View $view): void
    {
        $this->route = $route;
        $this->request = $request;
        $this->view = $view;
    }

    /**
     * Sets view and data
     * @param null|string $view if not $view passed, RouteContext::$route['action'] will be set
     * @param array $data associative array, where key is variable and value result, $data['products'] => ['Product 1'...]
     * @param int $render_options Flags to be set for desired render, default - View::RENDER_SSR | View::INCLUDE_LAYOUT
     */
    public function render(?string $view = null, array $data = [], int $render_options = View::RENDER_SSR | View::INCLUDE_LAYOUT)
    {
        if (is_null($view)) {
            $view = $this->route->action;
        }
        $this->view->render($view, $data, $render_options);
    }
    public function getAjaxHtml(string $view, array $data = [], array $json_payload = [], int $render_options = View::RENDER_AJAX): string
    {
        $html = $this->view->render($view, $data, $render_options);
        if (is_null($html)){
            throw new \Exception('Cannot get html', 404);
        }
        return $html;
    }
    
    protected function setMeta(?string $title, ?string $description = null, ?string $keywords = null): void
    {
        $this->view->setMeta($title, $description, $keywords);
    }
}