<?php 
header("content-type:text/html;charset=utf-8");
ini_set('memory_limit','3072M');
set_time_limit(0);
// 确定格式，所有的电话号码开头全部列出
$arr = array(
    130,131,132,133,134,135,136,137,138,139,
    150,151,152,153,155,156,157,158,159,
    176,177,178,
    180,181,182,183,184,185,186,187,188,189,
);
//循环拼接
/*for($i = 0; $i < 1000000; $i++) {
    $tmp[] = $arr[array_rand($arr)].''.mt_rand(1000,9999).''.mt_rand(1000,9999);
}*/
$phone = [];
$ph = 13527400000;
for ($i=0; $i <100000; $i++) { 
	$phone[] = $ph+$i;
}

function chunk($list, $num)
{
    $temp = [];
    //判断数组
    if (!is_array($list)) {
        return false;
    }
    //判断数量是否小于列数   小于 直接返回第一列
    if (count($list) < $num) {
        return $temp[] = $list;
    }
    //向上取整
    $argv = ceil(count($list) / $num);
    //循环切片
    for ($i = 1; $i <= $num; $i++) {
        $temp[$i] = array_slice($list, $argv * ($i - 1), $argv);
    }
    return $temp;
}

//去掉重复
//$phone = array_unique($tmp);
//统计下个数
//$name = count($phone);
//自动换行
$phone = chunk($phone,10);
/*$str = implode("\r\n", $phone[1]);
//var_export(array_unique($phone));
 
//写入文档
file_put_contents("13983-1.csv",$str);
$str = implode("\r\n", $phone[2]);
//var_export(array_unique($phone));
 
//写入文档
file_put_contents("13983-2.csv",$str);
$str = implode("\r\n", $phone[3]);
//var_export(array_unique($phone));
 
//写入文档
file_put_contents("13983-3.csv",$str);*/
for ($i=1; $i <=10; $i++) { 
	$str = implode("\r\n", $phone[$i]);
	file_put_contents("135274-$i".'.csv', $str);
}
 

