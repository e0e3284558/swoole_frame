<?php

go(function (){
   co::sleep(2);
   echo "mysql \n";
});

go(function (){
    co::sleep(2);
    echo "file \n";
});

echo "任务执行完成 ";