<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';


use App\Database;

try {
    $pdo = Database::connect();
    $result = $pdo->query('SELECT 1')->fetchColumn();
    echo 'DB 接続 OK (結果: ' . $result . ')';
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo 'DB 接続に失敗しました';
}
