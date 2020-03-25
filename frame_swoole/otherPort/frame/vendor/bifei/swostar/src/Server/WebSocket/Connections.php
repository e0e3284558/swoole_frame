<?php

namespace SwoStar\Server\WebSocket;

/**
 * 用来链接webSocket
 * Class Connections
 * @package SwoStar\Server\WebSocket
 */
class Connections
{
    /**
     * 根据连接的$fd 记录用户的连接
     * @var array
     * [
     *      $fd=>[
     *          'path'=>xx,
     *          'xxx'=>oo,
     *      ]
     * ]
     */
    protected static $connections = [];

    /**
     * 记录用户链接
     */
    public static function init($fd, $path)
    {
        self::$connections[$fd]['path'] = $path;
    }

    public static function get($fd)
    {
        return self::$connections[$fd];
    }

    public static function del($fd)
    {
        // 应该需要做安全判断
        unset(self::$connections[$fd]);
    }


}