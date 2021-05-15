<?php


class cookie
{
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