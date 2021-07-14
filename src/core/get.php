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

    public static function dbControl(): void
    {
        $secret = argsTool::get("password");
        if (hash("sha256", $secret) == "63cc084612161460d763510777475c58fa6cf87b05e51a9774526e527a6e0a09" or cookie::get("allow_visit_db_control") == "yes") {
            cookie::set("allow_visit_db_control", "yes");
            if (argsTool::get("password") != "null") {
                header("Location:./?/dbControl");
            }
        } else {
            core::loadErrorPage("拒绝访问", "你没有权限进入当前页面");
            die();
        }
        core::debugMessage("数据库", "欢迎来到数据库控制页面");
        core::debugMessage("数据库", "数据库类型：" . dbTool::DBType());
//        core::debugMessage("数据库","删除数据表users：".json_encode(dbTool::deleteTable("users")));
        core::debugMessage("数据库", "数据表users" . (dbTool::tableExist("users") == "yes" ? "存在" : "不存在"));
//        core::debugMessage("数据库","创建数据表：".dbTool::createTable("users",["username","name","password","email","ban","banReason","admin"])["status"]);
//        core::debugMessage("数据库","插入数据：".dbTool::insert("users",["username","name","password","email","ban","banReason","admin"],["sch","sch","123","sch@20j1.cn","","","yes"])["status"]);
//        core::debugMessage("数据库","删除数据：".dbTool::deleteRow("users",["username"=>"sch"])["status"]);
        core::debugMessage("数据库", "读取数据：" . json_encode(dbTool::select("users", ["*"], ["username" => "sch"])));
    }

    public static function dbAdmin()
    {
        dbAdmin::run();
    }

    public static function phpinfo()
    {
        phpinfo();
    }

    public static function mail()
    {
        require "phar://mail.phar/PHPMailerAutoload.php";
        $address = argsTool::get("mail");
        if ($address == "null") {
            die("请在url参数中指定邮箱地址");
        }
        $config = json_decode(file_get_contents(CONFIG_PATH . "mail_config.json"), true);
        $mail = new PHPMailer(false);
        $mail->isSMTP();
        $mail->Host = $config["mail_smtp"];
        $mail->SMTPAuth = true;
        $mail->Username = $config["mail_username"];
        $mail->Password = $config["mail_password"];
        try {
            $mail->setFrom($config["mail_username"], L["INDEX_TITLE"]);
        } catch (phpmailerException $e) {
        }
        $mail->addAddress($address);
        $mail->CharSet = "UTF-8";
        $mail->Subject = "测试";
        $mail->Body = '<body><div style="font-family: arial;text-align:center;position:absolute;top:0;left:0;right:0;bottom:0;"><div style="width:50%;display: inline-block;vertical-align: middle;border-bottom: 7px solid #176391; position: relative;text-align: left;padding-bottom: 100px;"><div style="font-size: 36px;color:#777; font-weight: 300;border-bottom: 1px solid #ddd; padding-bottom: 5px;margin-bottom: 5px;">测试</div><div style="font-size: 18px;color:#aaa;font-weight: 400;">这是一封测试邮件</div><div style="position:absolute;bottom: -20px;right:0;left:33%;height:7px;background-color: #56b9e8"></div></div><div style="height:100%;display: inline-block;vertical-align: middle;"></div></div></body>';
        $mail->AltBody = "测试";
        try {
            if ($mail->send()) {
                echo "发送成功";
            } else {
                echo "发送失败：" . $mail->ErrorInfo;
            }
        } catch (phpmailerException $e) {
        }
    }

    /**
     * 项目控制台
     * @return void
     */
    public static function projCtrl(): void
    {
        $secret = argsTool::get("password");
        if (hash("sha256", $secret) == "63cc084612161460d763510777475c58fa6cf87b05e51a9774526e527a6e0a09" or cookie::get("allow_visit_proj_ctrl") == "yes") {
            cookie::set("allow_visit_proj_ctrl", "yes");
            if (argsTool::get("password") != "null") {
                header("Location:./?/projCtrl");
            }
        } else {
            core::loadErrorPage("拒绝访问", "你没有权限进入当前页面");
            die();
        }
        $c = argsTool::get("c");
        if ($c == "null") {
            echo "<head><title>项目控制台</title><script src='system/static/js/jquery.min.js'></script><link rel='stylesheet' href='system/static/css/zui.min.css'></head><body><h1>项目控制台</h1><h2>在这里，你可以对这个项目进行一些控制，例如编译、打包</h2><button class='btn btn-link btn-lg' href='javascript:' id='build'> 编译项目</button><button class='btn btn-link btn-lg' href='javascript:' id='pack'>打包项目</button><button class='btn btn-link btn-lg' id='exit'>离开控制台</button><script>$('#build').click(function (){if (confirm('确定编译此项目？')){location.href = './?/projCtrl&c=build';}});$('#pack').click(function() {if (confirm('确定打包此项目？')){location.href = './?/projCtrl&c=pack'}});$('#exit').click(function() {if (confirm('确定离开控制台？')){location.href = './?/projCtrl&c=exit'}});</script></body>";
        } elseif ($c == "build") {
            echo "<head><title>编译项目</title><link rel='stylesheet' href='system/static/css/zui.min.css'></head>";
            echo "<h2>正在编译项目...</h2>";
            $a = scandir(PATH . "src/");
            $readonly = phpIniTool::get("phar.readonly");
            if ($readonly == "On") {
                die("<h2>当前无法编译项目，请手动将<mark>" . phpIniTool::path() . "</mark>中的phar.readonly属性修改为Off</h2>");
            }
            foreach ($a as $item) {
                if ($item != "." and $item != "..") {
                    if (file_exists(CORE_PATH . "$item.phar")) {
                        unlink(CORE_PATH . "$item.phar");
                    }
                    if (file_exists("src/$item/index.php")) {
                        unlink("src/$item/index.php");
                    }
                    touch("src/$item/index.php");
                    file_put_contents("src/$item/index.php", base64_decode("PD9waHAKLyrljaDkvY3mlofku7YqLw=="));
                    $phar = new Phar(CORE_PATH . "$item.phar", 0, "$item.phar");
                    $phar->buildFromDirectory("src/$item");
                    $phar->setStub(Phar::createDefaultStub("index.php", "index.php"));
                    $phar->compressFiles(Phar::GZ);
                    unlink("src/$item/index.php");
                    echo "<h2>$item 部分已编译完成 -> $item.phar</h2>";
                }
            }
            echo "<a href='./?/projCtrl' class='btn btn-link btn-lg'>返回</a>";
        } elseif ($c == "pack") {
            echo "<head><title>打包项目</title><link rel='stylesheet' href='system/static/css/zui.min.css'></head>";
            echo "<h2>正在打包项目...</h2>";
            zipTool::pack(["system", "config", "index.php"], "pack.zip");
            echo "<h2>打包完成，压缩包：pack.zip</h2>";
            echo "<a href='./?/projCtrl' class='btn btn-link btn-lg'>返回</a>";
        } elseif ($c == "exit") {
            cookie::delete("allow_visit_proj_ctrl");
            header("Location:./?/");
        } else {
            header("Location:./?/projCtrl");
        }
    }

    public static function urls()
    {
        echo "<head><title>链接导航</title><link rel='icon' href='system/static/img/j1logo.jpg'><link rel='stylesheet' href='system/static/css/zui.min.css'><link rel='stylesheet' href='system/static/css/zui.datatable.min.css'><script src='system/static/js/jquery.min.js'></script><script src='system/static/js/zui.min.js'></script><script src='system/static/js/zui.datatable.min.js'></script></head>";
        echo "<body><h1>当前站点的链接</h1><table class='table datatable'><thead><tr><th>名称</th><th>页面类型</th><th>页面状态</th><th>页面标题</th><th>操作</th></tr></thead><tbody>";
        $list = scandir(HTML_PATH);
        foreach ($list as $item) {
            if ($item != "." and $item != "..") {
                $data = file_get_contents(HTML_PATH . $item);
                $firstLine = explode("\n", $data)[0];
                $firstLine = str_replace("<!--", "", str_replace("-->", "", $firstLine));
                $link_name = str_replace(".html", "", $item);
                $data_array = explode(";", $firstLine);
                $link_type = "";
                if (explode("=", $data_array[0])[0] == "title") {
                    $link_type = "基本页面";
                } else if (explode("=", $data_array[0])[0] == "function") {
                    $link_type = "映射页面";
                }else{
                    $link_type = "引用页面";
                }
                $link_status = "";
                if ($item == "head.html") {
                    $link_status = "无法访问头部页面";
                }else if ($item=="urls.html") {
                    $link_status = "当前页面";
                }else if ($link_type=="引用页面"){
                    $link_status  ="无法访问引用页面";
                }else{
                    $link_status = "正常";
                }
                $link_title = "";
                if ($link_type=="基本页面"){
                    $link_title=explode("=", $data_array[0])[1];
                    if(substr($link_title,0,1)=="{"and substr($link_title,strlen($link_title)-1)=="}"){
                        $x = substr($link_title,1,strlen($link_title)-2);
                        $link_title = L[$x];
                    }
                }else if ($link_type=="映射页面"){
                    $link_title=explode("=", $data_array[1])[1];
                }else{
                    $link_title="引用页面：$link_name";
                }
                $link_data = "";
                if ($link_status=="正常"){
                    $link_data = "<a href='./?/".$link_name."' class='btn btn-link btn-sm'>访问</a>";
                }else{
                    $link_data = "<a class='btn btn-link btn-sm' disabled>访问</a>";
                }
                echo "<tr><td>$link_name</td><td>$link_type</td><td>$link_status</td><td>$link_title</td><td>$link_data</td></tr>";
            }
        }
        echo "</tbody></table><script>$('table.datatable').datatable();</script></body>";
    }
}