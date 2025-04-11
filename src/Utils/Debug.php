<?php

namespace Explt13\Nosmi\Utils;

final class Debug
{
    static function printReadable(mixed $arr): void
    {
        echo "<pre>" . print_r($arr, 1) . "</pre>";
    }

    static function printIncludedFile(): void
    {
        $backtrace = debug_backtrace();
        foreach ($backtrace as $trace) {
            if (isset($trace['file'])) {
                echo "<div style='color: green; font-size: 28px'>Included in {$trace['file']} on line {$trace['line']}</div><br/>";
            }
        }
    }

}