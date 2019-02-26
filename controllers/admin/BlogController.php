<?php
namespace controllers\admin;
use models\Blog;
use models\Category;
class BlogController
{

    //列表页
    public function index()
    {
        $data = new Blog;
        $d = $data->index();
        view('admin.blog.index',['data'=>$d]);

    }

    //显示添加表单
    public function create()
    {
    	$cat = new Category;
    	$data = $cat->categorydata();
        view('admin.blog.insert',['cat'=>$data]);
    }
    public function add(){
		$type = $_POST['type'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		$show = $_POST['is_show'];
		$top = isset($_POST['top']) ? $_POST['top'] : 0;
        $tags = isset($_POST['tags']) ? $_POST['tags'] : '';
        //上传图片
        $img = $this->uploads();   
        $add = new Blog;
        //添加标签判断是否为空
		if(!empty($tags))
		{
			$tags = trim($tags,','); //去掉首尾逗号
			$arr = explode(',',$tags);
			$tagsid = $add->tags($arr);  //返回的是标签的id
        }
        
        //判断是否上传图片
        $imgurl = $img[1] ? $img[0] : NULL; 
		
		//把数组里的id转成字符串用，号隔开
		$tagsid = implode(',',$tagsid);
		 
		$st = $add->insert($type,$title,$content,$show,$top,$imgurl,$tagsid);
		
        message($st ? '添加成功' : '添加失败', 2, '/admin/blog/create');
		
	}
	
	//删除文章
	public function delete($id='')
	{
		if(!empty($id)){

		   if(!isset($_SESSIION['id']))
		   {
			   $del = new Blog;
			   $data = $del->delete($id);
		   }
	   }
	   			  
	   message($data ? '删除成功' : '删除失败', 2, '/admin/blog/index');
	}
    
    public function uploads()
    {
    		$img = $_FILES['img'];
			//创建目录
    		$root = ROOT.'public/uploads/';
			//今天日期
			$date = date('Ymd');  //20180913
			//如果没有这个目录就创建目录
			if(!is_dir($root.$date))
			{
				//创建目录（第二个参数：有写的权限(只对linux系统有效，所以这里写不写都一样)）
				mkdir($root.$date,0777);
			}
			//生成唯一的名字
			$name = md5(time().rand(1,9999));  //32位字符串
			
			//补上文件的后缀，先取出原来这个图片的后缀
			//strrchr:从最后某一个字符串开始截取到最后
			$ext = strrchr($_FILES['img']['name'],'.');  //得到 .jpg
			
			//补上扩展名
			$name = $name.$ext;
			
			//移动图片
			$st = move_uploaded_file($_FILES['img']['tmp_name'],$root.$date.'/'.$name);
			
			//返回给编辑器
			echo json_encode([
				'success'=>$st,
				'msg'=>'上传失败',  //如果上面为false 就显示这句话
				'file_path'=>'/public/uploads/'.$name.'.php'  //上传成功保存在数据库的路径如果上传失败就没有这一项
			]);
			
			$url = '/uploads/'.$date.'/' . $name;
			//返回上传图片的路径和是否长传成功
			return [$url,$st];
			
    }

}
?>