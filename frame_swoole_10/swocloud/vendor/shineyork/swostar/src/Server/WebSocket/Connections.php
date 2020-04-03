<?php
namespace SwoStar\Server\WebSocket;

class Connections
{
    /**
     * 记录用户的连接
     * [
     *    fd => [
     *        'path' => xxx,
     *        'xxx' => ooo
     *    ]
     * ]
     * @var array
     */
    protected static $connections = [];

    public static function init($fd, $request)
    {
        self::$connections[$fd]['path'] = $request->server['path_info'];
        self::$connections[$fd]['request'] = $request;
    }

    public static function get($fd)
    {
        return self::$connections[$fd];
    }

    public static function del($fd)
    {
        unset(self::$connections[$fd]);
    }
}
