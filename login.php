<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($_POST['action'] == 'login') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['hourly_wage'] = $user['hourly_wage'];
            header("Location: index.php");
            exit;
        } else {
            $error = "用户名或密码错误！";
        }
    } elseif ($_POST['action'] == 'register') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            $success = "注册成功，请登录！";
        } catch (Exception $e) {
            $error = "用户名已被注册！";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>登录/注册 - 工时统计系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* 增加了 padding: 15px，防止手机端卡片贴边 */
        body { background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 15px; }
        .auth-card { width: 100%; max-width: 400px; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: #fff; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h3 class="text-center mb-4 text-primary">工时与薪资系统</h3>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label>用户名</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>密码</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" name="action" value="login" class="btn btn-primary">登录</button>
                <button type="submit" name="action" value="register" class="btn btn-outline-secondary">注册</button>
            </div>
        </form>
    </div>
</body>
</html>