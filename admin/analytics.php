<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Ensure users has created_at
mysqli_query($conn, "ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

// Analytics Data Fetching
$year = date('Y');
$month = date('m');

// 1. Monthly Revenue for Charts
$revenue_q = mysqli_query($conn, "SELECT MONTH(booking_date) as month, SUM(amount) as total 
                                  FROM bookings 
                                  WHERE YEAR(booking_date) = '$year' AND payment_status = 'Completed'
                                  GROUP BY MONTH(booking_date)");
$monthly_revenue = array_fill(1, 12, 0);
while($row = mysqli_fetch_assoc($revenue_q)) {
    $monthly_revenue[(int)$row['month']] = (float)$row['total'];
}

// 2. Top Selling Packages
$top_packages = mysqli_query($conn, "SELECT test, COUNT(*) as count, SUM(amount) as revenue
                                     FROM bookings 
                                     GROUP BY test 
                                     ORDER BY count DESC LIMIT 6");

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
    <title>Enterprise Insights | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0f2f5; color: #1e293b; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .card-shadow { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02); }
        .chart-container { position: relative; height: 300px; width: 100%; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <!-- Premium Header Area -->
        <div class="mb-12 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
            <div class="space-y-2">
                <div class="flex items-center gap-3 mb-1">
                    <span class="bg-blue-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">Live Intel</span>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">v4.2 Enterprise Analytics</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">Business <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Intelligence</span></h1>
                <p class="text-slate-500 font-medium text-lg">Comprehensive financial performance and kinetic growth metrics.</p>
            </div>
            
            <div class="flex gap-4">
                <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Current Fiscal</div>
                        <div class="font-black text-slate-800"><?php echo date('F Y'); ?></div>
                    </div>
                </div>
                <button onclick="window.print()" class="bg-slate-900 text-white px-8 py-4 rounded-[2rem] font-black hover:bg-blue-600 transition-all transform hover:-translate-y-1 active:scale-95 shadow-xl shadow-slate-200 flex items-center gap-3">
                    <i class="fas fa-cloud-download-alt"></i> Intelligence Report
                </button>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <?php 
                $total_rev = array_sum($monthly_revenue);
                $total_pts = array_sum($monthly_users);
                $curr_month_rev = $monthly_revenue[(int)date('m')];
            ?>
            <div class="glass p-8 rounded-[2.5rem] card-shadow">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-emerald-200">
                        <i class="fas fa-indian-rupee-sign"></i>
                    </div>
                    <span class="text-emerald-500 font-black text-xs bg-emerald-50 px-2 py-1 rounded-lg">+14.2%</span>
                </div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Gross Annual Revenue</div>
                <div class="text-3xl font-black text-slate-900">₹<?php echo number_format($total_rev, 0); ?></div>
            </div>

            <div class="glass p-8 rounded-[2.5rem] card-shadow text-white relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-blue-700 z-0"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-xl">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="text-white/60 font-black text-xs uppercase tracking-widest">Active Velocity</span>
                    </div>
                    <div class="text-[10px] font-black text-white/60 uppercase tracking-widest mb-1">Monthly Peak Revenue</div>
                    <div class="text-3xl font-black">₹<?php echo number_format(max($monthly_revenue), 0); ?></div>
                </div>
            </div>

            <div class="glass p-8 rounded-[2.5rem] card-shadow">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-blue-200">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="text-blue-600 font-black text-xs bg-blue-50 px-2 py-1 rounded-lg">All Time</span>
                </div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Patient Base</div>
                <div class="text-3xl font-black text-slate-900"><?php echo number_format($total_pts); ?></div>
            </div>

            <div class="glass p-8 rounded-[2.5rem] card-shadow bg-slate-900 border-none text-white">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full border-2 border-slate-900 bg-blue-500 flex items-center justify-center text-[10px] font-bold">AS</div>
                        <div class="w-8 h-8 rounded-full border-2 border-slate-900 bg-emerald-500 flex items-center justify-center text-[10px] font-bold">RK</div>
                    </div>
                </div>
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">System Load Ratio</div>
                <div class="text-3xl font-black">Optimal</div>
            </div>
        </div>

        <!-- Visual Analytics Layer -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-10">
            <!-- Performance Chart -->
            <div class="xl:col-span-2 bg-white p-10 rounded-[3rem] card-shadow border border-slate-50">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                    <div>
                        <h3 class="font-black text-xl text-slate-900">Performance Quotient</h3>
                        <p class="text-slate-400 text-sm font-medium">Tracking revenue volatility throughout <?php echo $year; ?></p>
                    </div>
                    <div class="flex gap-2 p-1 bg-slate-50 rounded-2xl border border-slate-100">
                        <button class="bg-white px-4 py-2 rounded-xl text-xs font-black text-blue-600 shadow-sm">Monthly</button>
                        <button class="px-4 py-2 rounded-xl text-xs font-black text-slate-400 hover:text-slate-600">Quarterly</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Best Sellers -->
            <div class="bg-white p-10 rounded-[3rem] card-shadow border border-slate-50 flex flex-col">
                <h3 class="font-black text-xl text-slate-900 mb-8">Asset Liquidity <span class="text-blue-600">(Tests)</span></h3>
                <div class="space-y-6 overflow-y-auto pr-2 max-h-[350px]">
                    <?php while($pkg = mysqli_fetch_assoc($top_packages)): ?>
                    <div class="group flex items-center justify-between p-4 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 text-sm truncate max-w-[120px]"><?php echo $pkg['test']; ?></h4>
                                <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-0.5">₹<?php echo number_format($pkg['revenue']); ?> Generated</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-black text-slate-900"><?php echo $pkg['count']; ?></div>
                            <div class="text-[9px] text-slate-400 font-bold uppercase">Orders</div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="mt-auto pt-8 border-t border-slate-50">
                    <a href="packages.php" class="w-full bg-slate-50 text-slate-600 py-4 rounded-2xl font-black text-xs hover:bg-slate-100 transition-all block text-center">Catalogue Audit</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Strategic Distribution -->
            <div class="bg-slate-900 p-10 rounded-[3rem] card-shadow text-white">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="font-black text-xl">Service Integrity Distribution</h3>
                    <i class="fas fa-ellipsis-v text-slate-600"></i>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-12">
                    <div class="w-48 h-48 relative shrink-0">
                        <canvas id="distributionChart"></canvas>
                    </div>
                    <div class="flex-grow space-y-4">
                        <?php 
                        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#ef4444'];
                        foreach($status_labels as $idx => $label): 
                        ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full" style="background: <?php echo $colors[$idx % count($colors)]; ?>"></div>
                                    <span class="text-xs font-bold text-slate-400"><?php echo $label; ?></span>
                                </div>
                                <span class="text-sm font-black"><?php echo $status_counts[$idx]; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Kinetic Growth Bar -->
            <div class="bg-white p-10 rounded-[3rem] card-shadow border border-slate-50">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="font-black text-xl text-slate-900">Kinetic Growth <span class="text-slate-400 text-sm font-medium ml-2">(Patients)</span></h3>
                    <div class="text-xs font-black text-indigo-600">Yearly Target: 85%</div>
                </div>
                <div class="chart-container" style="height: 200px;">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global Config
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#94a3b8';

        // 1. Performance Chart (Line)
        const ctxPerf = document.getElementById('performanceChart').getContext('2d');
        const gradientPerf = ctxPerf.createLinearGradient(0, 0, 0, 300);
        gradientPerf.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
        gradientPerf.addColorStop(1, 'rgba(59, 130, 246, 0)');

        new Chart(ctxPerf, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: [<?php echo implode(',', $monthly_revenue); ?>],
                    borderColor: '#3b82f6',
                    backgroundColor: gradientPerf,
                    borderWidth: 5,
                    fill: true,
                    tension: 0.5,
                    pointRadius: 0,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#3b82f6',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 15,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 14 },
                        displayColors: false,
                        cornerRadius: 12
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        border: { display: false },
                        grid: { color: 'rgba(234, 236, 240, 0.5)' }
                    },
                    x: { border: { display: false }, grid: { display: false } }
                }
            }
        });

        // 2. Distribution (Doughnut)
        new Chart(document.getElementById('distributionChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($status_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($status_counts); ?>,
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#ef4444'],
                    borderWidth: 0,
                    cutout: '80%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // 3. Growth Chart (Bar)
        new Chart(document.getElementById('growthChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    data: [<?php echo implode(',', $monthly_users); ?>],
                    backgroundColor: '#6366f1',
                    borderRadius: 12,
                    barThickness: 12
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
