<?php

use App\Kernel;
use App\Utils\ConfigProvider;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $referer = $_SERVER['HTTP_REFERER'] ?? "";
    if(str_contains($referer,ConfigProvider::getConfigVariable("front_url"))) header("Access-Control-Allow-Origin: *");
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
