<?php

use Swoole\Coroutine\Channel;

class Pool
{
    /**
     * 避免超标
     * @var int
     */
    protected $maxConnection = 30;

    /**
     * 最少连接个数，就是在初始化的时候事先创建多少
     *
     * 创建太多会消耗内存，也不一定能得到
     * @var int
     */
    protected $minConnection = 5;

    /**
     * 在协程中使用保存创建的连接个数
     * @var
     */
    protected $channel;

    /**
     * 从连接池中获取超时的时间
     * @var int
     */
    protected $timeout = 3;

    protected $count = 0;

    protected static $instance = null;

    private function __construct()
    {
        $this->init();
    }

    /**
     * @return static|null
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 初始化，创建连接
     */
    protected function init()
    {
        $this->channel = new Channel($this->maxConnection);
        for ($i = 0; $i < $this->minConnection; $i++) {
            $connection = $this->createConnection();
            if ($connection) {
                $this->channel->push($connection);
                $this->count++;
            }
        }
    }

    public function getConnection()
    {
        $connection = null;
        // 表示没有连接可用
        if ($this->channel->isEmpty()) {
            // 没有链接需要创建
            if ($this->count < $this->maxConnection) {
                // 判断是否超过最大的链接个数
                $connection = $this->createConnection();
                if ($connection) {
                    $this->channel->push($connection);
                    $this->count++;
                }
            } else {
                var_dump('等待获取链接');
                $connection = $this->channel->pop($this->timeout);
            }
        }else{
            // 表示还有空余的链接
            $connection = $this->channel->pop($this->timeout);
        }
        return $connection;
    }

    /**
     * 释放连接（重回连接池）
     * @param $connection
     */
    public function freeConnection($connection)
    {
        $this->channel->push($connection);
    }

    /**
     * 创建连接
     * @return PDO|null
     */
    protected function createConnection()
    {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=swoole', 'root', 'bfccm@db123');
            return $pdo;
        } catch (Exception $e) {
            return false;
        }
    }
}