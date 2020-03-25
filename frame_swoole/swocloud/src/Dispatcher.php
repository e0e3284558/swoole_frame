<?php

namespace SwoCloud;

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
            'ip'   => $data['ip'],
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
     * 用户登录的方法
     * @param Route $route
     * @param Request $request
     * @param Response $response
     */
    public function login(Route $route, Request $request, Response $response)
    {
        // 获取im-server服务器
        $imServer = json_decode($this->getIMServer($route),true);
        dd($imServer, '这是获取的服务器信息');

        $url = $imServer['ip'] . ':' . $imServer['port'];
        // 去数据库中进行用户密码和账户验证

        $uid = $request->post['id'];
        $token = $this->getToken($uid, $url);
        dd($token, "生成的token");
        $response->end(json_encode([
            'token' => $token,
            'url'=>$url
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
        $imServer = $route->getRedis()->smembers($route->getServerKey());
        if (!empty($imServer)) {
            return Arithmetic::{$route->getArithmetic()}($imServer);
        }
        return false;
    }
}