<?php
namespace Explt13\Nosmi\base;

use Explt13\Nosmi\RouteContext;

class View
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
    
    private RouteContext $route;
    private array $meta = ["title" => "", "description" => "", "keywords" => ""]; // app/views/layouts/meta.php
    
    public function __construct(RouteContext $route)
    {
        $this->route = $route;
    }
    
    public function render(string $view, array $data, int $render_options = self::RENDER_SSR | self::INCLUDE_LAYOUT): string|null
    {
        $content = $this->getContentHtml($view, $data);
        if ($render_options === self::RENDER_AJAX) {
            return $content;
        }
        if ($render_options === (self::RENDER_AJAX | self::INCLUDE_LAYOUT)){
            return $this->includeLayout($content);
        }
        if ($render_options === self::RENDER_SSR) {
            echo $content;
            return null;
        }
        if ($render_options === (self::RENDER_SSR | self::INCLUDE_LAYOUT)) {
            echo $this->includeLayout($content);
            return null;
        }
        return null;
    }

    private function getContentHtml(string $view, array $data): string
    {
        $viewFile = APP . "/views/" . $this->route->prefix .  $this->route->controller . '/' . $view . '.php';
        if (is_file($viewFile)) {
            extract($data, EXTR_SKIP);
            ob_start();
            require_once $viewFile;
            return ob_get_clean();
        } else {
            throw new \Exception("View not found: {$view}", 500);
        }
    }

    private function includeLayout(string $content): string
    {
        $layoutFile = APP . "/views/layouts/" . $this->route->layout . '.php';
        if (is_file($layoutFile)) {
            ob_start();
            require_once $layoutFile;
            return ob_get_clean();
        } else {
            throw new \Exception("Layout not found: {$this->route->layout}", 500);
        }
    }

    public function setMeta(?string $title, ?string $description = null, ?string $keywords = null): void
    {
        $this->meta['title'] = $title;
        $this->meta['description'] = $description;
        $this->meta['keywords'] = $keywords;
    }
}