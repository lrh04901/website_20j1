<?php


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
                    foreach ($data_array as $item) {
                        $file_path = self::getDirFromPath($GLOBALS["path"]) . "out" . str_replace("/", "\\", $item[0]);
//                    echo $file_path."\n";
//                    rmdir($file_path);
//                    touch($file_path);
                    }
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
            $d .= chr($i_b);
        }
        $d .= chr($c);
        return $d;
    }

    private static function string_decode($string, $secret): string
    {

        $a = substr($string, 0, strlen($string) - 1);
        $b = $string[strlen($string) - 1];
        $c = substr(hash("sha256", $secret), 0, strlen($secret));
        $d = "";
        for ($i = 0; $i < strlen($a); $i++) {
            $i_a = self::xor_num(ord($a[$i]), ord($c[$i % strlen($c)]));
            $i_b = self::xor_num($i_a, ord($b));
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
        echo "准备应用文件(夹)\n";
        $dir = self::getDirFromPath($GLOBALS["path"]);
//    echo $dir."\n";
        if (is_dir($dir . "out")) {
            echo "yes";
        } else {
            mkdir($dir . "out");
        }
        echo "\n";
        $out_dir = $out_path === $GLOBALS["path"] ?: $out_path;
        foreach ($data_array as $item) {
//        create_file($dir.$out_dir."out",$item[0]);
            $a = $dir . $out_dir . "out" . str_replace("/", "\\", $item[0]);
            echo $a . "\n";
            mkdir($a, 0777, true);
            rmdir($a);
            touch($a);
            file_put_contents($a, $item[2]);
        }
    }

    private static function getDirFromPath($path)
    {
        $a = explode("\\", $path);
        $b = $a[count($a) - 1];
        return str_replace($b, "", $path);
    }

    private static function create_file($base_path, $path)
    {
        $path_ = str_replace("/", "\\", $path);
//    echo $path_."\t";
        /**创建文件(自动补全缺少文件夹)
         * $1为基本目录，表示文件创建的位置
         * $2为相对路径地址，相对于基本目录的地址
         *
         * $1=C:\Users\SCH\PhpstormProjects\xcpak\test\out\
         * $2=d\d\f
         */
        if (count(explode("\\", $path_)) === 1) {//判断是否包含目录分隔符
            touch($base_path . $path_);
//        echo "yes";
        } else {
            $a = explode("\\", $path_);//用分隔符分开
//        print_r($a);
            $b = $a[0];//第一个目录
            if (!is_dir($base_path . $b)) {//不是目录
                if (is_file($base_path . $b)) {//为文件的情况
                    unlink($base_path . $b);
                }
            }
//        echo $base_path.$path_."\n";
            if (!is_dir($base_path . $path_)) {
                mkdir($base_path . $path_);
            }
            $base_path_new = $base_path . "\\" . $b;
            $path_new = "";
            for ($i = 1; $i < count($a) - 1; $i++) {
                $path_new .= "\\" . $a[$i];
            }
            self::create_file($base_path_new, $path_new);
        }
    }
}