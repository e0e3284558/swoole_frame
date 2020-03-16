<?php
// 1.子进程的pid
// 2.0
// 3.负数-->代表
//$son = pcntl_fork();

//if ($son > 0) {
//    // 父进程空间
//    $son11 = pcntl_fork();
//} elseif ($son < 0) {
//    echo "失败";
//} else {
//    // 小于0 子进程空间
//    echo $son . ":i\n";
//    echo $son11 . ":i\n";
//}

for ($i = 0; $i < 4; $i++) {
    $son11 = pcntl_fork();
    if ($son11 > 0) {
        // 父进程空间
        echo posix_getpid()."\t";
    } else if ($son11 < 0) {
        echo "失败";
    } else {
        echo posix_getpid()."\t";
        // 小于0 子进程空间
        echo "son:".$son . ":--i\n";
        echo "son11:".$son11 . ":i\n";
        break;
    }
}


// 配合 for循环
if ($son11 > 0) {
    $status = 0;
    var_dump( pcntl_wait($status)); // 阻塞
}


while (true) {

}