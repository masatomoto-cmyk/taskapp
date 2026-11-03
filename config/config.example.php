<?php
// このファイルを config.php という名前でコピーして使う（config.php は Git に入れない）
return [
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 8889,          // MAMP の MySQL の初期ポート
        'name'     => 'taskapp',
        'user'     => 'root',
        'password' => 'root',        // MAMP の初期値。変えている場合は合わせる
    ],
];
