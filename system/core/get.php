<?php

class get
{
    //Array ( [0] => title=更新站点 [1] => css=zui.min,zui.uploader.min,update [2] => js=zui.uploader.min,webuploader.min,update [3] => )
    public static function oneKeyLoad(string $name){
        $html = file_get_contents(HTML_PATH.$name.".html");
        $args = strstr($html,"\n",true);
        $html = str_replace($args,"",$html);
        $args = str_replace("<!--","",str_replace("-->","",$args));
        $args = trim($args," ");
        $args_array = explode(";",$args);
//        print_r($args_array);
        $title = explode("=",$args_array[0])[1];
        $css = array();
        foreach (explode(",",explode("=",$args_array[1])[1]) as $item) {
            if (strstr($item,"(")&&strstr($item,")")){
                $css_name = strstr($item,"(",true);
                $css_args = trim(str_replace($css_name,"",$item),"()");
                $css[count($css)] = [$css_name,$css_args];
            }else{
                $css[count($css)] = $item;
            }
        }
        $js = array();
        foreach (explode(",",explode("=",$args_array[2])[1]) as $item){
            $js[count($js)] = $item;
        }
        loadHead($title,["css"=>$css,"js"=>$js]);
        loadBodyByText($html);
    }
    public static function index()
    {
        loadHead(L["INDEX_TITLE"], ["css" => [["index", "media=\"screen\""]]]);
        loadBody("index");
    }

    public static function update()
    {
        self::oneKeyLoad("update");
//        loadHead("更新站点", ["css" => ["zui.min", "zui.uploader.min", "update"], "js" => ["zui.uploader.min", "webuploader.min", "update"]]);
//        loadBody("update");
    }

    public static function classIntroduce()
    {
        loadHead("胡杨班", ["css" => [["classIntroduce", "media=\"screen\""]]]);
        loadBody("classIntroduce");
    }

    public static function yule()
    {
        loadHead("娱乐", ["css" => [["yule", "media=\"screen\""]]]);
        loadBody("yule");
    }

    public static  function Page404()
    {
        loadHead("404 Not Found", ["css" => [["Page404", "media=\"screen\""]]]);
        loadBody("Page404");
    }
}