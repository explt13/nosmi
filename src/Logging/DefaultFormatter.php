<?php

namespace Explt13\Nosmi\Logging;

use Explt13\Nosmi\Interfaces\LogFormatterInterface;

class DefaultFormatter implements LogFormatterInterface
{

    public function format(array $log): string
    {
        $message = "[{$log['status']->value}] [" . date('d-m-Y h:i:s A') . "]" . " {$log['status']->name}: {$log['message']}";
        $message .= str_repeat('-', 128);
        $message .= "\n\n";
        return $message;
    }


    private function formatVerbose(array $log): string
    {
        ob_start();
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $backtrace = ob_get_clean();
        $message = "[{$log['status']->value}] [" . date('d-m-Y h:i:s A') . "]" . " {$log['status']->name}: {$log['message']}\n";
        $message .= "BACKTRACE:\n" . $backtrace;
        $message .= str_repeat('-', 128);
        $message .= "\n\n";
        return $message;
    }
}