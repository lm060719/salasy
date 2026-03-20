<?php
require 'config.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO work_hours (user_id, work_date, hours, multiplier, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['work_date'],
        $_POST['hours'],
        $_POST['multiplier'],
        $_POST['notes']
    ]);
    header("Location: index.php");
}
?>