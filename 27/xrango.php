<?php


//foreach (range(1,1000000000000) as $key=>$value){
//    var_dump($value);
//
//}


// 生成器
function xrange($start, $end)
{
    // 通过for继续执行
    for ($i = $start; $i <= $end; $i++) {
        echo "执行i {$i} \n";
        // 程序暂停，返回结果，for会继续
        yield $i;
        // 终止了for
        // return $i;
    }
}


//foreach (xrange(1, 1000000000) as $key => $value) {
////    var_dump($value);
//}
$ger = xrange(1, 10);
var_dump($ger->current());
$ger->next();
var_dump($ger->current());

echo "执行完成\n";