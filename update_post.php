<?php 
session_start();
require 'bbs_db.php'; 


// 更新
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = trim($_POST['body']);
    $post_id =$_POST['post_id'];
    $thread_id = $_POST['thread_id'];

    if ($body) {
        try {
            $stmt = $pdo->prepare("UPDATE posts SET body=:body, update_at=NOW() WHERE id=:id AND user_id=:user_id");
            $stmt->bindValue(':body', $body, PDO::PARAM_STR);
            $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['flash'] = "更新しました！";

            //更新内容反映
            $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=:id AND user_id=:user_id");
            $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $posts = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $_SESSION['flash'] = "更新エラー: " . $e->getMessage();
        }
    } else {
        $SESSION['flash'] = "何も入力されていません(>_<)";
    }

    header("Location: all_thread.php");
}

?>

