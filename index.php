<?php
if (file_exists("debug")){
    include("system/core/dev/core.php");
    core::initialize();
    core::runWeb();
}else{
    include("system/core/core.phar");
    include("phar://core.phar/core.php");
    core::initialize();
    core::runWeb();
}
//if (file_exists("system/core/core.php")){
////    include("system/core/core.php");
////    core::initialize();
////    core::runWeb();
//}else{
//    die("核心代码不存在");
//}
