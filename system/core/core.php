<?php
include "redirect.php";
/**
 * class core
 * <br>
 * 核心模块，用于加载其余模块、加载网页等
 */
class core
{
    //初始化
    public static function initialize()
    {
        self::loadComponent("define");//加载定义模块
        self::loadComponent("get");//加载get请求模块
        self::loadComponent("post");//加载post请求模块
        self::loadComponent("cookie");//加载cookie模块
        self::loadComponent("language");//加载语言模块
        self::loadComponent("argsTool");//加载参数模块
        self::loadComponent("encryptTool");//加载加密模块
        self::loadComponent("dbTool");//加载数据库模块
        self::loadComponent("fileDBTool");//加载文件数据库模块
        self::loadComponent("mysqlTool");//加载MySQL数据库模块
        self::loadComponent("xcpak");//加载XC包模块
        self::loadComponent("uploader");//加载上传模块
    }

//运行网站
    public static function runWeb()
    {
        $rm = strtolower($_SERVER["REQUEST_METHOD"]);
        $path = substr(self::getPath(), 1);
        if (!$path) {
            $path = "index";
        }
        if ($rm == "get") {
            if (file_exists(HTML_PATH . $path . ".html")) {
                get::load($path);
                /*if (!call_user_func($rm."::".$path)){
    //                get::oneKeyLoad($path);
                }*/
            } else {
                self::loadErrorPage("404 Not Found", "找不到你要访问的页面或资源");
            }
        } elseif ($rm == "post") {
            if (function_exists("post::" . $path)) {
                call_user_func($rm . "::" . $path);
            } else {
                die("404 not fount:找不到你要访问的页面或资源");
            }
        }
//    call_user_func($rm . "::" . $path);
    }

//获取当前虚拟地址
    public static function getPath()
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
        if (substr($c, 0, 1) != "/") {
            header("Location:./?/");
        }
        return $c;//返回地址
    }

//加载模块
    public static function loadComponent($name)
    {
        if (defined("CORE_PATH")) {
            $component_path = CORE_PATH . $name . ".php";
        } else {
            $component_path = "system/core/" . $name . ".php";
        }
        if (file_exists($component_path)) {
            include($component_path);
        } else {
            echo "<b style='color: red'>没有找到组件：" . $name . "</b>";
            die();
        }
    }

//加载头部文件
    public static function loadHead(string $title, array $extraFile = null): void
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
                    if (is_string($t)) {
                        echo "<link href='" . CSS_PATH . $t . ".css' rel='stylesheet' type='text/css'>";
                    } elseif (is_array($t)) {
                        echo "<link href='" . CSS_PATH . $t[0] . ".css' rel='stylesheet' type='text/css' " . $t[1] . ">";
                    }
                }
            }
        }
        $value = str_replace("{extraFile}", $extraFileText, $value);
        $value = self::link_process($value);
        echo $value;
    }

//加载页面主体文件
    public static function loadBody($name, $args = null)
    {
        $value = file_get_contents(HTML_PATH . $name . ".html");
        if ($args) {
            for ($i = 0; $i < count($args); $i++) {
                $key = array_keys($args)[$i];
                $value = str_replace("{" . $key . "}", $args[$key], $value);
            }
        }
        $value = self::link_process($value);
        echo $value;
    }

    public static function loadBodyByText($content, $args = null)
    {
        $value = $content;
        if ($args) {
            for ($i = 0; $i < count($args); $i++) {
                $key = array_keys($args)[$i];
                $value = str_replace("{" . $key . "}", $args[$key], $value);
            }
        }
        $value = self::link_process($value);
        echo $value;
    }

    /**
     * @param string $title 错误页面的标题
     * @param string $message 错误消息
     */
    public static function loadErrorPage(string $title, string $message)
    {
        self::loadHead($title);
        self::loadBody("error", ["ERROR_TITLE" => $title, "ERROR_CONTENT" => $message]);
    }

    public static function link_process($input): string
    {
        $value = $input;
        $index = 0;
        $args_list = array();
        $args_index = 0;
        $left = false;
        $value = str_replace("{nav}", file_get_contents(HTML_PATH . "nav.html"), $value);
        for ($i = 0; $i < count(L); $i++) {
            $key = array_keys(L)[$i];
            $value = str_replace("{TEXT_" . $key . "}", L[$key], $value);
        }
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
                if ($t === " " || $t === "\r" || $t === "\n") {
                    $left = false;
                    continue;
                }
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
            } elseif ($x[0] === "JS") {
                $value = str_replace("{" . $item . "}", JS_PATH . $x[1] . ".js", $value);
            } elseif ($x[0] === "MEDIA") {
                $value = str_replace("{" . $item . "}", MEDIA_PATH . $x[1] . "." . $x[2], $value);
            } elseif ($x[0] === "AUDIO") {
                $value = str_replace("{" . $item . "}", AUDIO_PATH . $x[1] . "." . $x[2], $value);
            } elseif ($x[0] === "FONT") {
                $value = str_replace("{" . $item . "}", FONT_PATH . $x[1] . "." . $x[2], $value);
            }
        }
        return $value;
    }

    /*function clearConsole()
    {
        echo "<script>console.clear();</script>";
    }*/
}