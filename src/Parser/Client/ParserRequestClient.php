<?php

namespace App\Parser\Client;

use App\Parser\Interface\RequestClientInterface;

class ParserRequestClient implements RequestClientInterface
{

    public function sendRequest( string $url,string $method = "GET", array $data = [] ): string|false
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST ,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER ,false);

        if($method == "POST") {
            curl_setopt($ch, CURLOPT_POST,true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query($data));
        }
    
        $response = curl_exec($ch);
    
        if(curl_errno($ch) !== 0) {
            return false;
        }
    
        return $response;
    }
}