<?php

namespace Explt13\Nosmi\Logging;

use Explt13\Nosmi\AppConfig\AppConfig;

class FrameworkLogger extends Logger
{
    protected function __construct()
    {
        $this->formatter = new DefaultFormatter();
        $this->log_enabled = true;
    }
}