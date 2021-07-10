<?php
class zipTool
{
    public static function pack($path,string $out="out.zip"):void
    {
        $zip = new ZipArchive();
        if (file_exists($out)){
            unlink($out);
        }
        if ($zip->open("./$out",ZipArchive::CREATE)!==true){
            die("error");
        }
        if (is_string($path)){
            if (is_file($path)){
                $zip->addFile($path);
            }elseif (is_dir($path)) {
                self::list_dir($path);
                foreach ($GLOBALS["dir_array"] as $file){
                    $zip->addFile($file);
                }
            }
        }elseif (is_array($path)){
            foreach ($path as $item) {
                if (is_file($item)){
                    $zip->addFile($item);
                }elseif (is_dir($item)){
                    $GLOBALS["dir_array"]=[];
                    self::list_dir($item);
                    foreach ($GLOBALS["dir_array"] as $file) {
                        $zip->addFile($file);
                    }
                }
            }
        }
        $zip->close();
    }

    private static function list_dir(string $path_dir): void
    {
//        if (!isset($GLOBALS["dir_array"]))$GLOBALS["dir_array"]=[];
        $a = scandir($path_dir);
        foreach ($a as $value) {
            if ($value !== "." && $value !== "..") {
                $now_file = $path_dir . "/" . $value;
                if (is_file($now_file)) {
                    array_push($GLOBALS["dir_array"],$now_file);

                } else {
                    self::list_dir($now_file);
                }
            }
        }
    }
}