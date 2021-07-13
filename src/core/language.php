<?php
$language_list = file_get_contents(LANG_PATH . "languages.json");//读取语言列表文件
$language_list = json_decode($language_list, true);//将语言列表的JSON数据解析
$language_list_json = array();//创建一个数组用于存储语言列表
foreach ($language_list as $item) {//读取语言支持文件
    $language_list_json[$item] = explode("=", explode("\n", str_replace("\n\n", "\n", str_replace("\r", "\n", file_get_contents(LANG_PATH . $item . ".lang"))))[0])[1];
    //读取每个语言的名称
}
$local_language = cookie::get("local_language");//读取当前设置的语言
if (!$local_language) {
    $h_a_l = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
    $h_a_l_1 = array();
    $h_a_l_1[0] = strstr($h_a_l, ",", true);
    $h_a_l = substr(str_replace($h_a_l_1[0], "", $h_a_l), 1);
    $h_a_l_1[1] = strstr($h_a_l, ";", true);
    if (isset($language_list_json[$h_a_l_1[1]])) {
        $local_language = $h_a_l_1[1];
    } elseif (isset($language_list_json[$h_a_l_1[0]])) {
        $local_language = $h_a_l_1[0];
    }
}
if (!$local_language) $local_language = "zh-cn";//未设置语言时加载简体中文
define("LOCAL_LANGUAGE", $local_language);//定义当前语言到全局常量
define("LANGUAGE_LIST", $language_list_json);//定义语言列表到常量
$language = array();//创建一个数组用来存储语言数据
foreach (explode("\n", str_replace("\n\n", "\n", str_replace("\r", "\n", file_get_contents(LANG_PATH . $local_language . ".lang")))) as $item) {//逐行读取语言数据
    if (!$item && substr($item, 0, 1) == "#") {//判断是否注释
        break;//注释行不处理
    }
    $a = explode("=", $item);//将每一行的数据进行分割
    $language[$a[0]] = $a[1];//将每一行的数据存入数组
    unset($item);//归还内存
}
define("L", $language);//定义语言数据到全局常量