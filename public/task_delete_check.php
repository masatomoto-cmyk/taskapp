<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\TaskRepository;
$id = TaskRepository::create(2,'削除前');
var_dump(TaskRepository::delete($id,999));
var_dump(TaskRepository::delete($id,2));
var_dump(TaskRepository::delete($id,2));


