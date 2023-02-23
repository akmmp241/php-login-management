<?php

function getDatabaseConfig(): array
{
    return [
        "database" => [
            "test" => [
                "url" => "mysql:host=localhost:3306;dbname=php_login_management_test",
                "username" => "root",
                "password" => "root"
            ],
            "production" => [
                "url" => "mysql:host=localhost:3306;dbname=php_login_management_test",
                "username" => "root",
                "password" => "root"
            ]
        ]
    ];
}

function getCSS(): string
{
    return "http://" . $_SERVER['SERVER_NAME'] . "/assets/";
}