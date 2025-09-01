<?php

use Dotenv\Dotenv;
use Silviooosilva\CacheerPhp\Core\Connect;
use Silviooosilva\CacheerPhp\Helpers\EnvHelper;
use Silviooosilva\CacheerPhp\Helpers\SqliteHelper;


$rootPath = EnvHelper::getRootPath();
$dotenv = Dotenv::createImmutable($rootPath);
$dotenv->load();


$Connection = $_ENV['DB_CONNECTION'] ?? 'mysql';
$Host       = $_ENV['DB_HOST'] ?? 'localhost';
$Port       = $_ENV['DB_PORT'] ?? '3306';
$DBName     = $_ENV['DB_DATABASE'] ?? 'cacheer_db';
$User       = $_ENV['DB_USERNAME'] ?? 'root';
$Password   = $_ENV['DB_PASSWORD'] ?? '';

// Retrieve Redis environment variables
$redisClient    = $_ENV['REDIS_CLIENT'] ?? '';
$redisHost      = $_ENV['REDIS_HOST'] ?? 'localhost';
$redisPassword  = $_ENV['REDIS_PASSWORD'] ?? '';
$redisPort      = $_ENV['REDIS_PORT'] ?? '6379';
$redisNamespace = $_ENV['REDIS_NAMESPACE'] ?? '';
$cacheTable     = $_ENV['CACHEER_TABLE'] ?? 'cacheer_table';

Connect::setConnection($Connection);

// Database configuration array
define('CACHEER_DATABASE_CONFIG', [
    "mysql" => [
        "driver"  => $Connection,
        "host"    => $Host,
        "port"    => $Port,
        "dbname"  => $DBName,
        "username"=> $User,
        "passwd"  => $Password,
        "options" => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE               => PDO::CASE_NATURAL
        ]
    ],
    "sqlite" => [
        "driver"  => $Connection,
        "dbname"  => SqliteHelper::database(),
        "options" => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE               => PDO::CASE_NATURAL
        ]
    ],
    "pgsql" => [
        "driver"  => $Connection,
        "host"    => $Host,
        "port"    => $Port,
        "dbname"  => $DBName,
        "username"=> $User,
        "passwd"  => $Password,
        "options" => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE               => PDO::CASE_NATURAL
        ]
    ],
]);

// Redis configuration array
define('REDIS_CONNECTION_CONFIG', [
    'REDIS_CLIENT'   => $redisClient,
    'REDIS_HOST'     => $redisHost,
    'REDIS_PASSWORD' => $redisPassword,
    'REDIS_PORT'     => $redisPort,
    'REDIS_NAMESPACE'=> $redisNamespace
]);

// Cache table name for database driver
if (!defined('CACHEER_TABLE')) {
    define('CACHEER_TABLE', $cacheTable);
}
