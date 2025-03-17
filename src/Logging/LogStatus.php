<?php

namespace Explt13\Nosmi\Logging;

enum LogStatus: string
{
    case INFO = 'I';
    case WARNING = 'W';
    case ERROR = 'E';
}