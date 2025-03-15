<?php
namespace Explt13\Nosmi;

class ErrorHandler
{
    use SingletonTrait;

    private function __construct()
    {
        if (DEBUG) {
            error_reporting(E_ALL);
            set_error_handler([$this, 'errorHandler'], E_NOTICE | E_WARNING);
        } else {
            ini_set('display_errors', 0);
            error_reporting(0);
        }

        set_exception_handler([$this, 'exceptionHandler']);
    }

    public function errorHandler($errno, $errstr, $errfile, $errline) {
        if ($errno === E_WARNING || $errno === E_NOTICE) {
            throw new \Exception("Custom Error: $errstr in $errfile on line $errline", 500);
        }
    }

    public function exceptionHandler(\Throwable $e)
    {
        $this->logError($e->getMessage(), $e->getFile(), $e->getLine());
        $this->render($e::class, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace(), $e->getCode() >= 100 ? $e->getCode() : 500);
    }

    private function logError(string $message = '', $file = '', $line = '')
    {
        error_log("[" . date("Y-m-d H:i:s P") . "] Error: {$message} | File: {$file} | Line: {$line}
                    \n--------------------------------------\n", 3, ROOT . '/tmp/errors.log');
    }
    
    private function render($err_type, $err_message, $err_file, $err_line, $callstack, $err_response = 500)
    {
        if ($err_type === 'PDOException'){
            $err_response = 500;
        }
        http_response_code($err_response);
        
        if (isAjax()) {
            if (!DEBUG) {
                if ($err_response >= 500 && $err_response < 600) {
                    $err_message = "Operation has failed. Try again later";
                }
            }
            echo json_encode(["message" => $err_message]);
            die;
        }

        $view_path = APP . '/views/errors';
        if (!DEBUG) {
            switch ($err_response) {
                case 404:
                    require_once $view_path . "/404.php";
                    break;
                default:
                    require_once $view_path . "/500.php";
            };
        } else {
            require_once $view_path . "/dev.php";
        }
    }
}