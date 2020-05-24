<?php
//设置字体
header("content-type:text/html;charset=utf-8");

define('HEA_URL', 'http://localhost/index.php');

//设置系统的模式
define('APP_DEBUG',true);   //开发
//define('APP_DEBUG',false);   //生产

define('CSS_URL1', '/Public/Home/css/');
define('IMG_URL1', '/Public/Home/images/');

define('ADMIN_CSS_URL1','/Public/Admin/css/');
define('ADMIN_FONTS_URL1','/Public/Admin/fonts/');
define('ADMIN_JS_URL1','/Public/Admin/js/');

include "./ThinkPHP/ThinkPHP.php";
