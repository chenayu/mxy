<?php
namespace controllers;

class IndexController
{
    public function index()
    {
        $data = new \models\Blog;
        $d = $data->index(1);
        $b='tom';

        view('index.index',['data'=>$d]);
    }

    public function hello()
    {
        echo 'hello';
    }
}
?>