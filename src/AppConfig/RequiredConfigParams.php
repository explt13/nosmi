<?php

namespace Explt13\Nosmi\AppConfig;

use Explt13\Nosmi\Exceptions\ConfigParameterNotSetException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Validators\FileValidator;

enum RequiredConfigParams: string
{
    case APP_ROOT = "FOLDER";
    case APP_SRC = "FOLDER";
    case APP_VIEWS = "FOLDER";
    case APP_ERROR_VIEWS = "FOLDER";
    case APP_LAYOUTS = "FOLDER";
    case APP_ROUTES_FILE = "FILE";
    case APP_DEPENDENCIES_FILE = "FILE";
    case APP_DEBUG = "BOOL";

    public function validateParam(ConfigInterface $config)
    {
        if (!$config->has($this->name)) {
            throw new ConfigParameterNotSetException($this->name);
        }
        $param = $config->get($this->name);
        switch ($this->value) {
            case "FOLDER":
                FileValidator::validateDirIsReadable($param);
                break;
            case "FILE":
                FileValidator::validateFileIsReadable($param);
                break;
        }
    }
}