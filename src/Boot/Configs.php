<?php

use Dotenv\Dotenv;

    if(file_exists(__DIR__ . "./../../.env")){
        $dotenv = Dotenv::createImmutable(__DIR__ . "./../../");
        $dotenv->load();
    }

    $Connection = $_ENV['DB_CONNECTION'] ?? 'mysql';
    $Host = $_ENV['DB_HOST'] ?? 'localhost';
    $Port = $_ENV['DB_PORT'] ?? '3306';
    $DBName = $_ENV['DB_DATABASE'] ?? 'cacheer_db';
    $User = $_ENV['DB_USERNAME'] ?? 'root';
    $Password = $_ENV['DB_PASSWORD'] ?? '';


    define('CACHEER_DATABASE_CONFIG', [
        "mysql" => [
            "driver" => $Connection,
            "host" => $Host,
            "port" => $Port,
            "dbname" => $DBName,
            "username" => $User,
            "passwd" => $Password,
            "options" => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ]
        ],
        "sqlite" => [
            "driver" => "sqlite",
            "dbname" => "/caminho/para/o/banco.sqlite",
            "options" => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ]
        ],
        "pgsql" => [
            "driver" => $Connection,
            "host" => $Host,
            "port" => $Port,
            "dbname" => $DBName,
            "username" => $User,
            "passwd" => $Password,
            "options" => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ]
        ],
    ]);
