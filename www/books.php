<?php
require 'vendor/autoload.php';

use App\ElasticExample;

$elastic = new ElasticExample();

// 1️⃣ Индексируем несколько книг
$books = [
    ['id' => 1, 'title' => '1984', 'author' => 'George Orwell', 'year' => 1949],
    ['id' => 2, 'title' => 'Brave New World', 'author' => 'Aldous Huxley', 'year' => 1932],
    ['id' => 3, 'title' => 'Fahrenheit 451', 'author' => 'Ray Bradbury', 'year' => 1953],
];

foreach ($books as $book) {
    echo "Indexing: {$book['title']}<br>";
    echo $elastic->indexDocument('books', $book['id'], $book);
    echo "<hr>";
}

// 2️⃣ Поиск по автору
echo "<h2>Поиск книг автора 'Orwell'</h2>";
echo $elastic->search('books', ['author' => 'Orwell']);
