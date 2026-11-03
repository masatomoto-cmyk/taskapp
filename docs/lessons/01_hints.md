# レッスン1 ヒント集

`____` の部分を自分で埋める。わからなければ、その単語で調べてよい。

## 課題A：schema.sql

### 書く順番
1. データベースを作る → 2. そのデータベースを使う → 3. users を作る → 4. tasks を作る

tasks は users を参照するので、**users が先**。

### 骨組み

```sql
CREATE DATABASE IF NOT EXISTS taskapp
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE taskapp;

CREATE TABLE users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasks (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL ,
  title       VARCHAR(100) NOT NULL,
  description VARCHAR(255) NULL,
  status      ENUM('todo','done','expired') NOT NULL DEFAULT 'todo',
  due_date    DATE NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### つまずきやすい点
- **user_id の型は users.id と完全に同じにする**（UNSIGNED の有無も）。違うと外部キーが作れない
- 最後の列の後ろに `,` を付けない（付けると構文エラー 1064）
- ENUM の値は `'todo'` のようにシングルクォートで囲む
- 外部キーは InnoDB でしか効かない（骨組みでは指定済み）

### エラーが出たら
| エラー | よくある原因 |
|---|---|
| 1064 You have an error in your SQL syntax | カンマの付けすぎ・付け忘れ、クォート忘れ |
| 1005 / errno 150 | 外部キーの型が一致していない、users を先に作っていない |
| 1050 Table already exists | 2回目の実行。tasks → users の順に DROP してからやり直す |

### 作れたか確かめる
phpMyAdmin で `SHOW CREATE TABLE tasks;` を実行し、FOREIGN KEY が表示されれば OK。

---

## 課題B：src/Database.php

### 考える順番
1. すでに接続済みなら、それを返す（2回目以降）
2. config.php を読み込む
3. DSN（接続先を表す文字列）を組み立てる
4. PDO を作って保存し、返す

### 骨組み

```php
<?php
declare(strict_types=1);

namespace App;

use PDO;   // ← これがないと何が起きるか、下の「つまずきやすい点」を参照

final class Database
{
    //dbの接続専用の箱を作り、最初は箱に入れておくdb接続には時間がかかる２回目以降は自動
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $config = require __DIR__ . '/../config/config.php';
        $db = $config['db'];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $db['host'], $db['port'], $db['password']
        );

        self::$pdo = new PDO($dsn, root, root, [
            PDO::ATTR_ERRMODE    =>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$pdo;
    }
}
```

### つまずきやすい点
- `namespace App;` の中で `PDO` と書くと、PHP は `App\PDO` を探してしまう。`use PDO;` で「PHP 標準の PDO だよ」と教える
- `config.php` は `return [...]` で配列を返しているので、`require` の結果をそのまま変数に入れられる
- `__DIR__` は「このファイルがあるフォルダ」。src/ から見て config/ は `../config/`
- ファイル名とクラス名を一致させる（`Database.php` ⇔ `class Database`）。オートロードはこれで探している
- ここでは try/catch しない。失敗したら例外のまま呼び出し側へ渡す（レッスン本文の「exit しない」と同じ考え方）

### PDO オプションの調べ方
PHP マニュアルの「PDO::setAttribute」のページに、3つの定数が全部載っている。

### 文法チェック
```bash
php -l /Applications/MAMP/htdocs/taskapp/src/Database.php
```
「No syntax errors detected」と出れば文法は OK。

---

## 課題C：public/db_check.php

### 骨組み

```php
<?php
declare(strict_types=1);

require __DIR__ . '/../____/autoload.php';

use App\Database;

try {
    $pdo = Database::connect();
    $result = $pdo->query('SELECT 1')->____();   // 値を1つだけ取り出すメソッド
    echo 'DB 接続 OK（結果: ' . $result . '）';
} catch (PDOException $e) {
    error_log($e->getMessage());   // 詳細はログへ
    echo 'DB 接続に失敗しました';   // 画面には一般的なメッセージだけ
}
```

### エラーが出たら
| 表示・ログ | よくある原因 |
|---|---|
| `Class "App\Database" not found` | ファイル名・クラス名・namespace のずれ。直しても駄目なら `composer dump-autoload` |
| `Class "App\PDO" not found` | Database.php に `use PDO;` がない |
| `SQLSTATE[HY000] [2002]` | MySQL が止まっている、またはポートが違う（MAMP は 8889） |
| `SQLSTATE[HY000] [1049] Unknown database` | 課題A の SQL をまだ実行していない |
| `SQLSTATE[HY000] [1045] Access denied` | config.php のユーザー名・パスワード違い |
| 真っ白な画面 | エラー表示がオフ。MAMP の PHP エラーログを見る |

「DB 接続に失敗しました」としか出ないときは、詳細を MAMP のログで見る：
`/Applications/MAMP/logs/php_error.log`
