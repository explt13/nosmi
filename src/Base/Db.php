<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Exceptions\DatabaseConnectionException;
use Explt13\Nosmi\Traits\SingletonTrait;

class Db{

    use SingletonTrait;

    protected \PDO $connection;

    public function connect(array $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_TIMEOUT => 5,
    ]): void
    {

        $config = AppConfig::getInstance();
        $db_driver = $config->get('DB_DRIVER');
        $db_hostname = $config->get('DB_HOSTNAME');
        $db_port = $config->get('DB_PORT');
        $db_name = $config->get('DB_NAME');
        $db_charset = $config->get('DB_CHARSET');
        $db_username = $config->get('DB_USERNAME');
        $db_password = $config->get('DB_PASSWORD');

        try {
            $dsn = sprintf("%s:host=%s;%sdbname=%s;charset=%s", 
                $db_driver,
                $db_hostname,
                $db_port === null ? "" : "port=$db_port;",
                $db_name,
                $db_charset
            );

            $this->connection = new \PDO(
                $dsn,
                $db_username,
                $db_password,
                $options
            );
        } catch (\PDOException $e) {
            throw DatabaseConnectionException::withMessage("Can not connect to the database: " . $e->getMessage());
        }
    }

    public function getConnection(): \PDO {
        return $this->connection;
    }
}