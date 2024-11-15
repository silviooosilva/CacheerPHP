<?php

const CACHEER_DATABASE_CONFIG = [
    "mysql" => [
        "driver" => "mysql",
        "host" => "localhost",
        "port" => "3306",
        "dbname" => "cacheer_db",
        "username" => "root",
        "passwd" => "123",
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
        "driver" => "pgsql",
        "host" => "localhost",
        "port" => "5432",
        "dbname" => "cacheer_db",
        "username" => "postgres",
        "passwd" => "123",
        "options" => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ],
];
