<?php
session_start();
require 'bbs_db.php';//PDO接続


try{
    $username = $_SESSION['user'];
    //スレッドを新しい順に取得
    $stmt = $pdo->prepare("SELECT threads.id, users.username
                            FROM threads
                            JOIN users ON users.id = threads.user_id
                            WHERE users.username=:username");
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt -> execute();
    $threads = $stmt ->fetchAll(PDO::FETCH_ASSOC);


}catch(PDOException $e){
    echo "エラー： ". $e->getMessage();
    exit;
}

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $body = trim($_POST['body']);
    $title = trim($_POST['title']);

    if ($title && $body) {

        try{
            $stmt = $pdo->prepare("INSERT INTO threads (title,body,user_id) VALUES (:title,:body,:user_id) ");
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':body', $body, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['flash'] = "投稿しました！";
            header("Location: create_threads.php");
            exit;

            exit;
        }catch(PDOException $e){
            echo "投稿を作成できませんでした。： " . $e->getMessage();
        }

    }else{
        $_SESSION['flash'] = "エラー：何も入力されていません(>_<)";
        header("Location: create_threads.php");
        exit;
    }
    
    
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>スレッド一覧</title>
        <link rel="stylesheet" href="create_style.css">
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
                <h3>新規スレッド</h3>
                <div class="flex-top">
                    <div class="face"><i class="fa-solid fa-circle-user"></i></div>
                    <div><h4>
                        <?=htmlspecialchars($_SESSION['user'],ENT_QUOTES) ?>
                    </h4></div>
                </div>
                <form method="post">
                    <div class="flex">
                        <p>タイトル</p>
                        <input type="text" name="title" placeholder="タイトルを作成…">
                    </div>
                    <textarea name="body" placeholder="…"></textarea>
                    <button type ="submit" >投稿</button>
                </form>
            </div>
        <?php else: ?>
            <div class="create-box login">
                <h3>登録して投稿<i class="fa-solid fa-angle-down"></i></h3>
                <a href="login.php">ログイン</a>
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