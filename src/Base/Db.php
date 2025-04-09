<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Traits\SingletonTrait;

class Db{
    use SingletonTrait;
    private \PDO $connection;
    protected function __construct()
    {
        $db = require_once CONF . '/db_connection.php';
        try {
            $this->connection = new \PDO($db['dsn'], $db['user'], $db['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 5,
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Can not connect to the database: " . $e->getMessage(), 500);
        }
    }
    public function getPDO(): \PDO {
        return $this->connection;
    }
}