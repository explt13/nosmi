<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Logging\FrameworkLogger;
use Explt13\Nosmi\Logging\Logger;
use Explt13\Nosmi\Traits\SingletonTrait;
use Explt13\Nosmi\Validators\FileValidator;

class ErrorHandler
{
    protected ConfigInterface $config;
    protected readonly bool $debug;

    public function __construct()
    {
        $this->config = AppConfig::getInstance();
        $this->debug = $this->config->get('APP_DEBUG') ?? false;
        if ($this->debug) {
            error_reporting(E_ALL);
            set_error_handler([$this, 'errorHandler'], E_NOTICE | E_WARNING);
        } else {
            ini_set('display_errors', 0);
            error_reporting(0);
        }

        set_exception_handler([$this, 'exceptionHandler']);
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno === E_WARNING || $errno === E_NOTICE) {
            throw new \Exception("Custom Error: $errstr in $errfile on line $errline", 500);
        }
    }

    public function exceptionHandler(\Throwable $e): void
    {
        $this->logError($e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
        $this->render($e::class, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace(), $e->getCode());
    }

    protected function logError(string $message = '', $file = '', $line = '', $code = ''): void
    {
        $logger = FrameworkLogger::getInstance();
        $log_dest = $this->config->get('LOG_FRAMEWORK_FILE') ?? $this->config->get('LOG') . '/framework.log';

        $logger->logError("[code: $code] $message | File: $file | Line: $line", null, $log_dest);
    }
    
    protected function render($err_type, $err_message, $err_file, $err_line, $callstack, $err_code = 500): void
    {
        $response_code = $err_code;
        if ($err_code < 100 || $err_code > 599 || !is_int($err_code)) {
            $response_code = 500;
        }
        $views_map = null;
        if ($this->config->has('APP_ERROR_VIEWS_MAP_FILE')) {
            $views_map = require $this->config->get('APP_ERROR_VIEWS_MAP_FILE');
            $error_views_folder = $this->config->get('APP_ERROR_VIEWS');
        }

        http_response_code($response_code);
        header('Content-Type: text/html; charset=utf-8');
        if ($this->debug) {
            if (!isset($views_map['dev'])) {
                $file = FRAMEWORK . "/Templates/Views/Errors/dev.php";
            } else {
                $file_name = $views_map['dev'];
                $file = $error_views_folder . "/" . $file_name . ".php";
            }
        } else {
            if (!isset($views_map['internal'])) {
                $file = FRAMEWORK . "/Templates/Views/Errors/error.php";
            } else {
                $file_name = $views_map['internal'];
                $file = $error_views_folder . '/' . $file_name . '.php';
            }
        }
        require $file;
        exit;
    }
}