<?php
session_start();
require 'bbs_db.php';//PDO接続

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $username = trim($_POST['username']);
    $rawPassword = $_POST['password'];
    $email = trim($_POST['email']);
    $checkpassword =  $_POST['checkpassword'];

    $checkerr = false;
    $nameerr = false;
    $emailerr = false;
    $pwerr = false;

    if (strlen ( $username ) < 4 || strlen($username) > 12) {
		$nameerr = true;
	}
    if(strlen($email)<4){
        $emailerr = true;
    }
    if(strlen($rawPassword)<4 || strlen($rawPassword)>16){
        $pwerr = true;
    }
    if(!($rawPassword === $checkpassword)){
        $checkerr = true;
    }


    if (!$nameerr && !$emailerr && !$pwerr && !$checkerr) {
        $password = password_hash($rawPassword, PASSWORD_DEFAULT); // ハッシュ化

        try{
            $stmt = $pdo->prepare("INSERT INTO users (username,email,password) VALUES (:username, :email, :password)");
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password, PDO::PARAM_STR);
            $stmt->execute();

            $user_id = $pdo->lastInsertId();

            $_SESSION['user'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['flash'] = "登録完了！";
            header("Location: login.php");
            exit;
        }catch(PDOException $e){
            echo "登録エラー: " . $e->getMessage();
        }

    }
    
    
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>ユーザー会員登録</title>
        <link rel="stylesheet" href="register_style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    </head>
    <header><h2>簡易掲示板<i class="fa-solid fa-comments"></i></h2></header>
    <body>
        <form action="" method="post">
            <div class="register-title"><h1>ユーザー会員登録</h1></div>
            <div class="err">
                <?php
                 if($nameerr || $emailerr || $pwerr){ echo "<h3>※内容に間違いがあります</h3>";}
                ?></div>
            <div class="register-box">
                <p>ユーザーネーム</p>
                <input type="text" name="username" placeholder="ユーザーネームを入力">
                <p>※4-12文字の半角英字が利用できます。</p>
                <div class="err">
                <?php
                 if($nameerr){ echo "※4-12文字の半角英字で入力してください";}
                ?></div>
            </div>
            <div class="register-box">
                <p>メールアドレス</p>
                <input type="text" name="email" placeholder="例) kaiin@bbs.jp">
                <div class="err">
                <?php
                 if($emailerr){ echo "※正しいメールアドレスを入力してください";}
                ?></div>
            </div>
            <div class="register-box">
                <p>パスワード</p>
                <input type="password" name="password" placeholder="パスワードを入力">
                <p>※4-16文字の半角英数字が利用できます。</p>
                <div class="err">
                <?php
                 if($pwerr){ echo "※4-12文字の半角英数字で入力してください";}
                ?></div>
            </div>
            <div class="register-box">
                <p>パスワード確認</p>
                <input type="password" name="checkpassword" placeholder="もう一度入力してください">
                <div class="err">
                <?php
                 if($checkerr){ echo "※パスワードが異なります<br>";}
                 if($pwerr){ echo "※4-12文字で入力してください";}
                ?></div>
            </div>
            <div class="button-box">
                <p>内容をもう一度確認の上、間違いがなければ「上記内容で登録」を押してください。</p>
                <button type="submit">上記内容で登録</button>
            </div>
        </form>
    </body>
</html>
    