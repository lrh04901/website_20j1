<?php
initialize();
runWeb();
//初始化
function initialize()
{
    loadComponent("define");//加载定义模块
    loadComponent("get");//加载get请求模块
    loadComponent("post");//加载post请求模块
    loadComponent("cookie");//加载cookie模块
    loadComponent("language");//加载语言模块
    loadComponent("argsTool");//加载参数模块
    loadComponent("encryptTool");//加载加密模块
    loadComponent("dbTool");//加载数据库模块
    loadComponent("fileDBTool");//加载文件数据库模块
    loadComponent("mysqlTool");//加载MySQL数据库模块
}

function runWeb()
{
    switch ($_SERVER["REQUEST_METHOD"]) {
        case "GET":
            switch (getPath()) {
                case "/":
                    get::index();
                    break;
                case "/update":
                    get::update();
                    break;
                case "/classIntroduce":
                    get::classIntroduce();
                    break;
                default:
                    break;

            }
            break;
        case "POST":
            switch (getPath()){
                case "/update_upload":
                    post::update_uploader();
                    break;
            }
            break;
        default:
            echo "不支持的请求方式";
            break;
    }
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
    if (substr($c,0,1)!="/"){
        header("Location:./?/");
    }
    return $c;//返回地址
}

//加载模块
function loadComponent($name)
{
    if (defined("CORE_PATH")) {
        $component_path = CORE_PATH . $name . ".php";
    }else{
        $component_path = "system/core/" . $name . ".php";
    }
    if (file_exists($component_path)) {
        include($component_path);
    }else{
        echo "<b style='color: red'>没有找到组件：".$name."</b>";
        die();
    }
}

//加载头部文件
function loadHead(string $title, array $extraFile = null): void
{
    $value = file_get_contents(HTML_PATH . "head.html");//读取头部文件
    $value = str_replace("{TITLE}", $title, $value);//设置标题
    $extraFileText = "";
    if ($extraFile) {
        if (isset($extraFile["js"])) {
            $js = $extraFile["js"];
            for ($i = 0; $i < count($js); $i++) {
                if (substr($js[$i], 0, 7) === "http://" || substr($js[$i], 0, 8) === "https://" || substr($js[$i], 0, 2) === "//") {
                    $extraFileText .= "<script src='" . $js[$i] . "'></script>";
                } else {
                    $extraFileText .= "<script src='" . JS_PATH . $js[$i] . ".js'></script>";
                }
            }
        }
        if (isset($extraFile["css"])) {
            $css = $extraFile["css"];
            for ($i = 0; $i < count($css); $i++) {
                $t = $css[$i];
                if (is_string($t)){
                    echo "<link href='".CSS_PATH.$t.".css' rel='stylesheet' type='text/css'>";
                }elseif (is_array($t)){
                    echo "<link href='".CSS_PATH.$t[0].".css' rel='stylesheet' type='text/css' ".$t[1].">";
                }
            }
        }
    }
    $value = str_replace("{extraFile}", $extraFileText, $value);
    echo $value;
}

//加载HTML文件
function loadHTML($name, $args = null)
{
    $value = file_get_contents(HTML_PATH . $name . ".html");
    for ($i = 0; $i < count(L); $i++) {
        $key = array_keys(L)[$i];
        $value = str_replace("{TEXT_" . $key . "}", L[$key], $value);
    }
    if ($args) {
        for ($i = 0; $i < count($args); $i++) {
            $key = array_keys($args)[$i];
            $value = str_replace("{" . $key . "}", $args[$key], $value);
        }
    }
    $index = 0;
    $args_list = array();
    $args_index = 0;
    $left = false;
    while (true) {
        $t = substr($value, $index, 1);
        $index++;
        if ($t === "{") {
            $left = true;
            continue;
        }
        if ($t === "}") {
            $left = false;
            $args_index++;
        }
        if ($left) {
            $args_list[$args_index] .= $t;
        }
        if ($index === strlen($value)) {
            break;
        }
    }
    foreach ($args_list as $item) {
        $x = explode("_", $item);
        if ($x[0] === "IMG") {
            $value = str_replace("{" . $item . "}", IMG_PATH . $x[1] . "." . $x[2], $value);
        } elseif ($x[0] === "LINK") {
            $path = substr($x[1], 0, 1) === "/" ? $x[1] : "/" . $x[1];
            $value = str_replace("{" . $item . "}", "./?" . $path, $value);
        }elseif ($x[0]==="JS"){
            $value = str_replace("{".$item."}",JS_PATH.$x[1].".js",$value);
        }elseif ($x[0]==="MEDIA"){
            $value = str_replace("{".$item."}",MEDIA_PATH.$x[1].".".$x[2],$value);
        }
    }
    echo $value;
}

function clearConsole()
{
    echo "<script>console.clear();</script>";
}