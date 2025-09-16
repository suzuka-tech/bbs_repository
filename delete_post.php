<?php
session_start();
require 'bbs_db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = (int)$_POST['post_id'];
    $thread_id = (int)$_POST['thread_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id=:id AND user_id=:user_id");
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['flash'] = "返信を削除しました！";
    } catch (PDOException $e) {
        $_SESSION['flash'] = "エラー: " . $e->getMessage();
    }

    header("Location: all_thread.php");
    exit;
}
?>
