<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Logging\Logger;
use Explt13\Nosmi\Traits\SingletonTrait;

class ErrorHandler
{
    use SingletonTrait;
    protected readonly bool $debug;

    protected function __construct()
    {
        $config = AppConfig::getInstance();
        $this->debug = $config->get('APP_DEBUG');
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
        $this->logError($e->getMessage(), $e->getFile(), $e->getLine());
        $this->render($e::class, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace(), $e->getCode() >= 100 ? $e->getCode() : 500);
    }

    protected function logError(string $message = '', $file = '', $line = ''): void
    {
        $logger = Logger::getInstance();
        $logger->logError("$message | File: $file | Line: $line");
    }
    
    protected function render($err_type, $err_message, $err_file, $err_line, $callstack, $err_response = 500): void
    {
        $config = AppConfig::getInstance();
        if ($err_type === 'PDOException'){
            $err_response = 500;
        }
        http_response_code($err_response);
        
        if (isAjax()) {
            if (!$this->debug) {
                if ($err_response >= 500 && $err_response < 600) {
                    $err_message = "Operation has failed. Try again later";
                }
            }
            echo json_encode(["message" => $err_message]);
            die;
        }
        
        $views = null;
        if ($config->has('APP_ERROR_VIEWS')) {
            $views = require_once $config->get('APP_ERROR_VIEWS');
        }

        if ($this->debug) {
            if (!is_null($views) && isset($views['DEBUG'])) {
                require_once $views['DEBUG'];
            }
            require_once FRAMEWORK . "/defaultViews/errors/dev.php";

            die;
        }

        foreach($views as $code => $file) {
            if ($code === $err_response) {
                require_once $file;
                die;
            }
            require_once FRAMEWORK . "/defaultViews/errors/500.php";
        }
    }
}