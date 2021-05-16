<?php

initialize();
switch (getPath()){
    case "/":
        get::index();
        break;
    default:
        break;

}

//初始化
function initialize(){
    include("system/core/define.php");
    loadComponent("get");//加载get请求模块
    loadComponent("cookie");//加载cookie模块
    loadComponent("language");//加载语言模块
}

//获取当前虚拟地址
function getPath()
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

//加载模块
function loadComponent($name){
    include(CORE_PATH.$name.".php");
}

//加载头部文件
function loadHead(string $title,array $extraFile=null):void
{
    $value = file_get_contents(HTML_PATH . "head.html");//读取头部文件
    $value = str_replace("{TITLE}", $title, $value);//设置标题
    $extraFileText = "";
    if ($extraFile){
        if (isset($extraFile["js"])){
            $js = $extraFile["js"];
            for ($i = 0;$i<count($js);$i++){
                $extraFileText.="<script src='".JS_PATH.$js[$i].".js'></script>";
            }
        }
        if (isset($extraFile["css"])){
            $css = $extraFile["css"];
            for ($i = 0;$i<count($css);$i++){
                $extraFileText.="<link href='".CSS_PATH.$css[$i].".css' rel='stylesheet' type='text/css'>";
            }
        }
    }
    $value = str_replace("{extraFile}",$extraFileText,$value);
    echo $value;
}

//加载HTML文件
function loadHTML($name, $args = null)
{
    $value = file_get_contents(HTML_PATH . $name . ".html");
    for ($i = 0;$i<count(L);$i++){
        $key = array_keys(L)[$i];
        $value = str_replace("{".$key."}",L[$key],$value);
    }
    if ($args) {
        for ($i = 0; $i < count($args); $i++) {
            $key = array_keys($args)[$i];
            $value = str_replace("{" . $key . "}", $args[$key], $value);
        }
    }
    echo $value;
}