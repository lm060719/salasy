<?php
// 开启 session
session_start();

// 清空所有的 session 变量
$_SESSION = array();

// 如果你使用了基于 cookie 的 session，顺便销毁掉客户端的 session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 彻底销毁 session
session_destroy();

// 重定向到登录页面
header("Location: login.php");
exit;
?>