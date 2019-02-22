<?php
define('ROOT', __DIR__ . '/../');

//设置时区
date_default_timezone_set('PRC');

// 使用 redis 保存 SESSION
ini_set('session.save_handler', 'redis');
// 设置 redis 服务器的地址、端
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=3');

session_start();

//包含公共方法
require(ROOT.'libs/functions.php');


if($_SERVER['REQUEST_URI']!=='/')
{
 
    $pathInfo = $_SERVER['REQUEST_URI'];
    //根据/拆分为数组
    $pathInfo = explode('/',$pathInfo);

    if(count($pathInfo)==4){
        $controller = ucfirst($pathInfo[2]).'Controller'; 
        $action = $pathInfo[3]; 
        $fullController = 'controllers\admin\\'.$controller;

    }else{
        //首字母大写拼出控制器文件名
        $controller = ucfirst($pathInfo[1]).'Controller'; 
        //判断是否有传方法参数
        if(isset($pathInfo[2]))
        {
            $action = $pathInfo[2];
        }else{
            error('参数不正确');
            exit;
        }

        $fullController = 'controllers\\'.$controller;
    }

 
    
}else{
    //如果没有参数就给一个默认的
    $fullController = 'controllers\IndexController';  //默认的控制器
    $action = 'index';  //默认的方法
}

function autoload($class)
{
    //类的名字和目录是一致的，把controller\UserController的\替换成/
    $path = str_replace('\\','/',$class);
   
    //判断访问的文件是否存在
    if(file_exists(ROOT.$path.'.php')){
        require(ROOT.$path.'.php'); 

    }else{
        error('访问的文件不存在');
        exit;    
    }

}
//注册自动加载类
spl_autoload_register('autoload');

$c = new $fullController;

//判断访问的方法是否存在
if(method_exists($c,$action)){
    $c->$action();
}else{
    //提示错误
    error('方法不存在');
}

 



?>