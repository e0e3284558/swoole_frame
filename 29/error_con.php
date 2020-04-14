<?php
require_once 'context.php';
// 说明协程的操作本身其实就是操作数组，这个数组在进程的范围内是有效的，是吧老师？
class Test
{
    static $test = [];
}

$http = new Swoole\Http\Server("0.0.0.0", 9600);
$http->set([ //需要注意为了效果需要把进程个数设置为1
    'worker_num' => 1
]);
$http->on('request', function ($request, $response) {

    if ($request->server['request_uri'] == '/favicon.ico') {
        return ;
    }

    $token = $request->get['token'];
    // Test::$test = $token;

    Context::put('token', $token);
    if ($token == 'sixstar') {
        Co::sleep(5);
    }
    // 在协程结束的时候调用
    defer(function () {
        Context::delete(); // delete
        echo "请求的token：清空 token\n";
    });
    echo "请求的token：".$token."\n";
    $response->end("协程的id".Co::getcid()."执行ok 你的token :".Context::get('token')."\n");
    // Context::delete('token');
});
echo "http://0.0.0.0:9600\n";
$http->start();
