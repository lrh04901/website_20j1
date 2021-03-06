<?php

/**
 * 核心模块，用于加载其余模块、加载网页等
 */
class core
{
    /**
     * 初始化程序，加载一些模块，并进行一些配置
     * @return void
     */
    public static function initialize(): void
    {
//        echo "[Debug Info]<br>";
        include("./system/core/core.phar");//加载核心库
        self::loadComponent("define");//加载定义模块
        if (json_decode(file_get_contents(CONFIG_PATH."global_config.json"),true)["proxy"]=="yes") {//加载反向代理唯一码
            define("PROXY_CODE", $_GET["PROXY_CODE"]);
            unset($_GET["PROXY_CODE"]);
        }
        self::loadComponent("encryptTool");//加载加密模块
        self::loadComponent("cookie");//加载cookie模块
        self::loadComponent("language");//加载语言模块
        self::loadComponent("get");//加载get请求模块
        self::loadComponent("post");//加载post请求模块
        self::loadComponent("argsTool");//加载参数模块
        self::loadComponent("dbTool");//加载数据库模块
        self::loadComponent("fileDBTool");//加载文件数据库模块
        self::loadComponent("mysqlTool");//加载MySQL数据库模块
        self::loadComponent("xcpak");//加载XC包模块
        self::loadComponent("uploader");//加载上传模块
        self::loadComponent("arrayTool");//加载数组模块
        self::loadComponent("dbAdmin");//加载数据库管理
        self::loadComponent("phpIniTool");//加载PHP配置文件模块
        self::loadComponent("zipTool");//加载zip模块
        self::loadComponent("configTool");//加载配置文件模块
        include(CORE_PATH . "mail.phar");//加载邮件库
        include(CORE_PATH . "phpExcel.phar");//加载Excel库
        include(CORE_PATH . "phpWord.phar");//加载Word库
        date_default_timezone_set("PRC");//设置时区
//        echo "PATH defined : ". (defined("PATH")?"yes":"no")."<br>";
//        echo "PATH = ".PATH."<br>";
//        echo "HTML_PATH = ".HTML_PATH."<br>";
//        echo "visit path = " . self::getPath()."<br>";
//        $path = self::getPath();
//        $path = substr($path,1);
//        if (!$path){
//            $path="index";
//        }
//        echo "$"."path = $path<br>";
//        $html_data = file_get_contents(HTML_PATH.$path.".html");
//        echo "read data ：" . ($html_data?"success":"fail")."<br>";
//        echo "file exist ：".(file_exists(HTML_PATH.$path.".html")?"yes":"no");
        include(PATH."autoexec.php");
    }

    /**
     * 加载网页，根据不同请求方式及请求路径加载不同的代码
     * @return void
     */
    public static function runWeb(): void
    {
        $rm = strtolower($_SERVER["REQUEST_METHOD"]);
        $path = substr(self::getPath(), 1);
        if (!$path) {
            $path = "index";
        }
        if ($rm == "get") {
            if (@file_get_contents(HTML_PATH . $path . ".html")) {
                get::load($path);
                /*if (!call_user_func($rm."::".$path)){
    //                get::oneKeyLoad($path);
                }*/
            } else {
                self::loadErrorPage("404 Not Found", "找不到你要访问的页面或资源");
            }
        } elseif ($rm == "post") {
//            echo function_exists("post::login")?"yes":"no";
//            if (function_exists("post::" . $path)) {
//                call_user_func($rm . "::" . $path);
//            } else {
//                die("404 not fount:找不到你要访问的页面或资源");
//            }
            if (!call_user_func("post::$path")){
                die("404 not fount:找不到你要访问的页面或资源");
            }
        }
//    call_user_func($rm . "::" . $path);
    }

    /**
     * 返回当前请求的虚拟地址
     * @return string 返回虚拟地址
     */
    public static function getPath(): string
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

