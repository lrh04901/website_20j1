<?php
include "redirect.php";

/**
 * 数组处理模块
 */
class arrayTool
{
    /**
     * 在数组最后追加一个数据
     * @param array $array 需要处理的数据
     * @param object $value 需要插入的数据
     * @return array 返回新的数组
     */
    static function add(array $array,object $value):array
    {
        $temp = array();
        for ($i = 0; $i <= count($array); $i++) {
            if ($i == count($array)) {
                $temp[$i] = $value;
            } else {
                $temp[$i] = $array[$i];
            }
        }
        return $temp;
    }

    /**
     * 删除数组中指定数据的项
     * @param array $array 需要处理的数组
     * @param object $value 需要删除的数据
     * @return array 返回新的数组
     */
    static function remove(array $array,object $value):array
    {
        $temp = array();
        $index = 0;
        for ($i = 0; $i < count($array); $i++) {
            if ($array[$i] == $value) {
                $index = $i;
                break;
            }
        }
        for ($i = 0; $i < $index; $i++) {
            $temp[$i] = $array[$i];
        }
        for ($i = $index + 1; $i < count($array); $i++) {
            $temp[$i - 1] = $array[$i];
        }
        return $temp;
    }

    /**
     * 删除数组中的第一项
     * @param array $array 需要处理的数组
     * @return array 返回新的数组
     */
    static function removeFirst(array $array):array
    {
        $temp = array();
        for ($i = 1; $i < count($array); $i++) {
            $temp[count($temp)] = $array[$i];
        }
        return $temp;
    }

    /**
     * 删除数组中指定位置的数据
     * @param array $array 需要处理的数组
     * @param int $index 需要删除的索引
     * @return array 返回新的数组
     */
    static function removeByIndex(array $array,int $index):array
    {
        $newArray = array();
        for ($i = 0; $i < count($array); $i++) {
            if (array_keys(array()[$i]) !== $index) {
                $newArray[array_keys($array)[$i]] = $array[array_keys($array)[$i]];
            }
        }
        return $newArray;
    }
}