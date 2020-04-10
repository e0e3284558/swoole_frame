<?php

namespace SwoCloud\Server\Traits;

use Swoole\Table;
use Swoole\Coroutine\Http\Client;
use Co;


trait AckTraits
{
    protected $table;

    public function createTable()
    {
        $this->table = new Table(1024);
        $this->table->column('ack', Table::TYPE_INT, 2);       //1,2,4,8
        $this->table->column('num', Table::TYPE_INT, 2);
        dd('创建共享缓存');
        $this->table->create();
    }

    /**
     *
     */
    public function confirmGo($unipid, $data, Client $client)
    {
        go(function () use ($unipid, $data, $client) {
            while (true) {
                Co::sleep(1);
                // 获取im-server回复的确认的消息
                $ackData = $client->recv(0.2);
                $ackInfo = json_decode($ackData->data, true);
                // 判断类型-是否为确认
                if (isset($ackInfo['method']) && $ackInfo['method'] == 'ack') {
                    // 确认信息
                    $this->table->incr($ackInfo['msg_id'], 'ack');
                }

                // 判断是否任务确认
                // 获取任务对应的状态
                $task = $this->table->get($unipid);

                // 是否被确认
                if ($task['ack'] > 0 || $task['num'] >= 3) {
                    dd('清空任务', $unipid);
                    $this->table->del($unipid);
                    $client->close();
                    break;
                } else {
                    // 重试发送信息
                    $client->push(json_decode($data));
                }
                $this->table->incr($unipid+1, 'num');
                dd('任务重试+1');

            }
        });
    }

    /**
     * 定时器
     */
    public function confirmTick()
    {

    }
}