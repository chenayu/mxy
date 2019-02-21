<?php
namespace models;
use PDO;

class Blog extends Base
{
    public function index($id)
    {
        $stmt = self::$pdo->prepare('SELECT * FROM articles where id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

?>