<?php
session_start();
require 'bbs_db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


if (!isset($_GET['thread_id']) || !is_numeric($_GET['thread_id'])) {
    echo "不正なアクセスです";
    exit;
}else{
    $user_id = (int)$_GET['user_id'];
    $thread_id = (int)$_GET['thread_id'];

    try {
        $stmt = $pdo->prepare("SELECT threads.*, users.username
                            FROM threads
                            JOIN users ON threads.user_id = users.id
                            WHERE threads.id=:id AND threads.user_id=:user_id");
        $stmt->bindValue(':id', $thread_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id',$user_id , PDO::PARAM_INT);
        $stmt->execute();
        $thread = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$thread) {
            echo "返信が存在しません";
            exit;
        }

    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
        exit;
    }

    try{

       $stmt = $pdo->prepare("
            SELECT posts.*, users.username
            FROM posts
            JOIN users ON users.id = posts.user_id
            WHERE posts.thread_id = :id
            ORDER BY COALESCE(posts.update_at, posts.created_at) DESC
            ");
        $stmt->bindValue(':id', $thread_id, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }catch(PDOException $e){
        echo "エラー： ". $e->getMessage();
        exit;
    }

 }


//対象レス取得

if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
    echo "不正なアクセスです";
    exit;
}
$post_id = (int)$_GET['post_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=:id AND user_id=:user_id");
    $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $post_edit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post_edit) {
        echo "返信が存在しません";
        exit;
    }

} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
    exit;
}




?>






<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>スレッド一覧</title>
        <link rel="stylesheet" href="detail_style.css">
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
        <?php else: ?>
            <!--未ログインのとき-->
            <a class="login-button" href="login.php">ログイン</a>
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

        <?php if(!empty($thread)): ?>
            <div class="threads-box">
                    
                    <?php
                        $date_at = $thread['update_at'] ?? $thread['created_at'];
                        $date_at_p = $posts['update_at'] ?? $posts['created_at'];
                    ?>
                    <div class="index-box">
                        <div class="index-top">
                            <div class="face"><i class="fa-solid fa-circle-user"></i></div>
                            <div><h4>
                                <?= htmlspecialchars($thread['username'],ENT_QUOTES) ?>
                            </h4></div>
                            <div><p><?= htmlspecialchars($date_at,ENT_QUOTES) ?></p></div>
                            <?php if ($thread['user_id'] === $_SESSION['user_id']): ?>
                                <!-- 自分のスレッドだけ編集可能 -->
                                <div class="edit"><a href="edit_threads.php?id=<?= $thread['id'] ?>"><i class="fa-regular fa-pen-to-square"></i></a></div>
                                <div class="trash"><form action="delete_thread.php" method="post" onsubmit="return confirm('本当に削除しますか？');">
                                    <input type="hidden" name="id" value="<?= $thread['id'] ?>">
                                    <button type="submit"><i class="fa-regular fa-trash-can"></i></button>
                                </form></div>
                            <?php endif; ?>
                        </div>
                        <h3><?= htmlspecialchars($thread['title'],ENT_QUOTES) ?></h3>
                        <p class="text"><?= nl2br(htmlspecialchars($thread['body'],ENT_QUOTES)) ?></p>
                    </div>
                    <?php if(isset($_SESSION['user'])): ?>
                        <?php if(!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <div class="reply">
                                    <div class="flex-top">
                                        <div class="reply-mark"><i class="fa-solid fa-reply"></i></div>
                                        <div class="face"><i class="fa-solid fa-circle-user"></i></div>
                                        <div><h4>
                                            <?= htmlspecialchars($post['username'],ENT_QUOTES) ?>
                                        </h4></div>
                                        <div><p><?= htmlspecialchars($date_at_p,ENT_QUOTES) ?></p></div>
                                        <?php if ($post['user_id'] === $_SESSION['user_id']): ?>
                                            <!-- 自分のスレッドだけ編集可能 -->
                                            <div class="edit"><a href="edit_post.php?id=<?= $post['id'] ?>"><i class="fa-regular fa-pen-to-square"></i></a></div>
                                            <div class="trash"><form action="delete_post.php" method="post" onsubmit="return confirm('返信を本当に削除しますか？');">
                                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                <input type="hidden" name="thread_id" value="<?= $thread['id'] ?>">
                                                <button type="submit"><i class="fa-regular fa-trash-can"></i></button>
                                            </form></div>
                                        <?php endif; ?>
                                        </div>
                                        <?php if($post['id']=$post_id): ?>
                                            <form action="update_post.php" method="post">
                                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                <input type="hidden" name="thread_id" value="<?= $thread['id'] ?>">
                                                <textarea name="body" required><?= htmlspecialchars($post['body'],ENT_QUOTES)?></textarea>
                                                <button type ="submit" >完了</button>
                                            </form>
                                        <? else: ?>
                                            <p class="text"><?= nl2br(htmlspecialchars($post['body'],ENT_QUOTES)) ?></p>
                                        <? endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="reply">
                                <p>まだ返信がありません！</p>
                            </div>
                        <?php endif; ?>
                     <?php else: ?>
                        <div class="reply login">
                            <i class="fa-solid fa-reply"></i>
                            <h3>ログインして返信を編集<i class="fa-solid fa-angle-down"></i></h3>
                            <a href="login.php">ログイン</a>
                        </div>
                    <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash-message">
                <?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES) ?>
            </div>
        <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
    </body>
</html>