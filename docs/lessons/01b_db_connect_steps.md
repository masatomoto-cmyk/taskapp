# レッスン1 の続き：DB につなぐ（課題B の残り → 課題C）

> 課題A（テーブル作り）は、3回書き直してほぼ自力で完成させました。いちばん難しい「設計」を越えたので、ここからは穴を1つずつ埋めるだけです。

## この回でできるようになること

`src/Database.php` を完成させて、ブラウザに **「DB 接続 OK（結果: 1）」** と出せるようになります。

## Database.php は何をするものか

お店にたとえると次のとおりです。

| コードの部分 | たとえ | 役割 |
|---|---|---|
| `config.php` | 合鍵の保管箱 | 接続先・ユーザー名・パスワードをしまっておく場所（Git に入れない） |
| `$dsn` | お店の住所 | 「どのサーバーの、どのデータベースに、どの文字コードで」つなぐか |
| `new PDO(...)` | 鍵を開けて入店する | 住所と合鍵を使って、実際に DB につなぐ |
| オプション3つ | 入店時のお約束 | 「失敗したら大声で知らせて」「品物は名札つきで」「注文票は本物で」 |
| `self::$pdo` | 入館証 | 一度入ったら入館証を持っておき、2回目からは並ばずに入る |

今日うめるのは、**「合鍵を渡す」所と「お約束3つ」の所**だけです。住所（`$dsn`）と入館証（`self::$pdo`）はもう書けています。

## 手を動かす

> 書くのは **`src/Database.php`**（`01_hints.md` ではありません）。1つうめたら保存して、ステップ0 の確かめ方を毎回やります。

### 0. 毎回の確かめ方（これだけ覚えれば OK）

穴をうめるたびに、ターミナルで次の2つを打ちます。

```bash
php -l /Applications/MAMP/htdocs/taskapp/src/Database.php
```

`No syntax errors detected` と出れば、文の形（文法）は大丈夫です。

> **注意：`php -l` は「文の形」しか見ません。** 実は `____` が残ったままでも `No syntax errors detected` と出ます（`____` も名前として正しい形だからです）。中身が正しいかは、課題C で実際に動かして確かめます。

### 1. 合鍵を渡す（PDO の2番目・3番目）

```php
self::$pdo = new PDO($dsn, ____, ____, [
```

- `new PDO(住所, ユーザー名, パスワード, お約束)` の順番です
- ユーザー名とパスワードは、すぐ上の行で作った **`$db`** に入っています（`$db = $config['db'];`）
- `config.php` の中を見ると、`'user' => ...` と `'password' => ...` という名札がついています
- 配列から名札で取り出す書き方は `$db['名札']`

**ヒント（ほぼ答え）：** 1つ目は `$db['____']`、2つ目も `$db['________']`。名札は config.php に書いてある英単語そのままです。

> `root, root` と書くのは間違いです（前に一度ありました）。引用符のない `root` は「root という名前の定数」と読まれてエラーになります。`'root'` と引用符をつければ動きますが、それだとパスワードをコードに直接書くことになるので、config.php を作った意味がなくなります。

### 2. お約束①：失敗したら大声で知らせて（ERRMODE）

```php
PDO::ATTR_ERRMODE => PDO::____,
```

- 「エラーのときにどうするか」の設定です。何も言わないと、静かに失敗して気づけないことがあります
- 選べる値は `ERRMODE_SILENT`（黙る）、`ERRMODE_WARNING`（警告だけ）、`ERRMODE_EXCEPTION`（例外を投げる＝大声で知らせる）の3つ
- 課題の条件は「エラー時に**例外**を投げる」

**ヒント：** 3つのうち「例外」を英語にした `EXCEPTION` が入っているもの。

### 3. お約束②：品物は名札つきで（FETCH_MODE）

```php
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::____,
```

- DB から1行取り出したとき、どんな形の配列で受け取るかの設定です
- 何も言わないと、同じ値が「名札つき」と「番号つき」で**2回ずつ**入ってきます（練習問題2で実際に見ます）
- 課題の条件は「**連想配列**（名札つきの配列）で返す」。連想配列は英語で associative array

**ヒント：** `FETCH_` のあとに associative を短くした4文字。

### 4. お約束③：注文票は本物で（EMULATE_PREPARES）

```php
PDO::ATTR_EMULATE_PREPARES => ____,
```

- `prepare()` を使ったとき、PHP が「なりきり（エミュレーション）」で SQL を組み立てるか、MySQL 本人に任せるかの設定です
- 課題の条件は「エミュレーションを**切る**」
- 「切る」＝ オフ ＝ 本当か嘘かの「嘘」の値

**ヒント：** `true` か `false` のどちらか。引用符はつけません（`'false'` と書くと文字列になってしまう）。

ここまでうめたら、ステップ0 の `php -l` をもう一度打ちます。

### 5. 課題C：本当につながるか確かめる（`public/db_check.php`）

`public/db_check.php` を新しく作り、次の形で打ちます（穴は2つ）。

```php
<?php
declare(strict_types=1);

require __DIR__ . '/../____/autoload.php';

use App\Database;

try {
    $pdo = Database::connect();
    $result = $pdo->query('SELECT 1')->____();
    echo 'DB 接続 OK（結果: ' . $result . '）';
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo 'DB 接続に失敗しました';
}
```

- 穴①：Composer が作ったフォルダの名前。taskapp の中にあるフォルダを `ls` で見てみましょう
- 穴②：結果の「1つ目の列の値」だけを取り出すメソッド。column（列）を取り出す（fetch）ので `fetch` + `Column`

