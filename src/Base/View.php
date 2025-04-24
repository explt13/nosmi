<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\ViewInterface;

class View implements ViewInterface
{
    private ConfigInterface $config;
    private LightRouteInterface $route;
    private ?string $layout_filename = null;
    private array $meta = [];
    private array $data = [];
    private bool $include_layout;
    private bool $return = false;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->include_layout = $this->config->get('INCLUDE_LAYOUT_BY_DEFAULT') ?? false;
        $this->layout_filename = $this->config->get('DEFAULT_LAYOUT_FILENAME');
    }

    public function withLayout(string $layout_filename): static
    {
        $this->layout_filename = $layout_filename;
        $this->include_layout = true;
        return $this;
    }

    public function withMeta(string $name, string $value): static
    {
        if (is_null($name)) {
            $this->meta[] = $value;
        } else {
            $this->meta[$name] = $value;
        }
        return $this;
    }
    public function withMetaArray(array $meta_array): static
    {
        foreach($meta_array as $name => $value) {
            $this->meta[$name] = $value;
        }
        return $this;
    }

    public function withData(string $name, mixed $value): static
    {
        $this->data[$name] = $value;
        return $this;
    }

    public function withDataArray(array $data_array): static
    {
        foreach($data_array as $name => $value) {
            $this->data[$name] = $value;
        }
        return $this;
    }

    public function withRoute(LightRouteInterface $route): static
    {
        $this->route = $route;
        return $this;
    }

    public function withReturn(): static
    {
        $this->return = true;
        return $this;
    }

    public function render(string $view, ?array $data = null): ?string
    {
        if (!is_null($data)) {
            foreach($data as $name => $value) {
                $this->data[$name] = $value;
            }
        }
        if ($this->return) {
            ob_start();
            $this->getView($view);
            return ob_get_clean();
        }
        echo $this->getView($view);
        return null;
    }

    private function getView($view):void
    {
        if ($this->include_layout) {
            $this->includeLayout(function() use ($view) {
                $this->getContentHtml($view);
            });
        } else {
            $this->getContentHtml($view);
        }
    }

    private function getContentHtml(string $view): void
    {
        $viewFile = $this->config->get('APP_VIEWS') . '/' . $this->route->getController() . '/' . $view . '.php';
        if (is_file($viewFile)) {
            require $viewFile;
        } else {
            throw new FileNotFoundException($view);
        }
    }

    private function includeLayout(callable $contentCallback): void
    {
        if (is_null($this->layout_filename)) {
            throw FileNotFoundException::withMessage('Layout file is not set');
        }
        $layoutFile = $this->config->get('APP_LAYOUTS') . '/' . $this->layout_filename . '.php';
        if (is_file($layoutFile)) {
            require $layoutFile;
        } else {
            throw new FileNotFoundException($layoutFile, 500);
        }
    }
}