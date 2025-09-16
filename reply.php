<?php
session_start();
require 'bbs_db.php'; 


if (!isset($_POST['thread_id']) || !is_numeric($_POST['thread_id'])) {
    echo "不正なアクセスです";
    exit;
}else{
    $thread_id =$_POST['thread_id'];
    $body = trim($_POST['body']);

    if ($body) {

        try{
            $stmt = $pdo->prepare("INSERT INTO posts (body,user_id,thread_id) VALUES (:body,:user_id,:thread_id) ");
            $stmt->bindValue(':thread_id',$thread_id,PDO::PARAM_INT);
            $stmt->bindValue(':body', $body, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['flash'] = "返信しました！";
            header("Location: all_thread.php");
            exit;

        }catch(PDOException $e){
            echo "返信を作成できませんでした。： " . $e->getMessage();
        }

    }else{
        $_SESSION['flash'] = "エラー：何も入力されていません(>_<)";
        header("Location: all_thread.php");
        exit;
    }
    
    
}

?>