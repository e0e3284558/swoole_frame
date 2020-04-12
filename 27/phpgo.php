<?php
// 通过原生php模拟协程

//function go1()
//{
//    for ($i = 0; $i <= 5; $i++) {
//        echo "go1执行 i {$i} \n";
//        sleep(1);
//        yield $i;
//    }
//}
//
//function go2()
//{
//    for ($i = 0; $i <= 5; $i++) {
//        echo "go2执行 i {$i} \n";
//        sleep(1);
//        yield $i;
//    }
//}

function go1()
{
    // 通过curl模拟异步
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://106.13.78.8/swoole/');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);

    $md = curl_multi_init();
    curl_multi_add_handle($md, $ch);

    do {
        $mrc = curl_multi_exec($md, $running);
        yield false;
    } while ($running > 0);

    var_dump(curl_multi_getcontent($ch));
    yield true;

}

function go2()
{

    // 通过curl模拟异步
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://106.13.78.8/swoole/');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);

    $md = curl_multi_init();
    curl_multi_add_handle($md, $ch);

    do {
        $mrc = curl_multi_exec($md, $running);
        var_dump($running);
        yield false;
    } while ($running > 0);

    var_dump(curl_multi_getcontent($ch));
    yield true;


}


$go1 = go1();
$go2 = go2();

while (true) {
    $re = $go1->current();
    $go2->current();

    $go1->next();
    $go2->next();

    if ($re) {
        break;
    }
}