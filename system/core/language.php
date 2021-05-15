<?php
$language_list = file_get_contents(LANG_PATH."languages.json");
$language_list = json_decode($language_list,true);
$language_list_json = array();
$local_language = cookie::get("local_language") ? cookie::get("local_language") : "zh-cn";
define("LOCAL_LANGUAGE",$local_language);
foreach ($language_list as $item) {
    $language_list_json[$item] = explode("=",explode("\n",str_replace("\n\n","\n",str_replace("\r","\n",file_get_contents(LANG_PATH.$item.".lang"))))[0])[1];
}
define("LANGUAGE_LIST",$language_list_json);
$language = array();
foreach (explode("\n",str_replace("\n\n","\n",str_replace("\r","\n",file_get_contents(LANG_PATH . $local_language . ".lang")))) as $item) {
    if(!$item && substr($item,0,1)=="#"){
        break;
    }
    $a = explode("=",$item);
    $language[$a[0]]=$a[1];
    unset($item);
}
define("L",$language);