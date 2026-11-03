<?php

declare(strict_types=1);

namespace App;

final class TaskRepository
{
    public static function create(int $userId, string $title): int
    {   if (trim($title) === '') {
        throw new \InvalidArgumentException('title は空にできません');
}
    
        $pdo = Database::connect();


        $stmt = $pdo->prepare(
            'INSERT INTO tasks (user_id, title) VALUES (:user_id, :title)'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':title'   => $title,
        ]);

        return (int) $pdo->lastInsertId();
    }
}
