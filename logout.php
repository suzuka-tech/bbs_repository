<?php
session_start();

// セッション変数をすべて削除
$_SESSION = [];

// セッション自体を破棄
session_destroy();

header("Location: all_thread.php");
exit;
?>
