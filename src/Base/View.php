<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\ViewInterface;
use Explt13\Nosmi\Validators\FileValidator;

class View implements ViewInterface
{
    private ConfigInterface $config;
    private ?LightRouteInterface $route = null;
    private ?string $layout_filename = null;
    private ?string $view_filename = null;
    private array $meta = [];
    private array $data = [];
    private bool $include_layout;
    private bool $immediate_render = false;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->include_layout = $this->config->get('INCLUDE_LAYOUT_BY_DEFAULT');
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

    public function withImmediateRender(): static
    {
        $this->immediate_render = true;
        return $this;
    }

    public function withViewFile(string $viewFile): static
    {
        $this->view_filename = $viewFile;
        return $this;
    }

    public function render(?string $view = null, ?array $data = null): ?string
    {
        if (!is_null($view)) {
            $this->view_filename = $view;
        }
        if (is_null($this->view_filename)) {
            throw new \RuntimeException("View file is not specified for View class, provide view parameter or use View::withViewFile method to set one");
        }
        if (is_null($this->route)) {
            throw new \RuntimeException("Route is not set for View class, use View::withRoute method to set route");
        }

        if (!is_null($data)) {
            foreach($data as $name => $value) {
                $this->data[$name] = $value;
            }
        }
        if ($this->immediate_render) {
            echo $this->getView();
            return null;
        }
        ob_start();
        $this->getView();
        return ob_get_clean();
    }

    private function getView():void
    {
        if ($this->include_layout) {
            $this->includeLayout(function() {
                $this->getContentHtml();
            });
        } else {
            $this->getContentHtml();
        }
    }

    private function getContentHtml(): void
    {
        if (!preg_match("/\\\\([a-zA-Z_][a-zA-Z0-9_]+?)(Controller)?$/", $this->route->getController(), $matches)) {
            throw new \RuntimeException('Cannot extract controller name from class name: ' . $this->route->getController());
        }
        $controller = $matches[1];
        $view_folder = $this->config->get('APP_VIEWS');
        $view_file =  $view_folder . '/' . $controller . '/' . $this->view_filename . '.php';
        if (FileValidator::isFile($view_file)) {
            require $view_file;
        } else {
            ob_end_clean();
            throw new FileNotFoundException($view_file);
        }
    }

    private function includeLayout(callable $contentCallback): void
    {
        if (is_null($this->layout_filename)) {
            throw FileNotFoundException::withMessage('Layout file is not set');
        }
        $layout_folder = $this->config->get('APP_LAYOUTS');
        $layout_file =  $layout_folder . '/' . $this->layout_filename . '.php';
        if (FileValidator::isFile($layout_file)) {
            require $layout_file;
        } else {
            throw new FileNotFoundException($layout_file, 500);
        }
    }
}