    /**
     * 加载模块，找不到指定模块将会终止网页执行
     * @param string $name 模块名
     * @return void
     */
    public static function loadComponent(string $name): void
    {
        if (file_exists("debug")){//调试模式
            $component_path = "src/core/$name.php";
        }else{
            $component_path = "phar://core.phar/$name.php";
        }

        /*if (defined("CORE_PATH")) {
            $component_path = CORE_PATH . $name . ".php";
        } else {
            if (file_exists("./system/core/define.php")) {
                $component_path = "system/core/dev/$name.php";
            }else{
                $component_path = "phar://core.phar/$name.php";
            }
        }
        if (defined("DEBUG")&&DEBUG=="yes"){
            $component_path = "./system/core/dev/$name.php";
        }else{
            $component_path = "phar://core.phar/$name.php";
        }*/
//        echo $component_path."<br>";
//        echo "loading <mark>$name</mark> component...";
        if (file_exists($component_path)) {
            include($component_path);
//            echo "<mark>$name</mark> component load successful!<br>";
        } else {
            self::loadErrorPage("网站加载失败", "<b style='color: red'>没有找到模块：" . $name . "</b>");
            die();
        }
    }

    /**
     * 加载网页的头部文件，有助于简化每个页面的代码
     * @param string $title 网页标题
     * @param array|null $extraFile 加载外部引用文件，可用于加载css和js
     * @return void
     */
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
                        $extraFileText .= "<link href='" . CSS_PATH . $t . ".css' rel='stylesheet' type='text/css'>";
                    } elseif (is_array($t)) {
                        $extraFileText .= "<link href='" . CSS_PATH . $t[0] . ".css' rel='stylesheet' type='text/css' " . $t[1] . ">";
                    }
                }
            }
            if (isset($extraFile["mobile"])){
                if ($extraFile["mobile"]=="yes"){
                    $extraFileText .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">';
                }
            }
        }
        $value = str_replace("{extraFile}", $extraFileText, $value);
        $value = self::link_process($value);
        echo  "<html lang='".LOCAL_LANGUAGE."'>";
        echo $value;
    }

    /**
     * 通过文件加载主体部分代码，根据不同文件名加载不同页面
     * @param string $name 加载页面的文件名
     * @param array|null $args 页面参数，用于替换代码中的引用数据，优先级比link_process高
     * @return void
     */
    public static function loadBody(string $name, array $args = null): void
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

    /**
     * 通过代码文本加载主体部分，将处理过后的代码进行加载
     * @param string $content 页面的代码文本
     * @param array|null $args 页面参数，用于替换代码中的引用数据，优先级比link_process高
     * @return void
     */
    public static function loadBodyByText(string $content, array $args = null): void
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
     * 加载一个错误页面
     * @param string $title 错误页面的标题
     * @param string $message 错误页面的消息
     * @return void
     */
    public static function loadErrorPage(string $title, string $message): void
    {
        self::loadHead($title);
        self::loadBody("error", ["ERROR_TITLE" => $title, "ERROR_CONTENT" => $message]);
    }

    /**
     * 对代码文本进行处理，将代码中的一些引用数据替换成对应内容
     * @param string $input 将需要处理的代码文本输入
     * @return string 返回处理后的代码文本
     */
    public static function link_process(string $input): string
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
        $language_list = json_decode(file_get_contents(LANG_PATH."languages.json"));
        $language_list_data = "";
        foreach ($language_list as $language) {
            $language_name = explode("=",explode("\n",str_replace("\r","",file_get_contents(LANG_PATH.$language.".lang")))[0])[1];
            $language_list_data.='<li><a data-language="'.$language.'" class="lang">'.$language_name.'</a></li>';
        }
        $value = str_replace("{language_list}",$language_list_data,$value);
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
                @$args_list[$args_index] .= $t;
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
                $rewrite = json_decode(file_get_contents(CONFIG_PATH."global_config.json"),true)["rewrite"]=="yes";
                if ($path=="/index"){
                    $path = "/";
                }
                if ($rewrite){//是否开启伪静态
                    $value = str_replace("{" . $item . "}",$path,$value);
                }else {
                    $value = str_replace("{" . $item . "}", "./?" . $path, $value);
                }
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

    /**
     * 清空浏览器控制台内容
     * @return void
     */
    public static function clearConsole(): void
    {
        echo "<script>console.clear();</script>";
    }

    /**
     * 在网页中打印一条调试消息
     * @param string $label 消息的标签
     * @param string $message 消息的内容
     * @param bool $die 在打印完后是否终止程序的执行
     * @return void
     */
    public static function debugMessage(string $label, string $message, bool $die = false): void
    {
        $time = date("m-d H:i");
        echo "[$time $label] $message<br>";
        if ($die)die();
    }
}
