<?php
session_start();
require 'bbs_db.php'; 


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "不正なアクセスです";
    exit;
}
$id = (int)$_GET['id'];//対象のスレッドIDを取得

try {
    $stmt = $pdo->prepare("SELECT * FROM threads WHERE id=:id AND user_id=:user_id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $thread = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$thread) {
        echo "スレッドが存在しません";
        exit;
    }

} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
    exit;
}

// 更新
$flash="";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);

    if ($title && $body) {
        try {
            $stmt = $pdo->prepare("UPDATE threads SET title=:title, body=:body, update_at=NOW() WHERE id=:id AND user_id=:user_id");
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':body', $body, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();

            $flash = "更新しました！";

            //更新内容反映
            $stmt = $pdo->prepare("SELECT * FROM threads WHERE id=:id AND user_id=:user_id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $thread = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $flash = "更新エラー: " . $e->getMessage();
        }
    } else {
        $flash = "何も入力されていません(>_<)";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>スレッド一覧</title>
        <link rel="stylesheet" href="edit_style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>
    <header>
        <h2 class="header-left">簡易掲示板<i class="fa-solid fa-comments" ></i></h2>
        <h2 class="header-center">ホーム</h2>
        <div class="header-right">
        <?php if(isset($_SESSION['user'])): ?>
            <!--ログイン済-->
            <a href="mypage.php"><i class="fa-solid fa-house-user"></i></a>
            <a class="logout-button" href="logout.php">ログアウト</a>
        <?php endif; ?>
        </div>
    </header>
    <body>
         <div class="side-menu">
            <a href="all_thread.php"><i class="fa-solid fa-house"></i></a>
            <a href="create_threads.php"><i class="fa-solid fa-plus"></i></a>
            <?php if(isset($_SESSION['user'])): ?>
                <!--ログイン済-->
                <a href="mypage.php"><i class="fa-solid fa-house-user"></i></a>
            <?php else: ?>
                <!--未ログインのとき-->  
                <a href="login_php"><i class="fa-solid fa-circle-user"></i></a>
            <?php endif; ?>
        </div>
        <?php if(isset($_SESSION['user'])): ?>
            <div class="create-box">
                <h3>スレッド編集</h3>
                <div class="flex-top">
                    <div class="face"><i class="fa-solid fa-circle-user"></i></div>
                    <div><h4>
                        <?=htmlspecialchars($_SESSION['user'],ENT_QUOTES) ?>
                    </h4></div>
                </div>
                <form method="post">
                    <div class="flex">
                        <p>タイトル</p>
                        <input type="text" name="title" value="<?= htmlspecialchars($thread['title'], ENT_QUOTES) ?>" required>
                    </div>
                    <textarea name="body" required><?= htmlspecialchars($thread['body'],ENT_QUOTES)?></textarea>
                    <button type ="submit" >更新</button>
                </form>
            </div>
        <?php else: ?>
            <div class="create-box login">
                <h3>登録して更新<i class="fa-solid fa-angle-down"></i></h3>
                <a href="login.php">ログイン</a>
            </div>
        <?php endif; ?>
            <?php if($flash): ?>
            <div class="flash-message">
                <?= htmlspecialchars($flash, ENT_QUOTES) ?>
            </div>
            <?php endif; ?>

    </body>
</html>