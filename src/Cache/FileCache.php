<?php
namespace Explt13\Nosmi\Cache;

use Explt13\Nosmi\Interfaces\CacheInterface;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Traits\SingletonTrait;
use Explt13\Nosmi\Validators\FileValidator;

class FileCache implements CacheInterface
{
    protected ConfigInterface $config;
    private string $cache_folder;
    private string $cache_ext;


    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->cache_folder = $this->config->get('FILE_CACHE_FOLDER');
        $this->cache_ext = $this->config->get('FILE_CACHE_EXT');
    }

    public function set(string $key, $data, int $expires = 3600): bool
    {
        if ($expires <= 0) {
            throw new \RuntimeException("Cannot set cache $key with $expires seconds");
        }
        $content = [
            'data' => $data,
            'exp' => time() + $expires,
        ];
        if (file_put_contents($this->getFilePath($key), serialize($content))) {
            return true;
        }
        return false;
    }

    public function update(string $key, $data): bool
    {
        if (!$this->has($key)) {
            return false;
        }
        $file_name = $this->getFilePath($key);
        $content = unserialize(file_get_contents($file_name));
        $content['data'] = $data;
        if (file_put_contents($this->getFilePath($key), serialize($content))) {
            return true;
        }
        return false;
    }
    
    public function get(string $key)
    {
        if (!$this->has($key)) {
            return null;
        }
        $file_name = $this->getFilePath($key);
        $content = unserialize(file_get_contents($file_name));
        return $content['data'];
    }

    public function delete(string $key): bool
    {
        $file_name = $this->getFilePath($key);
        if (FileValidator::resourceExists($file_name)) {
            unlink($file_name);
            return true;
        }
        return false;
    }

    public function has(string $key): bool
    {
        $file_name = $this->getFilePath($key);
        if (!FileValidator::resourceExists($file_name)) {
            return false;
        }
        $content = unserialize(file_get_contents($file_name));
        if ($content['exp'] < time()) {
            unlink($file_name);
            return false;
        }
        return true;
    }

    public function expire(string $key, int $seconds): bool
    {
        if (!$this->has($key)) {
            return false;
        }
        $file_name = $this->getFilePath($key);
        $content = unserialize(file_get_contents($file_name));
        $exp = $content['exp'] + $seconds;
        $content['exp'] = $exp;
        file_put_contents($file_name, serialize($content));
        return true;
    }

    public function getTtl(string $key): int
    {
        if (!$this->has($key)) {
            return -2;
        }
        $file_name = $this->getFilePath($key);
        $content = unserialize(file_get_contents($file_name));
        return $content['exp'] - time();
    }

    private function getFilePath(string $file_name): string
    {
        return $this->cache_folder . "/" . $this->hashKey($file_name) . '.' . $this->cache_ext;
    }

    private function hashKey(string $key): string
    {
        $hash_function = $this->config->get('FILE_CACHE_HASH');
        return match ($hash_function) {
            "md5" => md5($key),
            "sha1" => sha1($key),
        };
    }
}