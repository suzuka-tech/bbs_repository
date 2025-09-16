<?php
define('HOST','localhost');
define('DB_NAME','bbs_db');
define('USERNAME','root');
define('PASSWORD','root');
define('PORT','3307');

try {
    $pdo = new PDO("mysql:host=".HOST.";port=".PORT.";dbname=".DB_NAME,USERNAME,PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB接続エラー: ".$e->getMessage());
}
?>
