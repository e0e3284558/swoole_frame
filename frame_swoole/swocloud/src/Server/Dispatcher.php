<?php

namespace SwoCloud\Server;

use Firebase\JWT\JWT;
use Swoole\Server as SwooleServer;
use Swoole\Http\Request;
use Swoole\Http\Response;
use \Redis;
use SwoCloud\Supper\Arithmetic;

class Dispatcher
{
    public function register(Route $route, SwooleServer $server, $fd, $data)
    {
        dd('register', 'Dispatcher');
        $serverKey = $route->getServerKey();
        // 把服务端信息记录到redis
        $redis = $route->getRedis();
        $value = json_encode([
            'ip' => $data['ip'],
            'port' => $data['port'],
        ]);
        $redis->sadd($serverKey, $value);
        $server->tick(3000, function ($timer_id, Redis $redis, SwooleServer $server, $serverKey, $fd, $value) {
            // 服务器是否正常运行，如果不是就主动清空
            // 并把信息从redis中清除
            if (!$server->exist($fd)) {
                $redis->srem($serverKey, $value);
                $server->clearTimer($timer_id);
                dd('im server 宕机 ，主动清空');
            }
        }, $redis, $server, $serverKey, $fd, $value);
    }

    /**
     * route向服务器广播
     * @param Route $route
     * @param SwooleServer $server
     * @param $fd
     * @param $data
     */
    public function routeBroadcast(Route $route, SwooleServer $server, $fd, $data)
    {
        dd($data,'接收到 im-server client 的 msg');
        // 获取到所有的服务器
        $imServers = $route->getIMServers();

        $token = $this->getToken(0, $route->getHost() . ':' . $route->getPort());
        $header = ['sec-websocket-protocol'=>$token];
        foreach ($imServers as $key => $imServer) {
            $imInfo = json_decode($imServer, true);
            // 转发给其他的服务器
            $route->send($imInfo['ip'], $imInfo['port'], [
                'method'=>'routeBroadcast',
                'data' => [
                    'msg' => $data['msg'],
                ]
            ],$header);
        }
    }

    public function ack($value='')
    {

    }


    /**
     * 用户登录的方法
     * @param Route $route
     * @param Request $request
     * @param Response $response
     */
    public function login(Route $route, Request $request, Response $response)
    {
        // 获取im-server服务器
        $imServer = json_decode($this->getIMServer($route), true);
//        dd($imServer, '这是获取的服务器信息');

        $url = $imServer['ip'] . ':' . $imServer['port'];
        // 去数据库中进行用户密码和账户验证

        $uid = $request->post['id'];
        $token = $this->getToken($uid, $url);
//        dd($token, "生成的token");
        $response->end(json_encode([
            'token' => $token,
            'url' => $url
        ]));
    }

    public function getToken($uid, $url)
    {
        $key = 'swocloud';
        $time = time();
        $payload = array(
            "iss" => '',
            'aud' => '',
            // 签发时间
            'iat' => $time,
            // 生效时间
            'nbf' => $time,
            "exp" => $time + (60 * 60 * 24),
            "data" => [
                'uid' => $uid,
                'name' => 'bifei' . $uid,
                'url' => $url
            ]
        );
        return JWT::encode($payload, $key);
//        $decoded=JWT::decode($jwt,$key,array('HS256'));
    }

    /**
     * 获取IMserver
     */
    public function getIMServer(Route $route)
    {
        // 所有的服务信息
        $imServers = $route->getIMServers();
        if (!empty($imServers)) {
            return Arithmetic::{$route->getArithmetic()}($imServers);
        }
        return false;
    }
}