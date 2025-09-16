<?php
session_start();
require 'bbs_db.php';//PDO接続

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    $err=false;

    try{
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username=:username");
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        //password-verify : 一致すれば true、一致しなければ false を返す
        if($user && password_verify($password,$user['password'])){
            $_SESSION['user'] = $username;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['flash'] = "ログイン完了!";
            header("Location: all_thread.php");
            exit;
        }else{
            $err=true;
        }

    }catch(PDOException $e){
        echo "登録エラー: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>ログイン画面</title>
        <link rel="stylesheet" href="login_style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <header><h2>簡易掲示板<i class="fa-solid fa-comments" ></i></h2></header>
    <body>
        <form action="" method="post">
            <div class="rogin-title"><h1>ログイン</h1></div>
            <div class="err">
                <?php
                 if($err){ echo "<h3>※ユーザー名またはパスワードが違います</h3>";}
                ?></div>
            <div class="a">
                <a href="register.php"><span class="fa-solid fa-angle-right"></span>新規会員登録はこちら</a>
            </div>
            <div class="rogin-box">
                <div class="index">
                    <p>ユーザーネーム</p>
                    <input type="text" name="username" placeholder="ユーザーネームを入力">
                </div>
                <div class="index">
                    <p>パスワード</p>
                    <input type="password" name="password" placeholder="パスワードを入力">
                </div>
                <div class="button-box">
                    <p></p>
                    <button type="submit">ログイン</button>
                </div>
            </div>
        </form>
    </body>
</html>
    