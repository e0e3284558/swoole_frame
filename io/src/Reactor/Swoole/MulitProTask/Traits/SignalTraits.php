<?php
namespace Bifei\Io\Reactor\Swoole\MulitProTask\Traits;

trait SignalTraits
{
    /**
     * 不同信号的处理函数
     * 六星教育 @shineyork老师
     * @param  [type] $sig [description]
     * @return [type]      [description]
     */
    protected function sigHandler($sig)
    {
        switch ($sig) {
          case SIGUSR1:
            //重启
            $this->reload();
            break;
          case SIGINT:
            // 停止
            $this->stop();
            break;
        }
    }
    /**
     * 安装linux信号-》循环的监听安装
     * 六星教育 @shineyork老师
     * @return [type] [description]
     */
    protected function monitorWorkersForLinux()
    {
         // 信号安装
         pcntl_signal(SIGUSR1, [$this, 'sigHandler'], false);
         pcntl_signal(SIGINT, [$this, 'sigHandler'], false);
         while (1) {
             pcntl_signal_dispatch();
             // 死循环有几个子进程就可以回收几次
             pcntl_wait($status);
             pcntl_signal_dispatch();
         }
    }
}
