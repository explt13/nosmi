<?php

namespace Explt13\Nosmi\Interfaces;

interface ViewInterface
{
    /**
     * Sets the layout file to be used for rendering the view. DEFAULT_LAYOUT_FILENAME will be used by default if set in config 
     *
     * @param string $layout_filename The filename of the layout.
     * @return static
     */
    public function withLayout(string $layout_filename): static;

    /**
     * Adds a meta tag to the view.
     *
     * @param string $name The name of the meta tag.
     * @param string $value The value of the meta tag.
     * @return static
     */
    public function withMeta(string $name, string $value): static;

    /**
     * Adds multiple meta tags to the view.
     *
     * @param array $meta_array An associative array of meta tags where the key is the name and the value is the content.
     * @return static
     */
    public function withMetaArray(array $meta_array): static;

    /**
     * Adds a single data variable to the view.
     *
     * @param string $name The name of the data variable.
     * @param mixed $value The value of the data variable.
     * @return static
     */
    public function withData(string $name, mixed $value): static;

    /**
     * Adds multiple data variables to the view.
     *
     * @param array $data_array An associative array of data variables where the key is the name and the value is the content.
     * @return static
     */
    public function withDataArray(array $data_array): static;

    /**
     * Associates a route with the view. By default the current server request route will be set
     *
     * @param LightRouteInterface $route The route to associate with the view.
     * @return static
     */
    public function withRoute(LightRouteInterface $route): static;

    /**
     * Sets immediate render option for rendering, if called a rendered content will be rendered and __not__ returned
     * 
     * @return static
     */
    public function withImmediateRender(): static;

    /**
     * Sets view file for rendering, if called specified view file will be used
     * 
     * @return static
     */
    public function withViewFile(string $viewFile): static;

    /**
     * Renders the specified view with optional data. \
     * The data for the view can be set with this method or with setData, setDataArray methods \
     * Provided data will overwrite existing value if key already presents 
     * 
     * @param string|null $view The name of the view to render.
     * @param array|null $data Optional data to pass to the view.
     * @return string|null The rendered view as a string, or null if withReturn() hasn't been called
     */
    public function render(?string $view = null, ?array $data = null): ?string;
}