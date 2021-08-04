<?php

/**
 * 加密模块
 */
class encryptTool
{
    /**
     * 加密字符串
     * @param string $string 需要加密的数据
     * @param string $secret 加密用的密钥
     * @param bool $return 是否返回，默认不返回直接输出
     * @return string 返回加密后的数据
     */
    public static function encode(string $string, string $secret, bool $return = false): string
    {
        $a = base64_encode($string);//转换为base64文本
        $b = str_split($a);//将base64文本拆开
        $c = "";//空变量用于存储第一阶段的数据
        foreach ($b as $item) {//对每一个字符进行操作
            $x_a = ord($item);//获取每一项的ASCII编码
            $x_b = 127 - $x_a;//将ASCII编码值反转
            $x_c = dechex($x_b);//将ASCII转换为16进制
            if (strlen($x_c) == 1) {
                $x_c = "0" . $x_c;//16进制为一位的时候进行补0，防止数据错位
            }
            $c .= $x_c;//将这一部分的数据追加到上面的空变量中
        }
        $d = substr(hash("sha256", $secret), 0, strlen($secret));
        //针对秘钥进行处理
        $c_a = str_split($c);//将处理后的文本拆开
        $d_a = str_split($d);//将处理后的秘钥拆开
        $e = "";//再次创建一个空变量用于存储第二阶段的数据
        $index = 0;//索引变量，用于计数
        foreach ($c_a as $item) {//对每一项进行处理
            $v_a = $item;//接收每一个字符
            $v_b = $d_a[$index++ % count($d_a)];//读取对应位置的混淆码
            $v_c = decbin(ord($v_a) + ord($v_b));//创建混淆后的文本
            if (strlen($v_c) == 7) {
                $v_c = "0" . $v_c;//每一组长度不足时用0补位
            }
            $e .= $v_c;//追加数据
        }
        $f = decbin(rand(0, 127));//生成掩码
        switch (strlen($f)) {//对掩码进行补位
            case 0:
                $f = "00000000";
                break;
            case 1:
                $f = "0000000" . $f;
                break;
            case 2:
                $f = "000000" . $f;
                break;
            case 3:
                $f = "00000" . $f;
                break;
            case 4:
                $f = "0000" . $f;
                break;
            case 5:
                $f = "000" . $f;
                break;
            case 6:
                $f = "00" . $f;
                break;
            case 7:
                $f = "0" . $f;
                break;
        }
        $g = "";//用于存储第三阶段数据的变量
        $h = str_split($e);//将第二部分的数据拆开
        $x = str_split($f);//将掩码拆开
        for ($i = 0; $i < count($h); $i++) {//对每一项进行处理
            $i_a = $h[$i];//每一项的数据
            $i_b = $x[$i % 8];//对应位置的掩码
            $i_c = $i_a === $i_b ? "0" : "1";//处理后文本
            $h[$i] = $i_c;//替换数组内的数据
        }
        $t = "";
        for ($i = 0; $i < count($h); $i++) {
            $t .= $h[$i];
        }
        $t .= $f;
        $h = str_split($t);
        for ($i = 0; $i < count($h); $i++) {//对每一项进行混淆
            $a = chr(rand(ord("a"), ord("z")));//随机生成一个小写字母
            $A = chr(rand(ord("A"), ord("Z")));//随机生成一个大写字母
            if ($h[$i] == 0) {
                $g .= $a;
            } else if ($h[$i] == 1) {
                $g .= $A;
            }
        }
        if ($return) {
            return $g;
        } else {
            echo $g;
        }
        return "";
    }

    /**
     * 解密字符串
     * @param string $string 需要解密的数据
     * @param string $secret 解密用的密钥
     * @param bool $return 是否返回，默认不返回直接输出
     * @return string 返回解密后的数据
     */
    public static function decode(string $string, string $secret, bool $return = false): string
    {
        $a = str_split($string);//拆开加密的数据
        $b = "";//创建一个空变量用于存储第一阶段的数据
        for ($i = 0; $i < count($a); $i++) {//对每一项进行处理
            $i_a = ord($a[$i]);//获取这一项的ASCII码
            $_a = ord("a");//a的ASCII码
            $_z = ord("z");//z的ASCII码
            $_A = ord("A");//A的ASCII码
            $_Z = ord("Z");//Z的ASCII码
            if ($i_a <= $_z && $i_a >= $_a) {
                $b .= "0";//将小写字母转换为0
            } else if ($i_a <= $_Z && $i_a >= $_A) {
                $b .= "1";//将大写字母转换为1
            }
        }
        $c = substr($b, 0, strlen($b) - 8);//读取主数据
        $d = substr($b, strlen($b) - 8, 8);//读取掩码数据
        $c_a = str_split($c);//将主数据拆开
        $c_b = str_split($d);//将掩码拆开
        $e = "";//空变量用于第二阶段
        for ($i = 0; $i < count($c_a); $i++) {//对每一项进行处理
            $i_a = $c_a[$i];//每一项的数据
            $i_b = $c_b[$i % 8];//对应位置的掩码
            $i_c = $i_a === $i_b ? "0" : "1";//反掩码计算
            $e .= $i_c;//追加数据
        }
        $f = str_split($e, 8);//将数据拆开
        $g = str_split(substr(hash("sha256", $secret), 0, strlen($secret)));
        $h = "";//空变量用于第三阶段
        for ($i = 0; $i < count($f); $i++) {
            $x_a = bindec($f[$i]);
            $x_b = ord($g[$i % count($g)]);
            $x_c = chr($x_a - $x_b);
            $h .= $x_c;
        }
        $m = str_split($h, 2);
        $n = "";
        for ($i = 0; $i < count($m); $i++) {
            $i_a = chr(127 - hexdec($m[$i]));
            $n .= $i_a;
        }
        $o = base64_decode($n);
        if ($return) {
            return $o;
        } else {
            echo $o;
        }
        return "";
    }
}
