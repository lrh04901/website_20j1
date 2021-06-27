<?php
const A = "./?/dbAdmin";
/**
 * 数据库管理页面
 */
class dbAdmin
{
    /**
     * 整个模块启动入口
     * @return void
     */
    public static function run():void
    {
        $p = argsTool::get("p");
        if (!$p) $p = "index";
        if ($p=="null") $p = "index";
//        echo $p;
        call_user_func("self::$p");
    }

    /**
     * 数据库管理首页
     * @return void
     */
    public static function index():void
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

    /**
     * 数据库管理页面登录
     * @return void
     */
    public static function login():void
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

    /**
     * 数据库管理首页（查看数据表列表）
     * @return void
     */
    public static function main():void
    {
        if (cookie::get("dbAdminLogin")!="yes")header("Location:".A."&p=index");
        $result = dbTool::getTables();
        if ($result["status"]=="success") {
            self::loadHead("首页");
            $table_count = count($result["data"]);
            echo "<body style='background:#cccccccc;'><h1>当前数据库中有".$table_count."个数据表：</h1><h2>数据库类型：".dbTool::DBType()."</h2><table class='table datatable'><thead><th>数据表名</th><th>管理</th><th>删除</th></thead><tbody>";
            foreach ($result["data"] as $datum) {
                echo "<td class='text-important'>$datum</td><td><a href='".A."&p=manageTable&table=".$datum."'>进入管理</a></td><td><a class='text-red' href='javascript:deleteTable(\"".$datum."\");'>删除数据表</a></td></tr>";
            }
            echo "</tbody></table><button id='create' class='btn btn-lg btn-primary'>创建数据表</button>
            <div class='modal fade' id='deleteTableModal'><div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>关闭</span></button><h4>删除数据表</h4></div><div class='modal-body'><p>是否删除数据表：<b id='deleteTableName'></b></p></div><div class='modal-footer'><button type='button' class='btn btn-light' data-dismiss='modal'>取消</button><button type='button' class='btn btn-danger' id='deleteTableConfirm'>删除</button></div></div></div></div>
            <div class='modal fade' id='createTableModal'><div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>关闭</span></button><h4>创建数据表</h4></div><div class='modal-body'><label for='createTableName'>数据表名：</label><input type='text' id='createTableName' class='form-control' name='createTableName' placeholder='数据表名'><label for='createTableCols'>数据表列(每一项用英文逗号分隔)：</label><input type='text' id='createTableCols' class='form-control' name='creareTableCols' placeholder='数据表列(每一项用英文逗号分隔)'></div><div class='modal-footer'><button type='button' class='btn btn-light' data-dismiss='modal'>取消</button><button type='button' class='btn btn-primary' id='createTableConfirm'>创建</button></div></div></div></div>
            <script>$(function() { $('#deleteTableConfirm').click(function (){
            location.href = '".A."' + '&p=deleteTable&table=' + $('#deleteTableName').text();});$('#createTableConfirm').click(function() { 
            location.href = '".A."' + '&p=createTable&table=' + $('#createTableName').val() + '&cols=' + $('#createTableCols').val();});$('#create').click(function (){
            $('#createTableModal').modal();});});</script><script>function deleteTable(tableName){ $('#deleteTableName').text(tableName);$('#deleteTableModal').modal();}</script>";
        }else{
            echo "<h1>数据库异常</h1>";
        }
    }

    /**
     * 删除数据表
     * @return void
     */
    public static function deleteTable():void
    {
        self::loadHead("删除数据表");
        $table = argsTool::get("table");
        $result = dbTool::deleteTable($table);
        if ($result["status"]=="success"){
            echo "<h1>成功</h1>";
        }else{
            echo "<h1>失败</h1>";
        }
        self::gotoWithTime(A."&p=main",1);
    }

    /**
     * 创建数据表
     * @return void
     */
    public static function createTable():void
    {
        self::loadHead("创建数据表");
        $name = argsTool::get("table");
        $cols = argsTool::get("cols");
        $cols_array = explode(",",$cols);
        $result = dbTool::createTable($name,$cols_array);
        if ($result["status"]=="success"){
            echo "<h1>成功</h1>";
        }else{
            echo "<h1>失败</h1>";
        }
        self::gotoWithTime(A."&p=main",1);
    }

    public static function manageTable():void
    {
        self::loadHead("管理数据表");
        $tableName = argsTool::get("table");
        $result_a = dbTool::select($tableName,["*"],["*"]);
        if ($result_a["status"]=="success") {
            $cols = array_keys($result_a["data"][0]);
//            print_r($cols);
//            echo count($result_a["data"]);
            echo "<body style='background:#cccccccc;'><h1>管理数据表：<i>" . $tableName . "</i></h1><h2><a href='".A."&p=main'>返回</a></h2></body><table class='table datatable'><thead><tr>";
            foreach ($cols as $col) {
                echo "<th>$col</th>";
            }
            echo "<th>编辑</th><th>删除</th></tr></thead><tbody>";
            foreach ($result_a["data"] as $datum) {
                if ($datum!=$result_a["data"][0]) {
                    $xid = dechex(rand(0,1000000));
                    echo "<tr>";
                    foreach ($cols as $col) {
                        echo "<td id='$xid-$col'>" . $datum[$col] . "</td>";
                    }
                    echo "<td><a href='javascript:editData(\"$xid\");'>编辑</a></td><td><a href='javascript:deleteData(\"$xid\")' class='text-red'>删除</a></td></tr>";
                }
            }
            echo "</tbody></table><button class='btn btn-primary btn-lg' id='insertData'>插入数据</button>
<div class='modal fade' id='myModal'>
<div class='modal-dialog'>
<div class='modal-content'>
<div class='modal-header'>
<button class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>关闭</span></button>
<h4 id='modal-title' class='modal-title'>标题</h4>
</div>
<div class='modal-body'>";
            foreach ($cols as $col) {
                echo "<label for='input_$col'>$col:</label><input type='text' name='modalInput' id='input_$col' placeholder='$col' class='form-control'>";
            }
            echo "
</div>
<div class='modal-footer'>
<button type='button' class='btn btn-default' data-dismiss='modal'>取消</button>
<button type='button' class='btn btn-primary' id='saveData'>完成</button>
</div>
</div>
</div>
</div>
<script>
$('#insertData').click(function (){
    $('#modal-title').text('插入数据');
    $('[name=\"modalInput\"]').val('');
    $('#myModal').modal();
});
function editData(id){
    console.log('edit:'+id);
}
function deleteData(id){
    console.log('delete:'+id)
}
</script>
</body>";
        }else{
            echo "<h1>数据库异常</h1>";
        }
//        print_r($result_a);
    }

    /**
     * 加载头部代码
     * @param string $title 页面标题
     * @return void
     */
    public static function loadHead(string $title):void
    {
        echo "<head><title>$title</title><link rel='icon' href='./system/static/img/j1logo.jpg'><!--script src='".JS_PATH."vue.global.js'></script--><script src='".JS_PATH."jquery.min.js'></script><script src='".JS_PATH."zui.datatable.min.js'></script><script src='".JS_PATH."zui.min.js'></script><link rel='stylesheet' href='".CSS_PATH."zui.min.css'><link rel='stylesheet' href='".CSS_PATH."zui.datatable.min.css'></head>";
    }

    /**
     * 延迟跳转
     * @param string $path 跳转的地址
     * @param int $time 等待时间（s）
     * @return void
     */
    private static function gotoWithTime(string $path, int $time=0):void
    {
        header("Refresh:$time;url=$path");
    }
}