打ち終わったら、ターミナルで動かします（ブラウザを開かなくても確かめられます）。

```bash
php /Applications/MAMP/htdocs/taskapp/public/db_check.php
```

次のように出れば、**レッスン1 は完成**です。

```
DB 接続 OK（結果: 1）
```

ブラウザなら http://localhost:8888/taskapp/public/db_check.php でも同じ表示になります。

## 練習問題（答えを見ずに解いてみよう）

1. `db_check.php` の `'SELECT 1'` を `'SELECT 1 + 1'` に変えると、何と表示されるでしょう？ 予想してから動かしてみましょう。
2. `public/fetch_test.php` を作って、次を打って動かしましょう。そのあと Database.php の `FETCH_ASSOC` を一時的に `FETCH_BOTH` に変えて、もう一度動かすと表示がどう変わるか見てみましょう（見たら `FETCH_ASSOC` に戻す）。

   ```php
   <?php
   declare(strict_types=1);
   require __DIR__ . '/../vendor/autoload.php';
   use App\Database;
   $row = Database::connect()->query('SELECT 1 AS one, 2 AS two')->fetch();
   print_r($row);
   ```

3. `Database::connect()` を2回呼んだとき、同じ接続が使い回されているかを確かめたいです。`var_dump(Database::connect() === Database::connect());` を動かすと何と出るでしょう？ そうなるのは Database.php のどの行のおかげでしょう？

## よくあるエラーと直し方

どれも実際に起こして確かめたものです。エラー文の **`in ○○.php:26`** の数字が、原因のある行番号です。

### `Undefined constant "App\____"`

```
Fatal error: Uncaught Error: Undefined constant "App\____" in .../src/Database.php:26
```

意味：まだうめていない穴 `____` が残っています。`php -l` では見つからないので、このエラーで初めて気づくことが多いです。26行目の穴をうめましょう。

### `Undefined constant "App\root"`

```
Fatal error: Uncaught Error: Undefined constant "App\root" in .../src/Database.php:26
```

意味：引用符なしの `root` を「定数」だと思って探したけれど、見つからなかったという意味です。ステップ1 のとおり `$db['...']` から取り出しましょう。

### `Undefined constant PDO::ERRMODE_EXCEPTON`

```
Fatal error: Uncaught Error: Undefined constant PDO::ERRMODE_EXCEPTON in .../src/Database.php:27
```

意味：定数の名前の打ち間違いです（この例は `EXCEPTION` の `I` 抜け）。エラーに出ている綴りと、正しい綴りを1文字ずつ見比べます。

### `Class "App\PDO" not found`

```
Fatal error: Uncaught Error: Class "App\PDO" not found in .../src/Database.php:26
```

意味：`use PDO;` が消えています。namespace App の中では、PHP は `App\PDO` を探してしまいます。

### `Call to undefined method PDOStatement::fetchColum()`

```
Fatal error: Uncaught Error: Call to undefined method PDOStatement::fetchColum() in .../public/db_check.php:10
```

意味：そんな名前のメソッドはない、という意味です。この例は `fetchColumn` の `n` 抜けです。

### `Failed opening required '.../vender/autoload.php'`

```
Warning: require(.../public/../vender/autoload.php): Failed to open stream: No such file or directory in .../public/db_check.php on line 4
```

意味：そのファイルが見つかりません。この例は `vendor` を `vender` と打ち間違えています。

### 「DB 接続に失敗しました」としか出ない

画面には、わざとくわしいことを出していません（DB の中の情報を利用者に見せないため）。くわしい理由はログに出ます。ターミナルで動かしたときは画面の上に、ブラウザのときは `/Applications/MAMP/logs/php_error.log` に出ます。

```
SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: YES)
```

| 番号 | 意味 | 見直す所 |
|---|---|---|
| 1045 | 合鍵が合わない | config.php のユーザー名・パスワード、または Database.php で渡す名札の綴り |
| 2002 | お店が閉まっている | MAMP の MySQL が動いているか、ポートが 8889 か |
| 1049 | そんなデータベースはない | 課題A の SQL を流したか、config.php の `name` |

## 答え

<details>
<summary>穴の答え（全部うめ終わって、どうしても動かないときだけ見る）</summary>

```php
self::$pdo = new PDO($dsn, $db['user'], $db['password'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
```

課題C：穴① `vendor`、穴② `fetchColumn`

</details>

<details>
<summary>練習問題の答え</summary>

1. `DB 接続 OK（結果: 2）`。SQL の計算結果が返ってきます。
2. `FETCH_ASSOC` では次のように名札つきだけが出ます。

   ```
   Array
   (
       [one] => 1
       [two] => 2
   )
   ```

   `FETCH_BOTH` にすると、同じ値が番号つきでもう一度入ります。

   ```
   Array
   (
       [one] => 1
       [0] => 1
       [two] => 2
       [1] => 2
   )
   ```

3. `bool(true)` と出ます。`if (self::$pdo !== null) { return self::$pdo; }` の行のおかげで、2回目は入館証（最初の接続）をそのまま返しています。

</details>

## 次回予告

レッスン1 が終わったら、まず **初めてのコミット**（今の状態を Git に記録）をします。そのあと **レッスン2：タスクの一覧と作成（CRUD の C と R）** に進みます。今日つないだ `Database::connect()` を使って、画面からタスクを登録し、一覧に出せるようになります。

理解チェック（`01_db_and_pdo.md` の最後の4問）も、自分の言葉で答えられるか試してみましょう。
