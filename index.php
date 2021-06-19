<?php
if (file_exists("system/core/core.php")){
    include("system/core/core.php");
    core::initialize();
    core::runWeb();
}else{
    die("核心代码不存在");
}
