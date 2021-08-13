<?php

/**
 * Cookie处理模块，将一些数据存储至浏览器中
 */
class cookie
{
    /**
     * 写入cookie数据到浏览器
     * @param string $name 数据名
     * @param string $value 数据内容
     * @return cookie 返回cookie对象，用于连续操作
     */
    static function set(string $name, string $value): cookie
    {
        if (defined("PROXY_CODE")){
            $fileName = COOKIE_PATH.PROXY_CODE.".cookie";
            $content = @file_get_contents($fileName);
            if (!$content){
                touch($fileName);
                file_put_contents($fileName,encryptTool::encode(json_encode([]),SECRET,true));
            }
            $data = json_decode(encryptTool::decode($content,SECRET,true),true);
            $data[$name] = $value;
            file_put_contents($fileName,encryptTool::encode(json_encode($data),SECRET,true));
        }else {
            setcookie($name, $value);
        }
        return new cookie();
    }

    /**
     * 从浏览器中读取cookie数据
     * @param string $name 数据名
     * @return mixed|null 返回cookie中的数据，若该数据不存在则返回null
     */
    static function get(string $name)
    {
        if (defined("PROXY_CODE")){
            $fileName = COOKIE_PATH.PROXY_CODE.".cookie";
            $content = @file_get_contents($fileName);
            if ($content){
                $data = json_decode(encryptTool::decode($content,SECRET,true),true);
                if (isset($data[$name])){
                    return $data[$name];
                }else{
//                    echo "在线cookie中找不到$name";
                    return null;
                }
            }else{
//                echo "在线cookie文件不存在";
                return null;
            }
        }else {
            if (isset($_COOKIE[$name])) {
                return $_COOKIE[$name];
            } else {
                return null;
            }
        }
    }

    /**
     * 从浏览器中删除某个cookie数据
     * @param string $name 数据名
     * @return cookie 返回cookie对象，用于连续操作
     */
    static function delete(string $name): cookie
    {
        if (defined("PROXY_CODE")){
            $fileName = COOKIE_PATH.PROXY_CODE.".cookie";
            if (file_exists($fileName)){
                $data = json_decode(encryptTool::decode(file_get_contents($fileName),SECRET,true),true);
                if (isset($data[$name])){
                    unset($data[$name]);
                    file_put_contents($fileName,encryptTool::encode(json_encode($data),SECRET,true));
                }
            }
        }else {
            setcookie($name, "", 0);
        }
        return new cookie();
    }
}
