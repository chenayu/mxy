<?php
namespace controllers\admin;
use models\Category;

class CategoryController
{
    //显示添加分类页
    public function insert()
    {
        view('admin.category.insert');
    }

    //添加分类
    public function addcategory()
    {
        $cat_name = $_POST['name'];
        $cat =  new Category;
        $add = $cat->addcategory($cat_name);
        //消息提示
        message($add ? '添加成功' : '添加失败', 2, '/admin/category/insert');
    }

    //显示分类页
    public function index()
    {
        
        $cat =  new Category;
        $data = $cat->categorydata();
        
        view('admin.category.index',['data'=>$data]);
    }

    //删除
    public function delete($id='')
    {
    	if(!empty($id)){
    		 $del = new Category;
    		 
	        if(!isset($_SESSIION['id']))
	        {
	            $data = $del->delete($id);
	        }
    	}
               
        message($data ? '删除成功' : '删除失败', 2, '/admin/category/index');
    }
}
?>