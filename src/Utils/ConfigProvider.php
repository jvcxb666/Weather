<?php

namespace App\Utils;

class ConfigProvider
{

    private static self $instance;
    private static array $config;

    private function __construct()
    {
        self::$config = include "config.php";
        self::$instance = $this;
    }

    public static function getInstance(): static
    {
        if(!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    public static function getConfigVariable(string $variable): mixed
    {
        $instance = self::getInstance();
        return $instance::$config[$variable] ?? null;
    }
}
