<?php

class get
{
    public static function index():void{
        loadHead("欢迎来到20计1",["js"=>["index"],"css"=>["index"]]);
        loadHTML("index");
    }
}