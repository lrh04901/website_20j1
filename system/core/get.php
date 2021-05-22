<?php

class get
{
    public static function index(){
        loadHead("欢迎来到20计1",["css"=>[["index","media=\"screen\""]]]);
        loadHTML("index");
    }
    public static function update(){
        loadHead("更新站点",["css"=>["zui.min.css","zui.uploader.min.css"],"js"=>["zui.uploader.min.js"]]);
        loadHTML("update");
    }
    public static function classIntroduce(){
        loadHead("胡杨班");
        loadHTML("classIntroduce");
    }
}