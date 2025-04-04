<?php

namespace Tests\unit;

use Explt13\Nosmi\Logging\Log;
use Explt13\Nosmi\Logging\LogFormatterModes;
use Explt13\Nosmi\Logging\LogStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public static function logWriteProvider(): array
    {
        return [
            "correct" => ["Should be appended correctly\n", '/dependencies.log'],
            "missing_file" => ["The file was not existed\n", '/not_existed.log'],
            "missing_directory" => ["The directory was not existed\n", '/not_existed_dir/something.log'],
            "set_warning_type" => ["A WARNING type. Should be appended correctly\n", '/dependencies.log', LogStatus::WARNING],
            "set_error_type" => ["An ERROR type. Should be appended correctly\n", '/dependencies.log', LogStatus::ERROR],
        ];
    }

    #[DataProvider('logWriteProvider')]
    public function testLogWriteInfo($message, $dest, $type = LogStatus::INFO)
    {
        $path_prepend = __DIR__ . '/logs';
        $logger = new Log();
        $logger->log($message, $path_prepend . $dest, $type);
        $logger->log($message, $path_prepend . $dest, $type, LogFormatterModes::Verbose);
        $this->assertTrue(true);
    }
}
