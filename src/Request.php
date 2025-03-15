<?php

namespace Explt13\Nosmi;

class Request
{
    protected array $query_params;
    protected ?array $post_data;
    protected array $headers;
    public bool $isAjax;

    public function __construct()
    {
        $this->headers = getallheaders();
        $this->query_params = $_GET;
        $this->isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    protected function setPostData(): void
    {
        if (!isset($this->headers['Content-Type']) || $this->headers['Content-Type'] === 'application/json') {
            $this->post_data = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON received: " . json_last_error_msg(), 500);
            }
        } else if (($this->headers['Content-Type'] === 'application/x-www-form-urlencoded') || 
                  (preg_match("#multipartform-data; boundary=.*#", $this->headers['Content-Type']))) {
            $this->post_data = $_POST;
        }
    }

    /**
     * if not Content Type header present -
     * JSON format will be used by default.
     */

    public function getPostDataValue(string $key, $default = null): mixed
    {
        $this->setPostData();
        return $this->post_data[$key] ?? $default;
    }

    public function getPostData(): array
    {
        $this->setPostData();
        return $this->post_data;
    }
    public function getQueryParams(): array
    {
        return $this->query_params;
    }

    public function getQueryParam(string $key, $default = null): mixed
    {
        return $this->query_params[$key] ?? $default;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}