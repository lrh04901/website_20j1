<?php
include "redirect.php";

class fileTool
{
    public static function createDir(string $path): bool
    {
        $aimUrl = str_replace('', '/', $path);
        $aimDir = '';
        $arr = explode('/', $aimUrl);
        $result = true;
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir)) {
                $result = mkdir($aimDir);
            }
        }
        return $result;
    }
}