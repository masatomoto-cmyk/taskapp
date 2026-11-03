# taskapp

認証付きのタスク管理アプリ。フレームワークを使わず素の PHP で、認証・CRUD・テスト・バッチ処理を実装する。

## 動かし方（MAMP）

1. `config/config.example.php` を `config/config.php` にコピーし、DB 接続情報を合わせる
2. `database/schema.sql` を phpMyAdmin などで実行する
3. `composer dump-autoload`
4. http://localhost:8888/taskapp/public/ を開く
