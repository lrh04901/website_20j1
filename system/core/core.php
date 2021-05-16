<?php

initialize();
runWeb();

//初始化
function initialize()
{
    include("system/core/define.php");//加载定义模块
    loadComponent("get");//加载get请求模块
    loadComponent("cookie");//加载cookie模块
    loadComponent("language");//加载语言模块
    loadComponent("argsTool");//加载参数模块
    loadComponent("encryptTool");//加载加密模块
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
    return $c;//返回地址
}

//加载模块
function loadComponent($name)
{
    include(CORE_PATH . $name . ".php");
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
                if (is_array($css)) {
                    if (substr($css[$i], 0, 7) === "http://" || substr($css[$i], 0, 8) === "https://" || substr($css[$i], 0, 2) === "//") {
                        $extraFileText .= "<link href='" . $css[$i][0] . "' rel='stylesheet' type='text/css' " . $css[$i][1] . ">";
                    } else {
                        $extraFileText .= "<link href='" . CSS_PATH . $css[$i][0] . ".css' rel='stylesheet' type='text/css' " . $css[$i][1] . ">";
                    }
                } elseif (is_string($css)) {
                    if (substr($css[$i], 0, 7) === "http://" || substr($css[$i], 0, 8) === "https://" || substr($css[$i], 0, 2) === "//") {
                        $extraFileText .= "<link href='" . $css[$i] . "' rel='stylesheet' type='text/css'>";
                    } else {
                        $extraFileText .= "<link href='" . CSS_PATH . $css[$i] . ".css' rel='stylesheet' type='text/css'>";
                    }
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
        $value = str_replace("{" . $key . "}", L[$key], $value);
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
        }
    }
    echo $value;
}

function clearConsole()
{
    echo "<script>console.clear();</script>";
}