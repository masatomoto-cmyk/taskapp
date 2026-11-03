# レッスン1：テーブル設計と DB 接続

## このレッスンのゴール

- アプリで使う 2 つのテーブル（users / tasks）を自分で設計し、SQL で作れる
- PDO で安全に DB へ接続するクラスを、何も見ずに書ける

## 作るアプリの全体像

| 段階 | 作るもの |
|---|---|
| ① CRUD | タスクの作成・一覧・更新・削除 |
| ② 認証 | ユーザー登録・ログイン・ログアウト。自分のタスクだけ操作できる |
| ③ テスト | PHPUnit で ①② のテストを書く |
| ④ バッチ | 期限切れタスクを夜間に一括で「expired」にする CLI スクリプト |

## フォルダ構成

```
taskapp/
├── public/      ブラウザから開くファイル（画面・フォームの受け口）だけを置く
├── src/         クラス（処理の本体）。namespace は App\
├── config/      設定。config.php は Git に入れない
├── database/    schema.sql（テーブル定義）
├── bin/         バッチなど、コマンドラインで動かすスクリプト
├── tests/       PHPUnit のテスト
└── docs/        このレッスンのメモ
```

ポイント：**画面（public）と処理（src）を分ける**。処理をクラスにしておくと、後でテストとバッチから同じ処理を呼べる。

---

## 課題 A：`database/schema.sql` を書く

次の要件を満たす `CREATE DATABASE` と `CREATE TABLE` を 2 つ書く。

**users**
- id（自動で増える主キー）
- email（重複禁止）
- password_hash（ハッシュ化したパスワード。password_hash() の結果は 255 文字あれば足りる）
- created_at（登録日時。自動で入る）

**tasks**
- id（自動で増える主キー）
- user_id（誰のタスクか。users.id を参照する外部キー。ユーザーを消したらタスクも消える）
- title（必須、100 文字まで）
- description（任意、長文 OK）
- status（todo / done / expired のどれか。初期値 todo）
- due_date（期限日。任意）
- created_at / updated_at（updated_at は更新時に自動で変わる）

ヒント
- 文字コードは `utf8mb4`（絵文字も保存できる）
- `ENUM`、`DEFAULT CURRENT_TIMESTAMP`、`ON UPDATE CURRENT_TIMESTAMP`、`FOREIGN KEY ... ON DELETE CASCADE` を調べてみる
- 書けたら phpMyAdmin（http://localhost:8888/phpMyAdmin/ など）の SQL タブで実行

## 課題 B：`src/Database.php` を書く

条件
- `namespace App;`
- `Database::connect(): PDO` で接続済みの PDO を返す
- 接続情報は `config/config.php` を `require` して読む（コードに直接書かない）
- DSN に `charset=utf8mb4` を付ける
- PDO のオプションを 3 つ指定する
  - エラー時に例外を投げる
  - 取得結果を連想配列で返す
  - 静的プレースホルダを使う（エミュレーションを切る）
- 余裕があれば：何度呼んでも接続は 1 回だけにする（static プロパティに入れておく）

## 課題 C：`public/db_check.php` で接続確認

- `require __DIR__ . '/../vendor/autoload.php';`
- `App\Database::connect()` で `SELECT 1` を実行し、成功したら「DB 接続 OK」と表示
- http://localhost:8888/taskapp/public/db_check.php を開いて確認
- 確認できたらこのファイルは消してよい

---

## 以前のコードから学ぶこと

過去に書いたコード（phpapp/CRUD.php など）で直したい点。今回のコードでは避ける。

1. **if の外に処理がはみ出している**：POST でないときも `password_hash()` と INSERT が動いてしまう
2. **`$e->getMessage()` を画面に出している**：DB の内部情報が利用者に見える。画面には「エラーが発生しました」だけ出し、詳細はログに残す
3. **クラスの中で `exit()` している**：呼び出し側が失敗に対応できず、テストもできない。例外を投げて呼び出し側に任せる

## 理解チェック（自分の言葉で答えられるか）

1. なぜ `utf8` ではなく `utf8mb4` を使うのか
2. エミュレーションを切る（静的プレースホルダ）と何がうれしいのか
3. tasks に user_id が必要なのはなぜか（後の「認可」とのつながり）
4. config.php を Git に入れないのはなぜか

できたら「課題できた」と伝えると、レビューする。
