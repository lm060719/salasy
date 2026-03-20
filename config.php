<?php
session_start();

$db_host = 'localhost';
//端口
$db_port = '0000'; 
$db_name = 'gongshi'; 
$db_user = 'gongshi'; 
$db_pass = '3adwad4'; 

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 检查登录状态的快捷函数
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}
?>