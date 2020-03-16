<?php
// var_dump($_SERVER);
$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->on('request', function ($request, $response) {
//     var_dump($request->post);
    //
//     var_dump($request->rawContent());
    //
//     //var_dump(file_get_contents('php://input'));

//     //var_dump(fopen('php://input'));

    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$http->start();
