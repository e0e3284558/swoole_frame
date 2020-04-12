<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://106.13.78.8/swoole/');
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_exec($ch);

$md = curl_multi_init();
curl_multi_add_handle($md, $ch);

do {
    $mrc = curl_multi_exec($md, $running);
    var_dump($running);
} while ($running > 0);

var_dump(curl_multi_getcontent($ch));