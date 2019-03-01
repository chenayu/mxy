<?php
namespace controllers;
use models\Blog;
use models\Category;
use libs\Page;
class IndexController
{
    public function index()
    {
        $data = new Blog;
        //取出文章
        $d = $data->index();

		$datas = [];
		foreach($d[0] as $v)
		{
			 //根据文章id找出标签添加到数组
			$tags = $data->tages($v['id']);
			$v['tags'] = $tags;
			$datas[]=$v;
        }
        $btns = $d[1];
         
        //参数一总文章数，参数二每页显示条数
        $c = Page::makePage($btns,10);
        
        //取出分类
        $cat = new Category;
        $category = $cat->categorydata();
        view('index.index',['data'=>$datas,'cat'=>$category,'btns'=>$c]);
    }

    //取出内容
    public function content()
    {
        $blog = new Blog;
        $data = $blog -> content();

        if(!$data)
        {
             error();
        }

        view('index.content',['data'=>$data]);
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