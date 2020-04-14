<?php
require_once 'context.php';
// 说明协程的操作本身其实就是操作数组，这个数组在进程的范围内是有效的，是吧老师？
class Test
{
    static $test = [];
}

$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->set([ //需要注意为了效果需要把进程个数设置为1
    'worker_num' => 1
]);
$http->on('request', function ($request, $response) {

    if ($request->server['request_uri'] == '/favicon.ico') {
        return ;
    }

    $token = $request->get['token'];
//    Test::$test = $token;

     Context::put('token', $token);
    if ($token == 'sixstar') {
        Co::sleep(5);
    }

    echo "请求的token：".$token."\n";
    $response->end("执行ok 你的token :".Test::$test."\n");
});
echo "http://0.0.0.0:9501\n";
$http->start();
