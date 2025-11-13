<?php

require 'vendor/autoload.php';

use App\ElasticExample;

$elastic = new ElasticExample();
$index = 'books';
$message = '';
$searchResults = [];

// Обработка добавления книги
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'add_book') {
            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $genre = $_POST['genre'] ?? '';
            $year = $_POST['year'] ?? '';

            if ($title && $author && $genre && $year) {
                $bookId = time() . rand(1000, 9999);
                $bookData = [
                    'title' => $title,
                    'author' => $author,
                    'genre' => $genre,
                    'year' => (int)$year
                ];

                $elastic->indexDocument($index, $bookId, $bookData);
                $message = "Книга '{$title}' добавлена";
            } else {
                $message = "Заполните все поля";
            }
        }

        // Поиск по жанру
        if ($_POST['action'] === 'search_genre') {
            $searchGenre = $_POST['search_genre'] ?? '';

            if ($searchGenre) {
                $result = $elastic->search($index, ['genre' => $searchGenre]);
                $data = json_decode($result, true);

                if (isset($data['hits']['hits']) && count($data['hits']['hits']) > 0) {
                    $searchResults = $data['hits']['hits'];
                    $message = "Найдено " . count($searchResults) . " книг в жанре '{$searchGenre}'";
                } else {
                    $message = "Книги не найдены";
                    $searchResults = [];
                }
            }
        }
    } catch (Exception $e) {
        $message = "Ошибка: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог книг</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .section { margin-bottom: 30px; }
        .form-group { margin-bottom: 10px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { padding: 5px; width: 300px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .message { padding: 10px; margin-bottom: 15px; background: #e7f3ff; border: 1px solid #0066cc; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Каталог книг</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="section">
        <h2>Добавить книгу</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_book">
            
            <div class="form-group">
                <label>Название:</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Автор:</label>
                <input type="text" name="author" required>
            </div>

            <div class="form-group">
                <label>Жанр:</label>
                <select name="genre" required>
                    <option value="">-- Выберите жанр --</option>
                    <option value="Фантастика">Фантастика</option>
                    <option value="Детектив">Детектив</option>
                    <option value="Роман">Роман</option>
                    <option value="Приключения">Приключения</option>
                    <option value="Ужасы">Ужасы</option>
                    <option value="История">История</option>
                </select>
            </div>

            <div class="form-group">
                <label>Год издания:</label>
                <input type="number" name="year" min="1000" max="<?php echo date('Y'); ?>" required>
            </div>

            <button type="submit">Добавить</button>
        </form>
    </div>

    <div class="section">
        <h2>Поиск по жанру</h2>
        <form method="POST">
            <input type="hidden" name="action" value="search_genre">
            
            <div class="form-group">
                <label>Жанр:</label>
                <select name="search_genre" required>
                    <option value="">-- Выберите жанр --</option>
                    <option value="Фантастика">Фантастика</option>
                    <option value="Детектив">Детектив</option>
                    <option value="Роман">Роман</option>
                    <option value="Приключения">Приключения</option>
                    <option value="Ужасы">Ужасы</option>
                    <option value="История">История</option>
                </select>
            </div>

            <button type="submit">Искать</button>
        </form>
    </div>

    <?php if (!empty($searchResults)): ?>
        <div class="section">
            <h2>Результаты поиска</h2>
            <table>
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Автор</th>
                        <th>Жанр</th>
                        <th>Год</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchResults as $hit): ?>
                        <?php $book = $hit['_source']; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['genre']); ?></td>
                            <td><?php echo htmlspecialchars($book['year']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</body>
</html>
