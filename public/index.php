<?php
define('ROOT', __DIR__ . '/../');

//设置时区
date_default_timezone_set('PRC');

// 使用 redis 保存 SESSION
ini_set('session.save_handler', 'redis');
// 设置 redis 服务器的地址、端
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=3');

session_start();

require(ROOT.'libs/functions.php');

function autoload($class)
{
    //类的名字和目录是一致的，把controller\UserController的\替换成/
    $path = str_replace('\\','/',$class);
    //判断访问的文件是否存在
    if(file_exists(ROOT.$path.'.php')){
        require(ROOT.$path.'.php');  
    }else{
        echo 'cw';
        exit;    
    }

}
//注册自动加载类
spl_autoload_register('autoload');

if($_SERVER['REQUEST_URI']!=='/')
{
 
    $pathInfo = $_SERVER['REQUEST_URI'];
    //根据/拆分为数组
    $pathInfo = explode('/',$pathInfo);
    //首字母大写拼出控制器文件名
    $controller = ucfirst($pathInfo[1]).'Controller'; 
    //判断是否有传方法参数
    if(isset($pathInfo[2])){
        $action = $pathInfo[2]; 
    }

}else{
    //如果没有参数就给一个默认的
    $controller = 'IndexController';  //默认的控制器
    $action = 'index';  //默认的方法
}

$fullController = 'controllers\\'.$controller;

$c = new $fullController;
$c->$action();

?>