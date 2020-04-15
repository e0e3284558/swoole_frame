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

    // 允许空闲时间
    protected $idleTime = 10;

    protected static $instance = null;

    private function __construct()
    {
        $this->init();
        $this->gc();
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

    public function call($conn, $method, $sql)
    {
        try {
            return $conn['db']->{$method}($sql);
        } catch (Exception $exception) {

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
                $this->count++;
                $connection = $this->createConnection();
                if ($connection) {
//                    $this->channel->push($connection);
//                    $this->count--;
                }
            } else {
                var_dump('等待获取链接');
//                $connection = $this->channel->pop($this->timeout);
            }
        } else {
            // 表示还有空余的链接
            return $this->channel->pop($this->timeout);
        }
    }

    /**
     * 释放连接（重回连接池）
     * @param $connection
     */
    public function freeConnection($connection)
    {
        $connection['last_used_time'] = time();
        $this->channel->push($connection);
    }

    /**
     * 回收空闲的连接
     */
    protected function gc()
    {
        swoole_timer_tick(2000, function () {
            // 记录可用的连接
            $conns = [];
            while (true) {
                if (!$this->channel->isEmpty() && $this->count > $this->minConnection) {
                    $connection = $this->channel->pop();
                    if (empty($connection)) {
                        continue;
                    }
                    if (time()->$connection['last_used_time'] > $this->idleTime) {
                        $connection['db']=null;
                        $this->count--;
                        echo "回收成功 \n";
                    } else {
                        array_push($conns, $connection);
                    }
                } else {
                    break;
                }
            }
            foreach ($conns as $key => $value) {
                $this->channel->push($value);
            }
            echo "当前连接数是多少{$this->count}\n";

        });
    }

    /**
     * 创建连接
     * @return PDO|null
     */
    protected function createConnection()
    {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=swoole', 'root', 'bfccm@db123');
            return [
                'last_used_time' => time(),
                'db' => $pdo,
            ];
        } catch (Exception $e) {
            return false;
        }
    }
}