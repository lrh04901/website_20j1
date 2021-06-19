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
     * @param $name "数据名"
     * @param $value "数据内容"
     * @return cookie 返回cookie对象，可以用于连续操作
     */
    static function set($name, $value):cookie
    {
        setcookie($name, $value);
        return new cookie();
    }

    /**
     * @param $name
     * @return mixed|null
     */
    static function get($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        } else {
            return null;
        }
    }

    static function delete($name)
    {
        setcookie($name, "", 0);
    }
}