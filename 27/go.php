<?php
function queryMysql()
{
    sleep(2);
    echo "mysql \n";
}

function fileWrite()
{
    sleep(2);
    echo "file \n";
}

queryMysql();
fileWrite();

echo "任务执行完成 ";