<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Analytics Data Fetching
$year = date('Y');

// 1. Monthly Revenue
$revenue_q = mysqli_query($conn, "SELECT MONTH(created_at) as month, SUM(amount) as total 
                                  FROM bookings 
                                  WHERE YEAR(created_at) = '$year' AND status = 'Completed'
                                  GROUP BY MONTH(created_at)");
$monthly_revenue = array_fill(1, 12, 0);
while($row = mysqli_fetch_assoc($revenue_q)) {
    $monthly_revenue[(int)$row['month']] = (float)$row['total'];
}

// 2. Top Selling Packages
$top_packages = mysqli_query($conn, "SELECT test, COUNT(*) as count, SUM(amount) as revenue
                                     FROM bookings 
                                     GROUP BY test 
                                     ORDER BY count DESC LIMIT 5");

// 3. User Growth
$user_growth = mysqli_query($conn, "SELECT MONTH(created_at) as month, COUNT(*) as count 
                                     FROM users 
                                     WHERE YEAR(created_at) = '$year'
                                     GROUP BY MONTH(created_at)");
$monthly_users = array_fill(1, 12, 0);
while($row = mysqli_fetch_assoc($user_growth)) {
    $monthly_users[(int)$row['month']] = (int)$row['count'];
}

// 4. Booking Status Distribution
$status_dist = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
$status_labels = [];
$status_counts = [];
while($row = mysqli_fetch_assoc($status_dist)) {
    $status_labels[] = $row['status'];
    $status_counts[] = (int)$row['count'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Insights | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; }
        .stat-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .chart-container { position: relative; height: 300px; width: 100%; }
        .main-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; }
    </style>
</head>
<body class="min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Business Insights</h1>
                <p class="text-slate-500 text-sm">Performance metrics and growth overview for <?php echo $year; ?></p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <div class="text-xs font-bold text-slate-400 uppercase">Current Month</div>
                    <div class="text-sm font-bold text-slate-800"><?php echo date('F Y'); ?></div>
                </div>
                <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-blue-700 transition-all flex items-center gap-2">
                    <i class="fas fa-download"></i> Export Report
                </button>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php 
                $total_rev = array_sum($monthly_revenue);
                $total_pts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users"))['c'];
                $avg_order = ($total_rev > 0) ? ($total_rev / max(1, array_sum($status_counts))) : 0;
            ?>
            <div class="stat-card">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Annual Revenue</div>
                        <div class="text-xl font-extrabold text-slate-900">₹<?php echo number_format($total_rev, 0); ?></div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Patients</div>
                        <div class="text-xl font-extrabold text-slate-900"><?php echo number_format($total_pts); ?></div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Avg. Order Value</div>
                        <div class="text-xl font-extrabold text-slate-900">₹<?php echo number_format($avg_order, 0); ?></div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Bookings</div>
                        <div class="text-xl font-extrabold text-slate-900"><?php echo array_sum($status_counts); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Revenue Chart -->
            <div class="lg:col-span-2 main-card">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-800">Revenue Performance</h3>
                    <div class="text-[10px] bg-slate-100 px-2 py-1 rounded font-bold text-slate-500 uppercase">Jan - Dec</div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Best Sellers -->
            <div class="main-card">
                <h3 class="font-bold text-slate-800 mb-6">Top Test Packages</h3>
                <div class="space-y-4">
                    <?php while($pkg = mysqli_fetch_assoc($top_packages)): ?>
                    <div class="flex items-center justify-between p-3 rounded-xl border border-slate-50 bg-slate-50/50">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-8 h-8 bg-white text-blue-600 rounded-lg flex items-center justify-center text-xs shadow-sm border border-slate-100 flex-shrink-0">
                                <i class="fas fa-microscope"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h4 class="font-bold text-slate-800 text-sm truncate"><?php echo $pkg['test']; ?></h4>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">₹<?php echo number_format($pkg['revenue']); ?> Rev.</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-slate-900"><?php echo $pkg['count']; ?></div>
                            <div class="text-[9px] text-slate-400 font-bold uppercase">Sales</div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <a href="packages.php" class="mt-6 w-full py-3 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-all block text-center">View All Packages</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Status Dist -->
            <div class="main-card">
                <h3 class="font-bold text-slate-800 mb-6 uppercase text-[11px] tracking-widest text-slate-400">Booking Status Mix</h3>
                <div class="flex items-center gap-8">
                    <div class="w-40 h-40">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="flex-grow space-y-3">
                        <?php 
                        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#ef4444'];
                        foreach($status_labels as $idx => $label): 
                        ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" style="background: <?php echo $colors[$idx % count($colors)]; ?>"></span>
                                    <span class="text-xs font-medium text-slate-600"><?php echo $label; ?></span>
                                </div>
                                <span class="text-xs font-bold text-slate-900"><?php echo $status_counts[$idx]; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- User Growth -->
            <div class="main-card">
                <h3 class="font-bold text-slate-800 mb-6 uppercase text-[11px] tracking-widest text-slate-400">Patient Growth</h3>
                <div class="chart-container" style="height: 200px;">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#94a3b8';

        // Revenue Chart
        new Chart(document.getElementById('revenueChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: [<?php echo implode(',', $monthly_revenue); ?>],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9' } },
                    x: { border: { display: false }, grid: { display: false } }
                }
            }
        });

        // Status Chart
        new Chart(document.getElementById('statusChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($status_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($status_counts); ?>,
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#ef4444'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // Growth Chart
        new Chart(document.getElementById('growthChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    data: [<?php echo implode(',', $monthly_users); ?>],
                    backgroundColor: '#6366f1',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false },
                    x: { border: { display: false }, grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>
