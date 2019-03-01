<?php
namespace models;
use PDO;

class Blog extends Base
{
    //首页取数据
    public function index()
    {

        $where = 1;
        $where ="is_show=1";
        $value = [];

        if(isset($_GET['keyword'])&&$_GET['keyword'])
		{
            $where .=" AND(title LIKE ? OR content LIKE ?)";
            $value[]='%'.$_GET['keyword'].'%';
            $value[]='%'.$_GET['keyword'].'%';
        }

        if(isset($_GET['tag']) && $_GET['tag'])
		{
            $tagid = $_GET['tag'];
            
            //获取返回的文章id
            $tagid= $this->tagid($tagid);
            // var_dump($tagid);
            $id = [];
            //循环出拼成字符串
            foreach($tagid as $k=>$v)
            {
                $id[]= $v['article_id'];
            }
            $str = implode(',',$id);
            $where .= " AND a.id in({$str})";
        }
        //根据类别取出数据
        if(isset($_GET['cat']) && $_GET['cat'])
        {
            $where .=" AND a.type_id = ?";
            $value[]= $_GET['cat'];
        }

        


        //分页
        $perpage = 10; //每页15条
         //max表示如果(int)$_GET['page']小于1就取左边1
        $page  = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        
        $offset = ($page-1)*$perpage;  //(当前页-1)*每页的条数
        //取出总的页数
        $pagestmt = self::$pdo->prepare("SELECT COUNT(id) FROM articles WHERE $where");
		$pagestmt->execute($value);
        $count = $pagestmt->fetch(PDO::FETCH_COLUMN);  //总文章数
        $pageCount = ceil($count / $perpage);  //计算出总页数
         
   
        //end分页

       $stmt = self::$pdo->prepare("SELECT left(a.content,97) as content,
            a.id,a.title,a.created_at,b.cat_name,b.id as catid,is_show,top,a.img
        FROM articles a LEFT JOIN types b ON a.type_id = b.id WHERE {$where} LIMIT $offset,$perpage");

       $stmt->execute($value);
       $blog = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
       return [$blog,$count];
    }

    //取出内容详情页
    public function content()
    {
        $id = $_GET['id'];
        $stmt = self::$pdo->prepare('SELECT * FROM articles WHERE id = ? AND is_show = 1');
        $stmt->execute([$id]);
        return $st = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    //查询出标签
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

    //根据传过来的id取出所所属文章id
    public function tagid($id)
    {
        $stmt = self::$pdo->prepare('SELECT article_id FROM article_tags WHERE tags_id=?');
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    
    //添加标签$tags保存标签的数组ph
    public function tags($tags,$id)
    {
    	foreach($tags as $v){
    		$stmt = self::$pdo->prepare('INSERT INTO tags(id,tags) VALUES(NULL,?)');
       		$st = $stmt->execute([$v]);
       		//获取新插入标签的ID
            $tagid = self::$pdo->lastInsertId(); 
            
            //判断是否保存标签成功
            if($st)
            {
                //把保存的标签id和文章id放入标签关联表
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