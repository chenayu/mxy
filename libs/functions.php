<?php

//配置文件
function config($name)
{
    static $config = null;
    if($config === null)
    {
        // 引入配置文件 
        $config = require(ROOT.'config.php');
    }
    return $config[$name];
}

// 加载视图
// 参数一、加载的视图的文件名
// 参数二、向视图中传的数据
function view($viewFileName, $data = [])
{
    // 解压数组成变量
    extract($data);

    $path = str_replace('.', '/', $viewFileName) . '.html';

    // 加载视图
    require(ROOT . 'views/' . $path);
}

function error()
{
    view('index.404');
    exit;
}


//页面跳转
function redirect($url)
{
    header('Location:' . $url);
    exit;
}

//消息提示
function message($message, $type, $url, $seconds = 5)
{
    if($type == 0)
    {
        echo "<script>alert('{$message}');location.href='{$url}';</script>";
        exit;

    }
    else if($type == 1)
    {
        // 加载消息页面
        view('common.success', [
            'message' => $message,
            'url' => $url,
            'seconds' => $seconds
        ]);
    }
    else if($type==2)
    {
        // 把消息保存到 SESSION
        $_SESSION['_MESS_'] = $message;
        // 跳转到下一个页面
        redirect($url);
    }
}
?>