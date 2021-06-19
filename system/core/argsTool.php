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
     * @static
     * @return string 返回GET请求url中的参数，不存在时返回null
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
     * @static
     * @return string 返回POST请求头中的参数，不存在时返回null
     */
    public static function post(string $name):string{
        if (isset($_POST[$name])){
            return $_POST[$name];
        }else{
            return "null";
        }
    }
}