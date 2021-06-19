<?php
/**
 * Class argsTool
 * 参数处理模块
 * 根据不同请求方法获取参数
 */

class argsTool
{
    /**
     * @param string $name "参数名"
     * @static
     * @return string
     * 在GET请求方式获取参数
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
     * @return string
     * 在POST请求方式获取参数
     */
    public static function post(string $name):string{
        if (isset($_POST[$name])){
            return $_POST[$name];
        }else{
            return "null";
        }
    }
}