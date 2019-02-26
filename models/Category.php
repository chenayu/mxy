<?php
namespace models;
use PDO;
class Category extends Base
{
   //添加分类
   public function addcategory($cat_name)
   {
       $stmt = self::$pdo->prepare('INSERT INTO types(id,cat_name) VALUES(NULL,?)');
       return $stmt->execute([$cat_name]);
   }

   //取出分类
   public function categorydata()
   {
       $stmt = self::$pdo->prepare('SELECT id,cat_name FROM types');
       $stmt->execute();
       return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   //删除
   public function delete($id)
   {   
       $stmt = self::$pdo->prepare('DELETE FROM types WHERE id = ?');
       return $stmt->execute([$id]);
        
      
   }
}

?>