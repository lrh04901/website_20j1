<?php

/**
 * get处理模块
 */
class get
{
    /**
     * 加载指定页面
     * @param string $name 页面名称
     * @return void
     */
    public static function load(string $name): void
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

    /**
     * 设置语言
     * @return void
     */
    public static function setLang(): void
    {
        $lang = argsTool::get("lang");
        $from = argsTool::get("from");
        cookie::set("local_language", $lang);
        header("Location:./?/" . $from);
    }

    /**
     * 判断是否登录
     * @return void
     */
    public static function isLogin(): void
    {
        $isLogin = cookie::get("isLogin");
        if ($isLogin === "yes") {
            core::loadErrorPage("登录状态", "已登录");
        } else {
            core::loadErrorPage("登录状态", "未登录,<a href='./?/login'>去登录</a>。");
        }
    }

    public static function dbControl():void{
        $secret = argsTool::get("password");
        if (hash("sha256",$secret)=="63cc084612161460d763510777475c58fa6cf87b05e51a9774526e527a6e0a09" or cookie::get("allow_visit_db_control")=="yes"){
            cookie::set("allow_visit_db_control","yes");
            if (argsTool::get("password")!="null"){
                header("Location:./?/dbControl");
            }
        }else{
            core::loadErrorPage("拒绝访问","你没有权限进入当前页面");
            die();
        }
        core::debugMessage("数据库","欢迎来到数据库控制页面");
        core::debugMessage("数据库","数据库类型：".dbTool::DBType());
//        core::debugMessage("数据库","删除数据表users：".json_encode(dbTool::deleteTable("users")));
        core::debugMessage("数据库","数据表users".(dbTool::tableExist("users")=="yes"?"存在":"不存在"));
//        core::debugMessage("数据库","创建数据表：".dbTool::createTable("users",["username","name","password","email","ban","banReason","admin"])["status"]);
//        core::debugMessage("数据库","插入数据：".dbTool::insert("users",["username","name","password","email","ban","banReason","admin"],["sch","sch","123","sch@20j1.cn","","","yes"])["status"]);
//        core::debugMessage("数据库","删除数据：".dbTool::deleteRow("users",["username"=>"sch"])["status"]);
        core::debugMessage("数据库","读取数据：".json_encode(dbTool::select("users",["*"],["username"=>"sch"])));
    }

    public static function dbAdmin()
    {
        dbAdmin::run();
    }

    public static function phpinfo()
    {
        phpinfo();
    }
}