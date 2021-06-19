<?php
include "redirect.php";
/**
 * Class cookie
 * Cookie处理模块
 * 将一些数据存储至浏览器中
 */

class cookie
{
    /**
     * @param string $name 数据名
     * @param string $value 数据内容
     * @return cookie 返回cookie对象，用于连续操作
     * 写入cookie数据到浏览器
     */
    static function set(string $name, string $value):cookie
    {
        setcookie($name, $value);
        return new cookie();
    }

    /**
     * @param string $name 数据名
     * @return mixed|null 返回cookie中的数据，若该数据不存在则返回null
     * 从浏览器中读取cookie数据
     */
    static function get(string $name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        } else {
            return null;
        }
    }

    /**
     * @param string $name 数据名
     * @return cookie 返回cookie对象，用于连续操作
     * 从浏览器中删除某个cookie数据
     */
    static function delete(string $name):cookie
    {
        setcookie($name, "", 0);
        return new cookie();
    }
}