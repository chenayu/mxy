<?php
namespace models;
use PDO;

class Blog extends Base
{
    public function index()
    {
       $stmt = self::$pdo->prepare('SELECT left(content,50) as content,title,created_at FROM articles limit 10');
       $stmt->execute();
       return $blog = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //生成静态页
    public function contenthtml()
    {
       //判断静态文件是否存在
        $file = scandir('./contents');

       if($file['2']){
            echo '存在'; 
            exit;       
       }
  
        $stmt = self::$pdo->prepare('SELECT * FROM articles');
        $stmt->execute();
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($blogs);
        // exit;
    
        // 开启缓冲区
        ob_start();
    
        // 生成静态页
        foreach($blogs as $v)
        {
            // 加载视图
            view('blogs.content', [
                'blog' => $v,
            ]);
            // 取出缓冲区的内容
            $str = ob_get_contents();
            // 生成静态页
            file_put_contents(ROOT.'public/contents/'.$v['id'].'.html', $str);
            // 清空缓冲区
            ob_clean();
        }
    
    }

    //删除静态
    public function delFile()
    {
		//打开目录
		$handle = opendir('./contents');
		//while循环，取出目录的每一行
		while($lineStr = readdir($handle)){
		//判断并且除去 . 和 .. 
            if($lineStr=="."||$lineStr=="..") continue;
         
				unlink('./contents/'.$lineStr);
		}
		closedir($handle);
		 
	}

 
}

?>