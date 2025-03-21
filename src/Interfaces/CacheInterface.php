<?php

namespace Explt13\Nosmi\Interfaces;

interface CacheInterface
{
    public function set($key, $data, $seconds=3600);
    public function get($key);
    public function delete($key);
}