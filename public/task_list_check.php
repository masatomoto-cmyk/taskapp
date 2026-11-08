<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
use App\TaskRepository;


$tasks = TaskRepository::findByUser(2);

foreach($tasks as $task){
    echo htmlspecialchars($task['title'],ENT_QUOTES,'UTF-8').'<br>';
}