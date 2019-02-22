<?php
namespace controllers;

class IndexController
{
    public function index()
    {
        $data = new \models\Blog;
        $d = $data->index();

        view('index.index',['data'=>$d]);
    }

    //生成静态页
    public function jthtml()
    {
        
        $data = new \models\Blog;
        $data->contenthtml();
    }

    public function delhtml()
    {
        $data = new \models\Blog;
        $data->delFile();
  
    }
}
?>