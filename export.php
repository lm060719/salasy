<?php
require 'config.php';
checkAuth();

$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$stmt = $pdo->prepare("SELECT work_date, hours, multiplier, notes FROM work_hours WHERE user_id = ? AND DATE_FORMAT(work_date, '%Y-%m') = ? ORDER BY work_date ASC");
$stmt->execute([$_SESSION['user_id'], $month]);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Salary_Report_'.$month.'.csv');
$output = fopen('php://output', 'w');
// 解决 Excel 打开 CSV 乱码问题 (BOM头)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, ['日期', '工时', '倍率', '备注', '当日薪资预估']);

$hourly_wage = $_SESSION['hourly_wage'];

while ($row = $stmt->fetch()) {
    $daily_salary = $row['hours'] * $hourly_wage * $row['multiplier'];
    fputcsv($output, [
        $row['work_date'], 
        $row['hours'], 
        $row['multiplier'], 
        $row['notes'], 
        number_format($daily_salary, 2)
    ]);
}
fclose($output);
?>