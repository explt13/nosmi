<?php

namespace Explt13\Nosmi\Interfaces;

interface ViewInterface
{

    public function withLayout(string $layout_file): static;

    /**
     * @param string $name - the key name of the meta attribute.
     */
    public function withMeta(string $name, string $value): static;

    /**
     * @param array{string: $name, string: $value}|string[] $meta_array
     */
    public function withMetaArray(array $meta_array): static;

    public function withData(string $name, mixed $value): static;
    /**
     * @param array{string: $name, mixed: $value}|mixed[] $data_array
     */
    public function withDataArray(array $data_array): static;

    public function withRoute(LightRouteInterface $route): static;
    
    public function render(string $view, ?array $data = null): ?string;

}