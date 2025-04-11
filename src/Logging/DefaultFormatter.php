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
}