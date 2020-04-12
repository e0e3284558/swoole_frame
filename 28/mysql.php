<?php
for ($i = 0; $i < 20; $i++) {
    $pdo = new PDO('mysql:host=localhost;dbname=swoole', 'root', 'bfccm@db123');
    $pdo->query('select count(*) from dept');
    $pdo = null;
}