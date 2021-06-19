<?php
/**
 * 重定向模块
 * 防止用户访问不正确的地址导致无法正常加载
 */
if (strstr($_SERVER["REQUEST_URI"],"index.php")){
    header("Location:./?/");
}
if (strstr($_SERVER["REQUEST_URI"], ".php")) {
    header("Location:../../");
}