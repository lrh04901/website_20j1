<?php

class get
{
    public static function index(){
        loadHead(L["INDEX_TITLE"],["css"=>[["index","media=\"screen\""]]]);
        loadBody("index");
    }
    public static function update(){
        loadHead("更新站点",["css"=>["zui.min","zui.uploader.min"],"js"=>["zui.uploader.min"]]);
        loadBody("update");
    }
    public static function classIntroduce(){
        loadHead("胡杨班",["css"=>[["classIntroduce","media=\"screen\""]]]);
        loadBody("classIntroduce");
    }
}