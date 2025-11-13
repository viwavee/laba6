<?php

require 'vendor/autoload.php';

use App\RedisExample;
use App\ElasticExample;
use App\ClickhouseExample;

echo "=== Lab6: Redis, Elasticsearch, ClickHouse Examples ===\n\n";

// Redis
echo "[Redis] Setting and getting value:\n";
try {
    $redis = new RedisExample();
    $setResult = $redis->setValue('user:101', json_encode(['name' => 'Alice', 'age' => 25]));
    echo "SET result: " . $setResult . "\n";
    
    $getResult = $redis->getValue('user:101');
    echo "GET result: " . $getResult . "\n";
} catch (Exception $e) {
    echo "Redis error: " . $e->getMessage() . "\n";
}
echo "\n";

// Elasticsearch
echo "[Elasticsearch] Indexing document and searching:\n";
try {
    $elastic = new ElasticExample();
    $indexResult = $elastic->indexDocument('books', 1, ['title' => '1984', 'author' => 'Orwell']);
    echo "Index result: " . $indexResult . "\n";
    
    $searchResult = $elastic->search('books', ['author' => 'Orwell']);
    $searchData = json_decode($searchResult, true);
    echo "Search hits: " . count($searchData['hits']['hits']) . " documents found\n";
} catch (Exception $e) {
    echo "Elasticsearch error: " . $e->getMessage() . "\n";
}
echo "\n";

// ClickHouse
echo "[ClickHouse] Query system tables:\n";
try {
    $click = new ClickhouseExample();
    $queryResult = $click->query('SELECT count() FROM system.tables');
    echo "System tables count: " . trim($queryResult) . "\n";
} catch (Exception $e) {
    echo "ClickHouse error: " . $e->getMessage() . "\n";
}

echo "\n=== All examples completed successfully ===\n";

