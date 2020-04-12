<?php
for ($i = 0; $i < 20; $i++) {
//        $pdo = new PDO('mysql:host=localhost;dbname=swoole', 'root', 'bfccm@db123');
//        $pdo->query('select count(*) from dept');
//        $pdo = null;
    go(function () {
        $swoole_mysql = new Swoole\Coroutine\MySQL();
        $swoole_mysql->connect([
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'root',
            'password' => 'bfccm@db123',
            'database' => 'swoole',
        ]);
        $res = $swoole_mysql->query('select count(*) from dept');
//            var_dump($res);
    });
}