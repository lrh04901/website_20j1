<?php
/**
 * PHP配置处理工具
 */
class phpIniTool{
    public static function get(string $name):string
    {
        $a =  explode("\n",file_get_contents(php_ini_loaded_file()));
        $b = [];
        foreach ($a as $item) {
            $item=str_replace("\r","",$item);
            $item=str_replace(" ","",$item);
            if ($item&&substr($item,0,1)!=";"&&substr($item,0,1)!="[") {
                $x=explode("=",$item);
                $b[$x[0]]=$x[1];
            }
        }
        if (isset($b[$name])){
            return $b[$name];
        }else{
            return "property undefined";
        }
    }

    public static function path():string
    {
        return php_ini_loaded_file();
    }

    public static function list():void
    {
        $a =  explode("\n",file_get_contents(php_ini_loaded_file()));
        $b = [];
        foreach ($a as $item) {
            $item=str_replace("\r","",$item);
            $item=str_replace(" ","",$item);
            if ($item&&substr($item,0,1)!=";"&&substr($item,0,1)!="[") {
                $x=explode("=",$item);
                $b[$x[0]]=$x[1];
            }
        }
        for ($i = 0;$i<count($b);$i++){
            $key = array_keys($b)[$i];
            $value = $b[$key];
            echo "$key=>$value<br>";
        }
    }
}