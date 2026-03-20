<?php
require 'config.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $entry_id = intval($_POST['id']);
    $month = $_POST['month'] ?? date('Y-m');
    
    // 执行删除操作，严格加上 user_id = ? 的限制，防止越权删除别人的数据（安全最佳实践）
    $stmt = $pdo->prepare("DELETE FROM work_hours WHERE id = ? AND user_id = ?");
    $stmt->execute([$entry_id, $_SESSION['user_id']]);
    
    // 删除后跳转回之前的月份视图
    header("Location: index.php?month=" . urlencode($month));
    exit;
}
?>