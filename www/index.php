<?php

require 'vendor/autoload.php';

use App\RedisExample;
use App\ElasticExample;
use App\ClickhouseExample;

// Redis
$redis = new RedisExample();
echo $redis->setValue('user:101', json_encode(['name' => 'Alice', 'age' => 25]));
echo $redis->getValue('user:101');

// Elasticsearch
$elastic = new ElasticExample();
echo $elastic->indexDocument('books', 1, ['title' => '1984', 'author' => 'Orwell']);
echo $elastic->search('books', ['author' => 'Orwell']);

// ClickHouse
$click = new ClickhouseExample();
echo $click->query('SELECT count() FROM system.tables');
