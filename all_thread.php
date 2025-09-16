<?php
session_start();
require 'bbs_db.php';


try{

    //スレッドを新しい順に取得
    $stmt = $pdo->prepare("SELECT threads.id, threads.title, threads.created_at,threads.update_at,threads.body,threads.user_id,users.username
                            FROM threads
                            JOIN users ON users.id = threads.user_id
                            ORDER BY COALESCE(threads.update_at, threads.created_at) DESC");

    $stmt -> execute();
    $threads = $stmt ->fetchAll(PDO::FETCH_ASSOC);


}catch(PDOException $e){
    echo "エラー： ". $e->getMessage();
    exit;
}


?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>スレッド一覧</title>
        <link rel="stylesheet" href="all_threads_style.css">
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

        <?php if(!empty($threads)): ?>
            <div class="threads-box">
                <?php foreach ($threads as $thread): ?>
                    
                    <?php
                         // 1件ごとに表示日付を決定（update_at がなければ created_at）
                        $date_at = $thread['update_at'] ?? $thread['created_at'];
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
                            <div class="detail">
                                <form action="detail_thread.php" method="post">
                                    <input type="hidden" name="user_id" value="<?= $thread['user_id'] ?>">
                                    <input type="hidden" name="thread_id" value="<?= $thread['id'] ?>">
                                    <button type ="submit" ><i class="fa-regular fa-comment-dots"></i></button>
                                </form>
                            </div>
                        </div>
                        <h3><?= htmlspecialchars($thread['title'],ENT_QUOTES) ?></h3>
                        <p class="text"><?= nl2br(htmlspecialchars($thread['body'],ENT_QUOTES)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="threads-box"><p class="threads-none">まだスレッドはありません<p>
            <a class="pencil" href="create_threads.php">新規投稿をさっそくつくる<i class="fa-solid fa-pen-to-square"></i></a></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash-message">
                <?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES) ?>
            </div>
        <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
    </body>
</html>