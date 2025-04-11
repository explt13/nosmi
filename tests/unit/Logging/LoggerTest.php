<?php

namespace Tests\unit;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Logging\DefaultFormatter;
use Explt13\Nosmi\Logging\Logger;
use Explt13\Nosmi\Logging\LogStatus;
use Explt13\Nosmi\Logging\VerboseFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public static function logWriteProvider(): array
    {
        return [
            "correct" => ["Should be appended correctly\n", '/dependencies.log'],
            "missing_file" => ["The file was not existed\n", '/not_existed.log'],
            "missing_directory" => ["The directory was not existed\n", '/not_existed_dir/something.log'],
            "set_warning_type" => ["A WARNING type. Should be appended correctly\n", '/dependencies.log'],
            "set_error_type" => ["An ERROR type. Should be appended correctly\n", '/dependencies.log'],
        ];
    }

    public static function logEnvInvalidDataProvider(): array
    {
        return [
            "empty folder" => [
                "Sample message",
                "",
                "app.log"
            ],
            "empty file" => [
                "Sample message",
                __DIR__ .'/env',
                ""
            ],
            "empty folder and file" => [
                "Sample message",
                "",
                ""
            ]
        ];
    }

    #[DataProvider('logWriteProvider')]
    public function testLogWrite($message, $dest)
    {
        $path = __DIR__ . '/logs' . $dest;
        $logger = Logger::getInstance();
        $logger->setFormatter(new VerboseFormatter());
        $logger->logInfo($message, null, $path);
        $this->assertTrue(true);
    }

    #[DataProvider('logWriteProvider')]
    public function testEnvVarsLogWrite($message, $dest)
    {
        $config = AppConfig::getInstance();
        $logger = Logger::getInstance();
        $logger->setFormatter(new DefaultFormatter());
        $config->set('LOG_FOLDER', __DIR__.'/env_set_folder');
        $config->set('LOG_FILE_WARNING', 'warning.log');
        $config->set('LOG_FILE_INFO', 'warning.log');
        $logger->logInfo($message);
        $logger->logWarning($message);
        $this->assertTrue(true);
    }
    #[DataProvider('logEnvInvalidDataProvider')]
    public function testEnvInvalidDataLogWrite($message, $folder, $file)
    {
        $config = AppConfig::getInstance();
        $logger = Logger::getInstance();

        $this->expectException(\Exception::class);

        $config->set('LOG_FOLDER', $folder);
        $config->set('LOG_FILE_WARNING', $file);
        $config->set('LOG_FILE_INFO', $file);
        $config->set('LOG_FILE_ERROR', $file);
        $logger->logInfo($message);
        $logger->logWarning($message);
        $logger->logError($message);
    }

    public function testChangeFormatter()
    {
        $message = "sample log message";
        $path = __DIR__ . '/formatter_logs';
        $logger = Logger::getInstance();
        $logger->setFormatter(new DefaultFormatter());
        $logger->setFormatter(new VerboseFormatter(), LogStatus::WARNING);

        $logger->logInfo($message, null, $path . '/info.log');
        $logger->logError($message, new VerboseFormatter(), $path . '/error.log');
        $logger->logWarning($message, null, $path . '/warning.log');
        $logger->logWarning($message, null, $path . '/warning.log');
        $logger->logError($message, new VerboseFormatter(), $path . '/error.log');
        $logger->logInfo($message, null, $path . '/info.log');
        

        $this->assertTrue(true);
    }
}
