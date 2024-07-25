<?php

namespace App\Utils;

use Predis\Client;

class RedisAdapter
{

    private Client $client;

    public function __construct()
    {
        $this->client = new Client("tcp://weatheredis:6379");
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function get($key): array
    {
        return json_decode($this->client->get($key),1) ?? [];
    }

    public function set(string $key,string|array $value): void
    {
        if(empty($key) || empty($value)) return;
        $this->client->set($key,json_encode($value));
    }

    public function del(string $key): void
    {
        $this->client->del($key);
    }

    public function deleteByParts(array $parts): void
    {
        foreach($this->client->keys("*") as $key) {
            $delete = true;
            foreach($parts as $part) {
                if(!str_contains($key,$part)){
                    $delete = false;
                    break;
                }
            }
            if($delete) $this->client->del($key);
        }
    }

}
