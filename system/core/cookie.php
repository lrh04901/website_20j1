<?php
/**
 * Class cookie
 * Cookie处理模块
 * 将一些数据存储至浏览器中
 */

class cookie
{
    /**
     * @param $name "数据名"
     * @param $value
     * @return cookie
     */
    static function set($name, $value):cookie
    {
        setcookie($name, $value);
        return new cookie();
    }

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