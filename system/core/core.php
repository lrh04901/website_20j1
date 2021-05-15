<?php
include("system/core/define.php");
loadHead("欢迎");
die("<h1>欢迎访问20J1网站</h1>");



function getPath()//获取当前虚拟地址
{
    $a = "/";//未加参数的情况
    if (isset($_SERVER['QUERY_STRING'])) {//指定地址参数时
        $a = $_SERVER['QUERY_STRING'];//读取请求参数的内容
    }
    $b = stripos($a, "&");//获取&符号第一次出现位置
    if ($b != "") {
        $c = substr($a, 0, $b);//获取第一参数
    } else {
        $c = $a;//未加参数时返回/
    }
    if ($c == "") {
        $c = "/";//参数为空时返回/
    }
    if (substr($c, strlen($c) - 1, 1) == "/") {
        $c = substr($c, 0, strlen($c) - 1);//若地址最后存在一个/，将删除这个/
    }
    if ($c == "") {
        $c = "/";//地址未空时变为/
    }
    return $c;//返回地址
}
function loadHead($title){
    $value = file_get_contents(HTML_PATH."head.html");
    $value = str_replace("{TITLE}",$title,$value);
    echo $value;
}