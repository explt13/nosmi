<?php

namespace Tests\Integration\Db;
use PHPUnit\Framework\TestCase;
use Explt13\Nosmi\Base\Db;
use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Exceptions\DatabaseConnectionException;
use Tests\Unit\helpers\Reset;

class DbTest extends TestCase
{
    protected function setUp(): void
    {

        // Set up the configuration for the database
        $config = AppConfig::getInstance();
        $config->set('DB_DRIVER', 'mysql');
        $config->set('DB_HOSTNAME', '127.0.0.1');
        $config->set('DB_PORT', '3306');
        $config->set('DB_NAME', 'test_db');
        $config->set('DB_CHARSET', 'utf8mb4');
        $config->set('DB_USERNAME', 'root');
        $config->set('DB_PASSWORD', '123');
    }
    
    // public static function tearDownAfterClass(): void
    // {
    //     Reset::resetSingleton(AppConfig::class);
    // }

    public function testDbConnectionSuccess(): void
    {
        $db = Db::getInstance();

        // Invalid credentials provided for DB_USERNAME and DB_PASSWORD
        $this->expectException(DatabaseConnectionException::class);
        $db->connect();
        $connection = $db->getConnection();
        $this->assertInstanceOf(\PDO::class, $connection);
        
    }

    public function testDbConnectionFailure(): void
    {
        $config = AppConfig::getInstance();
        $config->set('DB_PASSWORD', 'wrong_password'); // Set an incorrect password

        $db = Db::getInstance();

        $this->expectException(DatabaseConnectionException::class);
        $db->connect();
    }
}