<?php
namespace models;
use PDO;

class Blog extends Base
{
    public function index()
    {
        // select * from articles a left join types b on a.type_id = b.id  
        // $stmt = self::$pdo->prepare('SELECT left(content,50) as content,id,title,created_at FROM articles limit 10');
       $stmt = self::$pdo->prepare('SELECT left(a.content,50) as content,
            a.id,a.title,a.created_at,b.cat_name,is_show,top,a.img
            FROM articles a LEFT JOIN types b ON a.type_id = b.id');

       $stmt->execute();
       return $blog = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function tages($id)
    {
        $stmt = self::$pdo->prepare('SELECT c.* FROM articles a left join article_tags b on a.id = b.article_id left join tags c on b.tags_id = c.id where b.article_id =?');
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //添加
    public function insert($type,$title,$content,$is_show,$top,$img)
    {
     $stmt = self::$pdo->prepare('INSERT INTO 
        articles(title,content,created_at,updated_at,is_show,type_id,top,img) 
        VALUES(?,?,now(),now(),?,?,?,?)');
     $st = $stmt->execute([
         $title,$content,$is_show,$type,$top,$img
     ]);

   

     if($st){
        return self::$pdo->lastInsertId();
     }else{
         //打印错误
        var_dump($stmt->errorInfo());
       
     }
     

    }

    //删除文章
    public function delete($id)
    {
        //取出保存图片的字段
        $ac = new Blog;
        $url = $ac->fetch('articles','img',$id);
       
        //判断有没有图片有就删除
        if($url!==NULL)
        {
            $url = ltrim($url,'/');
            if(is_file($url))
                unlink($url);
        }
        
        $st = $ac->deletes('articles',$id);
       
        if($st){
            return $ac->deletes('article_tags',$id,'article_id');
        }else{
            return false;
        }
        
    
    }
    
    //添加标签
    public function tags($tags,$id)
    {
    	foreach($tags as $v){
    		$stmt = self::$pdo->prepare('INSERT INTO tags(id,tags) VALUES(NULL,?)');
       		$st = $stmt->execute([$v]);
       		//保存新插入标签的ID
            $tagid = self::$pdo->lastInsertId(); 
            
            //判断是否保存标签成功
            if($st)
            {
                //把保存的标签id和文章id放入关联表
                $addtag = self::$pdo->prepare('INSERT INTO article_tags(article_id,tags_id) VALUES(?,?)');
                $st2 = $addtag->execute([$id,$tagid]);
            }
            
    	}
    	return $st2;
    	
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