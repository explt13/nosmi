<?php
namespace Explt13\Nosmi\Http;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Logging\Logger;
use Explt13\Nosmi\Validators\FileValidator;

class HttpErrorHandler
{
    protected ConfigInterface $config;
    protected LightServerRequestInterface $request;
    protected readonly bool $debug;

    public function __construct(LightServerRequestInterface $request)
    {
        $this->request = $request;
        $this->config = AppConfig::getInstance();
        $this->debug = $this->config->get('APP_DEBUG') ?? false;
    }


    public function exceptionHandler(\Throwable $e): LightResponseInterface
    {
        $this->logError($e->getMessage(), $e->getFile(), $e->getLine());
        return $this->generateResponse($e::class, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace(), $e->getCode());
    }

    protected function logError(string $message = '', $file = '', $line = ''): void
    {
        $logger = Logger::getInstance();
        $logger->logError("$message | File: $file | Line: $line");
    }
    
    protected function generateResponse($err_type, $err_message, $err_file, $err_line, $callstack, $err_code = 500): LightResponseInterface
    {
        if ($err_code < 100 || $err_code > 599){
            $err_code = 500;
        }

        $factory = new HttpFactory();
        $response = $factory->createResponse($err_code);
        
        if ($this->request->isAjax()) {
            if ($this->debug) {
                $response = $response->withJson(['message' => $err_message]);
            } else {
                $response = $response->withJson(['message' => $response->getReasonPhrase()]);
            }
            return $response;
        }


        $views_map = null;
        if ($this->config->has('APP_ERROR_VIEWS_MAP_FILE')) {
            /**
             * @var array $views_map
             */
            $views_map = require $this->config->get('APP_ERROR_VIEWS_MAP_FILE');
            $error_views_folder = $this->config->get('APP_ERROR_VIEWS');
        }
        
        
        if ($this->debug) {
            $file = FRAMEWORK . "/Templates/Views/Errors/dev.php";
            if (isset($views_map['dev'])) {
                $file = $error_views_folder . '/' . $views_map['dev'] . '.php';
            }
        } else {
            $file = FRAMEWORK . "/Templates/Views/Errors/$err_code.php";
            if (!FileValidator::isReadableFile($file)) {
                $file = FRAMEWORK . "/Templates/Views/Errors/error.php";
            }
            if (isset($views_map['error'])) {
                $file = $error_views_folder . '/error.php';
            }
            if (isset($views_map[$err_code])) {
                $file = $error_views_folder . '/' . $views_map[$err_code] . '.php';
            }
        }
        
        FileValidator::validateResourceIsReadable($file);
        ob_start();
        require $file;
        $html = ob_get_clean();
        return $response->withHtml($html);
    }
}