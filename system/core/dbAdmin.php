<?php
include "redirect.php";
const A = "./?/dbAdmin";
/**
 * 数据库管理页面
 */
class dbAdmin
{
    public static function run()
    {
        $p = argsTool::get("p");
        if (!$p) $p = "index";
        if ($p=="null") $p = "index";
//        echo $p;
        call_user_func("self::$p");
    }

    public static function index()
    {
        if (cookie::get("dbAdminLogin")=="yes"){
            header("Location: ".A."&p=main");
        }
        self::loadHead("数据库管理——首页");
        echo "<body style='background:#cccccccc;'><h1 style='margin: 30px 10%'>dbAdmin 数据库管理器</h1><div style='margin: 30px 10%'><label for='password'>输入管理员密码：</label><input id='pwd' style='width: 65%' type='password' class='form-control'></div><button id='btn' style='margin: 10px 30%' class='btn btn-primary btn-lg'>登录</button><script>
    $(function (){
       $('#btn').click(function (){
let pwd = $('#pwd').val();location.href = './?/dbAdmin&p=login&pwd='+pwd;});});</script></body>";
    }

    public static function login()
    {
        self::loadHead("登录");
        $pwd = argsTool::get("pwd");
        if (encryptTool::decode(json_decode(file_get_contents(CONFIG_PATH."dbAdmin_pwd.json"))[0],$pwd,true)==$pwd){
            echo "<h1>登录成功</h1>";
            cookie::set("dbAdminLogin","yes");
            header("Location: ".A."&p=main");
        }else{
            echo "<h1>密码错误</h1>";
        }
    }

    public static function main()
    {
        if (cookie::get("dbAdminLogin")!="yes")header("Location:".A."&p=index");
        $result = dbTool::getTables();
        if ($result["status"]=="success") {
            self::loadHead("首页");
            $table_count = count($result["data"]);
            echo "<body style='background:#cccccccc;'><h1>当前数据库中有".$table_count."个数据表：</h1><h2>数据库类型：".dbTool::DBType()."</h2><table class='table datatable'><thead><th>数据表名</th><th>管理</th><th>删除</th></thead><tbody>";
            foreach ($result["data"] as $datum) {
                echo "<td class='text-important'>$datum</td><td><a href='".A."&p=manageTable&table=".$datum."'>进入管理</a></td><td><a class='text-red' href='javascript:delete'>删除数据表</a></td>";
            }
            echo "</tbody></table><button id='create'>创建数据表</button><script>$(function() {
              $('table.datatable').datatable();
            });</script><script>function deleteTable(tableName){
                location.href = '".A."' + '&p=delete&table=' + tableName;
            }function create(){
                
            }</script>";
        }else{
            echo "<h1>数据库异常</h1>";
        }
    }

    public static function loadHead($title)
    {
        echo "<head><title>$title</title><!--script src='".JS_PATH."vue.global.js'></script--><script src='".JS_PATH."jquery.min.js'></script><script src='".JS_PATH."zui.datatable.min.js'></script><script src='".JS_PATH."zui.min.js'></script><link rel='stylesheet' href='".CSS_PATH."zui.min.css'><link rel='stylesheet' href='".CSS_PATH."zui.datatable.min.css'></head>";
    }
}