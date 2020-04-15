<?php
Swoole\Runtime::enableCoroutine(true);
require "./Pool.php";

$http = new Swoole\Http\Server("0.0.0.0", 9200);

$http->set(['worker_num'=>1]);

$http->on('request', function ($request, $response) {
    if ($request->server['request_uri'] === '/favicon.ico') {
        return;
    }
    $pool = Pool::getInstance();
    $conn = $pool->getConnection();
    $query='select sleep(2)';
    $pool=Pool::getInstance();

    if (isset($conn) && $conn['db'] instanceof PDO){
        $pool->call($conn,'query',$query);
//    $conn->query('select sleep(2)');
        $pool->freeConnection($conn);
        $response->end("执行ok\n");
    }

});

echo "http://106.13.78.8:9200/ \n";
$http->start();