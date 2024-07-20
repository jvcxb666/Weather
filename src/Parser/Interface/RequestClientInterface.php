<?php

namespace App\Parser\Interface;

interface RequestClientInterface
{
    public function sendRequest( string $url,string $method = "GET", array $data = [] ): string|false;
}
