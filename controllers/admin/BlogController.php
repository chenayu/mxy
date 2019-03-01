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
		$datas = [];
		foreach($d as $v)
		{
			echo '<pre>';
			 //根据文章id找出标签添加到数组
			$tags = $data->tages($v['id']);
			$v['tags'] = $tags;
			$datas[]=$v;
		}
		// var_dump($datas);
		// exit;
        view('admin.blog.index',['data'=>$datas]);

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
		$tags = isset($_POST['tags']) && $_POST['tags']!=',' ? $_POST['tags'] : '';
		 
        //上传图片
        $img = $this->uploads();   
		$add = new Blog;
		
		//判断是否上传图片
		$imgurl = $img[1] ? $img[0] : NULL; 
		 
		//添加文章返回刚文章的id
		$id = $add->insert($type,$title,$content,$show,$top,$imgurl);
		
		//添加标签判断是否为空
		if(!empty($tags))
		{
			$tags = trim($tags,','); //去掉首尾逗号
			$arr = explode(',',$tags); //把标签字符串以,转成数组
			$tagsid = $add->tags($arr,$id);  //返回的是标签的id
        }
  
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
	
	//上传图片
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
				'file_path'=>'/public/uploads/'.$name.'.php'  //上传失败就没有这一项
			]);
			
			$url = '/uploads/'.$date.'/' . $name;
			//返回上传图片的路径和是否长传成功
			return [$url,$st];
			
    }

}
?>