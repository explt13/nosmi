<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Interfaces\ModelInterface;

abstract class Model implements ModelInterface
{
    protected array $attributes = [];
    protected array $errors = [];
    protected array $rules = [];
    protected \PDO $pdo;

    public function __construct()
    {
        $connection = Db::getInstance();
        $this->pdo = $connection->getConnection();
    }
}