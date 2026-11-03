<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\TaskRepository;

try {
    $id = TaskRepository::create(2,'テスト用タスク');
    echo 'タスクを作成しました (id: ' . $id .') ';

}  catch (InvalidArgumentException $e) {
    echo '入力が正しくありません' . $e->getMessage();

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo 'タスクの作成に失敗しました';
}