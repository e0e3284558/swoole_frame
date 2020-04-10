<?php

namespace App\Listener;

use SwoStar\Event\Listener;
use SwoStar\Server\WebSocket\Connections;
use SwoStar\Server\WebSocket\WebSocketServer;
use Swoole\Coroutine\Http\Client;

class WSMessageFrontListener extends Listener
{
    protected $name = 'ws.message.front';

    public function handler(WebSocketServer $swoStarServer = null, $swooleServer = null, $frame = null)
    {
        /*
         * 消息的格式需要统一
         * {
         *      'method'=>执行操作
         *      'msg'=》消息
         * }
         */
        $data = json_decode($frame->data, true);
        $this->{$data['method']}($swoStarServer, $swooleServer, $data, $frame->fd);
    }

    /**
     * 服务器广播
     * @param WebSocketServer|null $swoStarServer
     * @param null $swooleServer
     * @param $data
     * @param null $frame
     * @throws \Exception
     */
    protected function serverBroadcast(WebSocketServer $swoStarServer = null, $swooleServer = null, $data, $frame = null)
    {
        $config = $this->app->make('config');
//        dd($config->get('server.route.server.ip'), $config->get('server.route.server.port'));

        $cli = new Client($config->get('server.route.server.host'), $config->get('server.route.server.port'));
        if ($cli->upgrade('/')) {
            $cli->push(json_encode([
                'method' => 'routeBroadcast',
                'msg' => $data['msg']
            ]));
        }
    }

    /**
     * 接收Route服务器的广播信息
     *
     * @param WebSocketServer|null $swoStarServer
     * @param null $swooleServer
     * @param $data
     * @param $fd
     */
    protected function routeBroadcast(WebSocketServer $swoStarServer = null, $swooleServer = null, $data, $fd)
    {
        dd($data, 'server 中的 routeBroadcast');
        $dataAck=[
            'method'=>'ack',
            'msg_id'=>$data['msg_id']
        ];
        $swooleServer->push($fd,json_encode($dataAck));
        $swoStarServer->sendAll(json_encode($data['data']));
    }

    /**
     * 接收客户端私聊的信息
     * @param WebSocketServer|null $swoStarServer
     * @param null $swooleServer
     * @param $data
     * @param $fd
     */
    protected function privateChat(WebSocketServer $swoStarServer = null, $swooleServer = null, $data, $fd)
    {
        // 1.获取私聊的id
        $clientId = $data['clientId'];
        // 2.从redis获取对应的服务器信息
        $clientIMServerInfoJson = $swoStarServer->getRedis()->hGet($this->app->make('config')->get('server.route.jwt.key'), $clientId);
        $clientIMServerInfo = json_decode($clientIMServerInfoJson, true);
        dd($clientIMServerInfo, '接收方的服务器信息');
        // 指定发送
        $request = Connections::get($fd)['request'];
        $token = $request->header['sec-websocket-protocol'];
        // $url=0.0.0.0:9000
        $clientIMServerUrl = explode(":", $clientIMServerInfo['serverUrl']);
        $swoStarServer->send($clientIMServerUrl[0], $clientIMServerUrl[1], [
            'method' => 'forwarding',
            'msg' => $data['msg'],
            'fd' => $clientIMServerInfo['fd']
        ], [
            'sec-websocket-protocol' => $token
        ]);
    }

    /**
     * 转发私聊信息
     * @param WebSocketServer|null $swoStarServer
     * @param null $swooleServer
     * @param $data
     * @param $fd
     */
    protected function forwarding(WebSocketServer $swoStarServer, $swooleServer ,$data, $fd)
    {
        $swooleServer->push($data['fd'], json_encode(['msg' => $data['msg']]));
    }
}