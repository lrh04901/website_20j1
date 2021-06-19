<?php
include "redirect.php";
/**
 * Class argsTool
 * 参数处理模块
 * 根据不同请求方法获取参数
 */

class argsTool
{
    /**
     * @param string $name 参数名
     * @return string 返回指定参数的内容，参数未提供时返回null
     * <p>返回GET请求url中的参数</p>
     */
    public static function get(string $name):string{
        if (isset($_GET[$name])){
            return $_GET[$name];
        }else{
            return "null";
        }
    }

    /**
     * @param string $name 参数名
     * @return string 返回指定参数的内容，参数未提供时返回null
     * <p>返回POST请求头中的参数</p>
     */
    public static function post(string $name):string{
        if (isset($_POST[$name])){
            return $_POST[$name];
        }else{
            return "null";
        }
    }
}