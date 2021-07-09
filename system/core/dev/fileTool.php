<?php

/**
 * 文件处理模块
 */
class fileTool
{
    /**
     * 创建一个目录
     * @param string $path 目录位置
     * @return bool 返回是否创建成功，成功为true，失败为false
     */
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