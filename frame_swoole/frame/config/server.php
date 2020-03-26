<?php
return [
    'http' => [
        'host' => '0.0.0.0',
        'port' => 9000,
        'swoole' => [

        ],
        'tcpable' => 1,//1为开启，0为关闭
        'rpc' => [
            'host' => '127.0.0.1',
            'port' => 8000,
            'swoole' => [
                'worker_num' => 1
            ],
        ],
    ],
    'ws'=>[
        'is_handshake'=>true
    ],
    'route'=>[
        'server'=>[
            'port'=>9600,
            'host'=>'192.168.186.130'
        ],
        'jwt'=>[
            'key'=>"swocloud",
            'alg'=>[
                'HS256'
            ]
        ]
    ]
];