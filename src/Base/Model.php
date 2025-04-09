<?php
namespace Explt13\Nosmi\Base;

abstract class Model
{
    protected array $attributes = [];
    protected array $errors = [];
    protected array $rules = [];
    protected \PDO $pdo;

    public function __construct()
    {
        $connection = Db::getInstance();
        $this->pdo = $connection->getPDO();
    }
}