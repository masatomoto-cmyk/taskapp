<?php

declare(strict_types=1);

namespace App;

final class TaskRepository
{
    public static function create(int $userId, string $title): int
    {
        if (trim($title) === '') {
            throw new \InvalidArgumentException('title は空にできません');
        }
        if (mb_strlen($title) > 100) {
            throw new \InvalidArgumentException('title は100文字以内にしてください');
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

    public static function findByUser(int $userId): array
    {   
        
        $pdo = Database::connect();

        $stmt = $pdo->prepare(
            'SELECT id, title, status, due_date, created_at
                FROM tasks
                WHERE user_id = :user_id
                ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll();
    }
    public static function updateTitle(int $id, int $userId, string $title): bool
    {
        if (trim($title) === '') {
            throw new \InvalidArgumentException('title は空にできません');
        }
        if (mb_strlen($title) > 100) {
            throw new \InvalidArgumentException('title は100文字以内にしてください');
        }

        $pdo = Database::connect();

        $stmt = $pdo->prepare(
        'UPDATE tasks SET title = :title WHERE id = :id AND user_id = :user_id
'
    );

        $stmt->execute([
            ':id'      => $id,
            ':user_id' => $userId,
            ':title' => $title
        ]);

        return $stmt->rowCount() > 0;
    }
    public static function delete(int $id, int $userId): bool
    {

        $pdo = Database::connect();

        $stmt = $pdo->prepare(
        'DELETE FROM tasks WHERE id = :id AND user_id = :user_id'
        
    );

    $stmt->execute([
            ':id'      => $id,
            ':user_id' => $userId,
            
        ]);

    return $stmt->rowCount() > 0;
    }
}
