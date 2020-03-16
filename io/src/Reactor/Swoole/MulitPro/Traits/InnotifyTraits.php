<?php

namespace Bifei\Io\Reactor\Swoole\MulitPro\Traits;

trait InnotifyTraits
{
    public function watchEvent()
    {
        return function ($event) {
            $action = 'file:';
            switch ($event['mask']){
                case IN_CREATE:
                    $action='IN_CREATE';
                    break;
                case IN_DELETE:
                    $action='IN_DELETE';
                    break;
                case IN_MODIFY:
                    $action='IN_MODIFY';
                    break;
                case IN_MOVE:
                    $action='IN_MOVE';
                    break;
            }
            debug('worker reloaded by inotify:'.$action.':'.$event['name']);
            posix_kill((pidGet($this->config['master_pid_files']))[0],SIGUSR1);
        };
    }
}