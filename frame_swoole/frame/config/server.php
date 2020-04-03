<?php
return [
    "http" => [
        "host" => "0.0.0.0",
        "port" => 9000,
        "swoole" => [
            "task_worker_num" => 0,
        ],
    ],
    'ws'=>[
        'host' => '0.0.0.0',                 //服务监听ip
        'port' => 9800,                      //监听端口
        'enable_http' => true,               //是否开启http服务
        'swoole' => [                        //swoole配置
            "task_worker_num" => 0,
            // 'daemonize' => 0,             //是否开启守护进程
        ],
        'is_handshake' => true,
    ],
    "rpc" => [
        'tcpable'=>1,                        //是否开启tcp监听
        "host" => "127.0.0.1",
        "port" => 9502,
        "swoole_setting" => [
            "worker_num" => "2"
        ]
    ],
    'route' => [
        'server' => [
            'host' => '106.13.78.8',
            'port' => 9500,
        ],
        'jwt' => [
            'key' => 'swocloud',
            'alg' => [
                'HS256'
            ]
        ]
    ]
];
