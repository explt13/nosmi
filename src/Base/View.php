<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Exceptions\ArrayNotAssocException;
use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Exceptions\InvalidRenderOptionException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\ViewInterface;
use Explt13\Nosmi\Routing\RouteContext;
use Explt13\Nosmi\Utils\Types;

class View implements ViewInterface
{
    private ConfigInterface $config;
    private LightRouteInterface $route;
    private string $layout_file;
    private array $meta;
    
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function withLayout(string $layout_file): static
    {
        $this->layout_file = $layout_file;
        return $this;
    }

    public function withMeta(string $name, string $value): static
    {
        $this->meta[$name] = $value;
        return $this;
    }

    /**
     * @param array{string: $name, string: $value} $meta_array
     */
    public function withMetaArray(array $meta_array): static
    {
        if (!Types::array_is_assoc($meta_array)) {
            throw new ArrayNotAssocException();
        }
        foreach($meta_array as $name => $value) {
            $this->meta[$name] = $value;
        }
        return $this;
    }

    public function withRoute(LightRouteInterface $route): static
    {
        $this->route = $route;
        return $this;
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
        throw new InvalidRenderOptionException($view, $render_options);
    }

    private function getContentHtml(string $view, array $data): string
    {
        $viewFile = $this->config->get('APP_VIEWS') . '/' . $this->route->controller . '/' . $view . '.php';
        if (is_file($viewFile)) {
            ob_start();
            require_once $viewFile;
            return ob_get_clean();
        } else {
            throw new FileNotFoundException($view);
        }
    }

    private function includeLayout(string $content): string
    {
        $layout = $this->config->get('DEFAULT_LAYOUT_FILE') ?? $this->layout_file;
        if (is_null($layout)) {
            throw FileNotFoundException::withMessage('Layout file is not set');
        }
        $layoutFile = $this->config->get('APP_LAYOUTS') . '/' . $layout . '.php';
        if (is_file($layoutFile)) {
            ob_start();
            require_once $layoutFile;
            return ob_get_clean();
        } else {
            throw new FileNotFoundException($this->route->layout, 500);
        }
    }
}