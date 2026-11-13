<?php

require __DIR__ . '/../vendor/autoload.php';
use App\TaskRepository;


$id = TaskRepository::create(2,'更新前');

var_dump(TaskRepository::updateTitle($id,2,'更新後'));

var_dump(TaskRepository::updateTitle($id,999,'乗っ取り'));