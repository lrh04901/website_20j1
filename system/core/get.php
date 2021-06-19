<?php
include "redirect.php";

/**
 * get处理模块
 */
class get
{
    //Array ( [0] => title=更新站点 [1] => css=zui.min,zui.uploader.min,update [2] => js=zui.uploader.min,webuploader.min,update [3] => )
    /**
     * 加载指定页面
     * @param string $name 页面名称
     * @return void
     */
    public static function load(string $name):void
    {
        $html = file_get_contents(HTML_PATH . $name . ".html");
        $args = strstr($html, "\n", true);
        $html = str_replace($args, "", $html);
        for ($i = 0; $i < count(L); $i++) {
            $key = array_keys(L)[$i];
            $args = str_replace("{" . $key . "}", L[$key], $args);
        }
        if (substr($args, 0, 4) == "<!--") {
            $args = str_replace("<!--", "", str_replace("-->", "", $args));
            $args = trim($args, " ");
            $args_array = explode(";", $args);
            if (explode("=", $args_array[0])[0] == "title") {
                $title = explode("=", $args_array[0])[1];
                $css = array();
                foreach (explode(",", substr($args_array[1], 4)) as $item) {
                    if (strstr($item, "(") && strstr($item, ")")) {
                        $css_name = strstr($item, "(", true);
                        $css_args = trim(str_replace($css_name, "", $item), "()");
                        $css[count($css)] = [$css_name, $css_args];
                    } else {
                        if ($item) {
                            $css[count($css)] = $item;
                        }
                    }
                }
                $js = array();
                foreach (explode(",", explode("=", $args_array[2])[1]) as $item) {
                    if ($item) {
                        $js[count($js)] = $item;
                    }
                }
                core::loadHead($title, ["css" => $css, "js" => $js]);
                core::loadBodyByText($html);
            } else {
                if (explode("=", $args_array[0])[0] == "function") {
                    $a = trim(explode("=", $args_array[0])[1]);
                    call_user_func("get::$a");
                }
            }
        } else {
            core::loadErrorPage("无法加载", "当前页面存在一些问题，所以你暂时无法访问这个页面。");
        }
    }
    /*public static function index()
    {
        self::oneKeyLoad("index");
//        loadHead(L["INDEX_TITLE"], ["css" => [["index", "media=\"screen\""]],"js"=>["jquery"]]);
//        loadBody("index");
    }

    public static function update()
    {
        self::oneKeyLoad("update");
//        loadHead("更新站点", ["css" => ["zui.min", "zui.uploader.min", "update"], "js" => ["zui.uploader.min", "webuploader.min", "update"]]);
//        loadBody("update");
    }

    public static function classIntroduce()
    {
        self::oneKeyLoad("classIntroduce");
//        loadHead("胡杨班", ["css" => [["classIntroduce", "media=\"screen\""]]]);
//        loadBody("classIntroduce");
    }

    public static function yule()
    {
        self::oneKeyLoad("yule");
//        loadHead("娱乐", ["css" => [["yule", "media=\"screen\""]],"js"=>["jquery"]]);
//        loadBody("yule");
    }*/
    /*
        public static  function Page404()
        {
            core::loadHead("404 Not Found", ["css" => [["Page404", "media=\"screen\""]]]);
            loadBody("Page404");
        }*/
    /**
     * 设置语言
     * @return void
     */
    public static function setLang():void
    {
        $lang = argsTool::get("lang");
        $from = argsTool::get("from");
        cookie::set("local_language", $lang);
        header("Location:./?/" . $from);
    }
}