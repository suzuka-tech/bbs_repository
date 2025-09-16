<?php
session_start();
require 'bbs_db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM threads WHERE id=:id AND user_id=:user_id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['flash'] = "スレッドを削除しました！";
    } catch (PDOException $e) {
        $_SESSION['flash'] = "エラー: " . $e->getMessage();
    }

    header("Location: all_thread.php");
    exit;
}
?>
