<?php


class argsTool
{
    public static function get(string $name):string{
        if (isset($_GET[$name])){
            return $_GET[$name];
        }else{
            return "null";
        }
    }
    public static function post(string $name):string{
        if (isset($_POST[$name])){
            return $_POST[$name];
        }else{
            return "null";
        }
    }
}