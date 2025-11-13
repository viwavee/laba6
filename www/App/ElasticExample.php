<?php

namespace App;

use App\Helpers\ClientFactory;

class ElasticExample
{
    private $client;

    public function __construct()
    {
        // Используем имя сервиса elasticsearch в docker-compose
        $this->client = ClientFactory::make('http://elasticsearch:9200/');
    }

    public function indexDocument($index, $id, $data)
    {
        $response = $this->client->put("$index/_doc/$id", [
            'json' => $data
        ]);
        return $response->getBody()->getContents();
    }

    public function search($index, $query)
    {
        // Elasticsearch ожидает тело запроса — используем POST с JSON
        $response = $this->client->post("$index/_search", [
            'json' => ['query' => ['match' => $query]]
        ]);
        return $response->getBody()->getContents();
    }
}
