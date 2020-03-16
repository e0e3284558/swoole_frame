<?php

class SwooleTask
{
    protected $queueId;
    protected $workerId;
    protected $taskId = 1;

    const SW_TASK_TMPFILE = 1;  //tmp file
    const SW_TASK_SERIALIZE = 2;  //php serialize
    const SW_TASK_NONBLOCK = 4;  //task

    const SW_EVENT_TASK = 7;

    /**
     * SwooleTask constructor.
     * @param $key
     * @param int $workerId
     * @throws Exception
     */
    function __construct($key, $workerId = 0)
    {
        $this->queueId = msg_get_queue($key);
        if ($this->queueId === false) {
            throw new \Exception("msg_get_queue() failed.");
        }
        $this->workerId = $workerId;
    }

    protected static function pack($taskId, $data)
    {
        $flags = self::SW_TASK_NONBLOCK;
        $type = self::SW_EVENT_TASK;
        if (!is_string($data)) {
            $data = serialize($data);
            $flags |= self::SW_TASK_SERIALIZE;
        }
        if (strlen($data) >= 8180) {
            $tmpFile = tempnam('/tmp/', 'swoole.task');
            file_put_contents($tmpFile, $data);
            $data = pack('l', strlen($data)) . $tmpFile . "\0";
            $flags |= self::SW_TASK_TMPFILE;
            $len = 128 + 24;
        } else {
            $len = strlen($data);
        }

        return pack('lSsCCS', $taskId, $len, 0, $type, 0, $flags) . $data;
    }

    function dispatch($data)
    {
        $taskId = $this->taskId++;
        if (!msg_send($this->queueId, 2, self::pack($taskId, $data), false)) {
            return false;
        } else {
            return $taskId;
        }
    }
}

echo "Sending text to msg queue.\n";
$task = new SwooleTask(822161698, 3);
//普通字符串
$task->dispatch("Hello from PHP!");