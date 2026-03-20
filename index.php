<?php
require 'config.php';
checkAuth();

$user_id = $_SESSION['user_id'];
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// 处理时薪设置
if (isset($_POST['update_wage'])) {
    $new_wage = floatval($_POST['hourly_wage']);
    $stmt = $pdo->prepare("UPDATE users SET hourly_wage = ? WHERE id = ?");
    $stmt->execute([$new_wage, $user_id]);
    $_SESSION['hourly_wage'] = $new_wage;
}

// 获取本月数据
$stmt = $pdo->prepare("
    SELECT * FROM work_hours 
    WHERE user_id = ? AND DATE_FORMAT(work_date, '%Y-%m') = ?
    ORDER BY work_date ASC
");
$stmt->execute([$user_id, $month]);
$records = $stmt->fetchAll();

$total_hours = 0;
$total_salary = 0;
$chart_data = [];

// 初始化本月图表数据 (30天/当月天数)
$days_in_month = date('t', strtotime($month . '-01'));
for($i=1; $i<=$days_in_month; $i++) {
    $date_str = $month . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
    $chart_data[$date_str] = 0;
}

foreach ($records as $row) {
    $total_hours += $row['hours'];
    $daily_salary = $row['hours'] * $_SESSION['hourly_wage'] * $row['multiplier'];
    $total_salary += $daily_salary;
    $chart_data[$row['work_date']] += $row['hours'];
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>工时仪表盘</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .text-primary { color: #0d6efd !important; }
        .text-success { color: #198754 !important; }
        /* 图表容器，确保在手机上有固定的最小高度 */
        .chart-container { position: relative; height: 300px; width: 100%; }
        /* 表格文字在手机上稍微缩小一点防止折行太严重 */
        @media (max-width: 768px) {
            .table-responsive { font-size: 0.9rem; }
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <h3 class="mb-0">👋 欢迎，<?= htmlspecialchars($_SESSION['username']) ?></h3>
        <div class="d-flex flex-wrap gap-2">
            <form class="d-inline-block flex-grow-1 flex-md-grow-0" method="GET">
                <input type="month" name="month" value="<?= $month ?>" class="form-control w-100" onchange="this.form.submit()">
            </form>
            <a href="export.php?month=<?= $month ?>" class="btn btn-success flex-grow-1 flex-md-grow-0">导出CSV</a>
            <a href="logout.php" class="btn btn-outline-danger flex-grow-1 flex-md-grow-0">退出</a>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card p-3 p-md-4 text-center h-100">
                <h6 class="text-muted">本月累计工时</h6>
                <h2 class="text-primary mb-0"><?= $total_hours ?> <small class="fs-6">小时</small></h2>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card p-3 p-md-4 text-center h-100">
                <h6 class="text-muted">本月预计薪资</h6>
                <h2 class="text-success mb-0">¥ <?= number_format($total_salary, 2) ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 p-md-4 h-100 d-flex justify-content-center">
                <form method="POST">
                    <label class="form-label text-muted" style="font-size: 0.9rem;">当前基础时薪 (¥)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="hourly_wage" class="form-control" value="<?= $_SESSION['hourly_wage'] ?>">
                        <button type="submit" name="update_wage" class="btn btn-outline-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card p-3 p-md-4">
                <h5 class="mb-3">➕ 录入工时</h5>
                <form action="add_entry.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">日期</label>
                        <input type="date" name="work_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">工作时长 (小时)</label>
                        <input type="number" step="0.5" name="hours" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">加班倍率</label>
                        <select name="multiplier" class="form-select">
                            <option value="1.0">1.0x (正常)</option>
                            <option value="1.5">1.5x (周末)</option>
                            <option value="2.0">2.0x (节假日)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">备注 (可选)</label>
                        <input type="text" name="notes" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">保存记录</button>
                </form>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card p-3 p-md-4">
                <h5 class="mb-3">📈 本月工时走势</h5>
                <div class="chart-container">
                    <canvas id="hoursChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 mb-4">
            <div class="card p-3 p-md-4">
                <h5 class="mb-3">📋 本月工时明细</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>日期</th>
                                <th>工时</th>
                                <th>倍率</th>
                                <th>当日薪资</th>
                                <th>备注</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">本月暂无工时记录</td></tr>
                            <?php else: ?>
                                <?php foreach ($records as $row): ?>
                                    <?php $daily_salary = $row['hours'] * $_SESSION['hourly_wage'] * $row['multiplier']; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['work_date']) ?></td>
                                        <td><span class="badge bg-primary rounded-pill"><?= $row['hours'] ?> h</span></td>
                                        <td><?= $row['multiplier'] ?>x</td>
                                        <td class="text-success">¥ <?= number_format($daily_salary, 2) ?></td>
                                        <td class="text-muted text-truncate" style="max-width: 150px;"><?= htmlspecialchars($row['notes']) ?></td>
                                        <td>
                                            <form action="delete_entry.php" method="POST" onsubmit="return confirm('确定要删除这条记录吗？');" style="margin: 0;">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="month" value="<?= $month ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('hoursChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_keys($chart_data)) ?>,
        datasets: [{
            label: '每日工时',
            data: <?= json_encode(array_values($chart_data)) ?>,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointRadius: 3
        }]
    },
    options: { 
        responsive: true,
        maintainAspectRatio: false, /* 关键：允许图表在手机上填满指定的容器高度 */
        plugins: {
            legend: { display: false } /* 手机上隐藏图例节省空间 */
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
</body>
</html>