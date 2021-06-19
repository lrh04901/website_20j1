<?php
include "redirect.php";
class xcpak
{
    public static function encode($dir_path): string
    {
        if (is_dir($dir_path)) {
            $outPak = $GLOBALS["path"] . "/out.xcpak";
            if (file_exists($outPak)) {
                unlink($outPak);
            }
            self::list_dir($dir_path);
            touch($outPak);
            $outData = "XCPAK";
            $outData .= str_repeat(chr(1), 10);
            $outData .= chr(12);
            for ($i = 0; $i < count($GLOBALS["dir_array"]); $i++) {
                $dir = $GLOBALS["dir_array"];
                $key = array_keys($dir)[$i];//文件路径
                $value = $dir[$key];//文件校验码
                $content = file_get_contents($GLOBALS["path"] . "/" . $key);//文件数据
                $key = self::string_encode($key, $value);//加密文件名
                $content = self::string_encode($content, $value);//加密文件数据
                $value = self::hash_encode($value);
                $outData .= chr(28) . chr(2) . $key . chr(3) . chr(30) . chr(2) . $value . chr(3) . chr(30) . chr(2) . $content . chr(3);
            }
            file_put_contents($outPak, $outData);
        } else {
            return "不是一个目录";
        }
        return "";
    }

    public static function decode(string $file_path, $out_path = ""): string
    {
        if (is_file($file_path)) {
            if (substr($file_path, strlen($file_path) - 5) === "xcpak") {
                $data = file_get_contents($file_path);
                file_put_contents(DATA_PATH."out.temp",$data);
//                echo $data[0];
//                echo substr($data,0,5);
//                echo $data;
                if (substr($data, 0, 5) === "XCPAK") {
                    $data = substr($data, 5, strlen($data) - 5);
                    for ($i = 0; $i < 10; $i++) {
                        if ($data[0] === chr(1)) {
                            $data = substr($data, 1, strlen($data) - 1);
                        } else {
                            echo "XC包数据错误";
                            return "";
                        }
                    }
                    if ($data[0] === chr(12)) {
                        $data = substr($data, 1, strlen($data) - 1);
                    } else {
                        echo "XC包数据错误";
                        return "";
                    }
                    $i_a = -1;//总索引
                    $i_b = 0;//内部索引
                    $l_a = false;//是否在存储
                    $data_array = array();
                    for ($i = 0; $i < strlen($data); $i++) {
//                    echo ord($data[$i]) . "\t";
                        if ($data[$i] === chr(12)) {
                            continue;
                        } elseif ($data[$i] === chr(28)) {
                            $i_a++;
                            $i_b = 0;
                            continue;
                        } elseif ($data[$i] === chr(2)) {
                            $l_a = true;
                            $data_array[$i_a][$i_b] = "";
                            continue;
                        } elseif ($data[$i] === chr(3)) {
                            $l_a = false;
                            continue;
                        } elseif ($data[$i] === chr(30)) {
                            $i_b++;
                            continue;
                        } elseif ($l_a) {
                            $data_array[$i_a][$i_b] .= $data[$i];
                        }
                    }
                    for ($i = 0; $i < count($data_array); $i++) {
                        $data_array[$i][1] = self::hash_decode($data_array[$i][1]);
                        $data_array[$i][0] = self::string_decode($data_array[$i][0], $data_array[$i][1]);
                        $data_array[$i][2] = self::string_decode($data_array[$i][2], $data_array[$i][1]);
                    }
                    self::applyData($data_array, $out_path);
//                $out_dir = $out_path===$GLOBALS["path"]?:$out_path;
//                    foreach ($data_array as $item) {
//                        $file_path = self::getDirFromPath($GLOBALS["path"]) . "out" . str_replace("/", "\\", $item[0]);
////                    echo $file_path."\n";
////                    rmdir($file_path);
////                    touch($file_path);
//                    }
                } else {
                    echo "不是标准的XC包文件";
                }
            } else {
                echo "不是XC包文件";
            }
        } else {
            return "不是一个文件";
        }
        return "";
    }

