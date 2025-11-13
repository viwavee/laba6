<?php

namespace App;

use App\Helpers\ClientFactory;

class RedisExample
{
    private $client;

    public function __construct()
    {
        $this->client = ClientFactory::make('http://localhost:7379/'); // redis-commander proxy
    }

    public function setValue($key, $value)
    {
        $response = $this->client->get("SET/$key/$value");
        return $response->getBody()->getContents();
    }

    public function getValue($key)
    {
        $response = $this->client->get("GET/$key");
        return $response->getBody()->getContents();
    }
}
