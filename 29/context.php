<?php
use Swoole\Coroutine;
// 帮助我们对协程操作的全局变量值做一个隔离
// 转意为：解决mysql的脏读问题
class Context
{
    /**
     * [
     *    'cid' => [  // 就是协程的id
     *        'key' => 'value' // 保存的全局变量的信息
     *    ]
     * ]
     * @var [type]
     */
    protected static $pool = [];

    static function get($key)
    {
        $cid = Coroutine::getuid();// 获取当前运行的协程id
        if ($cid < 0)
        {
            return null;
        }
        if(isset(self::$pool[$cid][$key])){
            return self::$pool[$cid][$key];
        }
        return null;
    }

    static function put($key, $item)
    {
        $cid = Coroutine::getuid();// 获取当前运行的协程id
        if ($cid > 0)
        {
            self::$pool[$cid][$key] = $item;
        }


    }

    static function delete($key = null)
    {
        $cid = Coroutine::getuid();
        if ($cid > 0)
        {
            if($key){
                unset(self::$pool[$cid][$key]);
            }else{
                unset(self::$pool[$cid]);
            }
        }
        var_dump(self::$pool);
    }
}