    private static function string_encode($string, $secret): string
    {
        $a = base64_encode($string);
//    echo $a;
        $b = substr(hash("sha256", $secret), 0, strlen($secret));
        $c = rand(0, 128);
        $d = "";
        for ($i = 0; $i < strlen($a); $i++) {
            $i_a = self::xor_num(255 - ord($a[$i]), ord($b[$i % strlen($b)]));
            $i_b = self::xor_num($i_a, $c);
            $i_c = dechex($i_b);
            if (strlen($i_c) === 1) {
                $i_c = "0" . $i_c;
            }
            $d .= $i_c;
        }
        $e = dechex($c);
        if (strlen($e) === 1) {
            $e = "0" . $e;
        }
        $d .= $e;
//    echo $d . "\n";
        return $d;
    }

    private static function string_decode($string, $secret): string
    {
        //假设字符串数据为d8ffe5cadaf481857f
        $z = strlen($string);
        $a = substr($string, 0, $z - 2);//除去后两位
        $b = $string[$z - 2] . $string[$z - 1];//读取后两位
        unset($z);
        $c = substr(hash("sha256", $secret), 0, strlen($secret));
        $d = "";
        for ($i = 0; $i < strlen($a); $i += 2) {
            $i_a = self::xor_num(hexdec($a[$i].$a[$i+1]), ord($c[($i / 2) % strlen($c)]));
            $i_b = self::xor_num($i_a, hexdec($b));
            $i_c = 255 - $i_b;
            $d .= chr($i_c);
        }
        return base64_decode($d);
    }

    private static function hash_encode($string): string
    {
        return self::string_encode($string, "XCPAK");
    }

    private static function hash_decode($string): string
    {
        return self::string_decode($string, "XCPAK");
    }

    private static function list_dir($path_dir)
    {
        $a = scandir($path_dir);
        foreach ($a as $value) {
            if ($value !== "." && $value !== "..") {
                $now_file = $path_dir . "/" . $value;
                if (is_file($now_file)) {
                    $c_file_path = str_replace($GLOBALS["path"], "", $now_file);
                    $GLOBALS["dir_array"][$c_file_path] = hash_file("md5", $GLOBALS["path"] . "/" . $c_file_path);
                } else {
                    self::list_dir($now_file);
                }
            }
        }
    }

    private static function xor_num(int $num1, int $num2): int
    {
        $bin1 = decbin($num1);
        $bin2 = decbin($num2);
        for ($i = strlen($bin1); $i < 8; $i++) {
            $bin1 = "0" . $bin1;
        }
        for ($i = strlen($bin2); $i < 8; $i++) {
            $bin2 = "0" . $bin2;
        }
        $arr1 = str_split($bin1);
        $arr2 = str_split($bin2);
        $str = "";
        for ($i = 0; $i < 8; $i++) {
            $str .= $arr1[$i] == $arr2[$i] ? "0" : "1";
        }
        return bindec($str);
    }

    private static function applyData($data_array, $out_path)
    {
//        echo "准备应用文件(夹)\n";
        $dir = str_replace("\\","/",self::getDirFromPath($GLOBALS["path"]));
//    echo $dir."\n";
//        if (is_dir($dir)) {
//            echo "yes";
//        } else {
//            mkdir($dir);
//        }
        echo "\n";
        $out_dir = $out_path === $GLOBALS["path"] ?: $out_path;
        foreach ($data_array as $item) {
//        create_file($dir.$out_dir."out",$item[0]);
            $a = $dir . $out_dir  . str_replace("/", "\\", $item[0]);
            $a = str_replace("\\","/",$a);
            self::setFileData($a,$item[2]);
//            echo $a . "\n";
//            mkdir($a, 0777, true);
//            rmdir($a);
//            touch($a);
//            file_put_contents($a, $item[2]);
        }
        echo "文件释放完成";
    }

    private static function getDirFromPath($path)
    {
        $a = explode("\\", $path);
        $b = $a[count($a) - 1];
        return str_replace($b, "", $path);
    }

    private static function setFileData($path,$data){
        $path_new=str_replace("\\","/",$path);
        if (file_exists($path_new)){
            if (is_dir($path_new)){
                rmdir($path_new);
            }
            if (is_file($path_new)){
                unlink($path_new);
            }
        }
        mkdir($path_new);//创建目录用来代替文件
        rmdir($path_new);//删除文件夹，此时保留中间的目录
        touch($path_new);//创建文件
        file_put_contents($path_new,$data);//写入数据
    }
}