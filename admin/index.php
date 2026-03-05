<?php
session_start();
include '../db/config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Stats Calculation: Everything not Completed or Cancelled is considered "In Progress / Pending"
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) t FROM bookings"))['t'] ?? 0;
$pending = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) p FROM bookings WHERE status NOT IN ('Completed', 'Cancelled')"))['p'] ?? 0;
$completed = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM bookings WHERE status='Completed'"))['c'] ?? 0;

// Revenue Calculations
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(amount) s FROM bookings WHERE status='Completed'"))['s'] ?? 0;
$today_revenue = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(amount) s FROM bookings WHERE DATE(created_at) = CURDATE() AND status='Completed'"))['s'] ?? 0;

// Last 7 days booking and revenue trend
$dates = [];
$counts = [];
$revenues = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $display_date = date('d M', strtotime($date));
    $dates[] = $display_date;
    
    // Booking counts
    $q_count = mysqli_query($conn, "SELECT COUNT(*) c FROM bookings WHERE DATE(created_at) = '$date'");
    $counts[] = mysqli_fetch_assoc($q_count)['c'] ?? 0;
    
    // Revenue counts
    $q_rev = mysqli_query($conn, "SELECT SUM(amount) s FROM bookings WHERE DATE(created_at) = '$date' AND status='Completed'");
    $revenues[] = mysqli_fetch_assoc($q_rev)['s'] ?? 0;
}
$dates_json = json_encode($dates);
$counts_json = json_encode($counts);
$revenues_json = json_encode($revenues);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | MyLab</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary: #0d6efd;
            --bg-body: #f4f7fe;
            --sidebar-bg: #1e293b;
            --white: #ffffff;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            margin: 0;
            display: flex;
            color: #1e293b;
        }

        /* Sidebar layout is now managed by sidebar.php component */
        body { background: #f8fafc; font-family: 'Inter', sans-serif; }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        header h2 { margin: 0; font-size: 24px; font-weight: 700; }

        /* --- STATS CARDS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.02);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .icon-revenue { background: #dcfce7; color: #15803d; }
        .icon-today { background: #f0f9ff; color: #0369a1; }

        .stat-info h3 { margin: 0; font-size: 13px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-info h2 { margin: 5px 0 0 0; font-size: 30px; font-weight: 800; }

        .financial-title { font-size: 14px; font-weight: 800; color: #1e3a8a; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .financial-title span { width: 30px; h-0.5 bg-blue-100; flex-grow: 1; }

        /* --- TOOLBAR (Search & Export) --- */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            gap: 20px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 500px; /* Limits search width to prevent overlap */
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            outline: none;
            background: #f8fafc;
            font-size: 14px;
            transition: 0.3s;
        }

        .search-box input:focus { border-color: var(--primary); background: white; }

        .search-box i { position: absolute; left: 18px; top: 15px; color: #94a3b8; }

        .action-btns { display: flex; gap: 10px; }

        .btn-export {
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: white;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-export:hover { background: #f1f5f9; border-color: #cbd5e1; }

        /* --- TABLE DESIGN --- */
        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 18px 20px; text-align: left; font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }
        td { padding: 18px 20px; border-top: 1px solid #f1f5f9; font-size: 14px; }
        tr:hover { background-color: #fcfdfe; }

        .badge { padding: 6px 12px; border-radius: 30px; font-size: 11px; font-weight: 700; }
        .badge-pending { background: #fff7ed; color: #c2410c; }
        .badge-done { background: #f0fdf4; color: #15803d; }

        .action-icon {
            padding: 8px;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            margin-right: 5px;
            transition: 0.2s;
        }
        .btn-sync { background: #e0f2fe; color: #0284c7; }
        .btn-del { background: #fee2e2; color: #dc2626; }
        .action-icon:hover { opacity: 0.8; transform: scale(1.1); }

        /* --- CHARTS SECTION --- */
        .charts-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 35px;
        }

        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        }

        .chart-header {
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
        }

        @media (max-width: 1024px) {
            .charts-container { grid-template-columns: 1fr; }
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h2>Booking Dashboard</h2>
            <div style="color: var(--text-muted); font-weight: 500;">
                <i class="far fa-calendar-alt"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </header>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-total"><i class="fas fa-folder-open"></i></div>
                <div class="stat-info">
                    <h3>Total Bookings</h3>
                    <h2><?php echo $total; ?></h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-pending"><i class="fas fa-spinner"></i></div>
                <div class="stat-info">
                    <h3>Pending</h3>
                    <h2><?php echo $pending; ?></h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-done"><i class="fas fa-check-double"></i></div>
                <div class="stat-info">
                    <h3>Completed</h3>
                    <h2><?php echo $completed; ?></h2>
                </div>
            </div>
        </div>

        <div class="financial-title"><i class="fas fa-chart-pie"></i> Financial Analytics <span></span></div>
        
        <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 35px;">
            <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border: 1px solid #dcfce7;">
                <div class="stat-icon icon-revenue"><i class="fas fa-indian-rupee-sign"></i></div>
                <div class="stat-info">
                    <h3>Total Revenue (Completed)</h3>
                    <h2>₹<?php echo number_format($total_revenue); ?></h2>
                </div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%); border: 1px solid #e0f2fe;">
                <div class="stat-icon icon-today"><i class="fas fa-coins"></i></div>
                <div class="stat-info">
                    <h3>Today's Earnings</h3>
                    <h2>₹<?php echo number_format($today_revenue); ?></h2>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-container">
            <!-- Weekly Trend Chart -->
            <div class="chart-card">
                <div class="chart-header">Bookings & Revenue Trend</div>
                <div style="height: 300px; width: 100%;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            
            <!-- Status Doughnut Chart -->
            <div class="chart-card">
                <div class="chart-header">Status Distribution</div>
                <div style="height: 300px; width: 100%; display: flex; justify-content: center;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Toolbar: Search + Export Buttons Fix -->
        <div class="toolbar">
            <form class="search-box" method="GET">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search by name, mobile or test..." value="<?php echo @$_GET['search']; ?>">
            </form>
            <div class="action-btns">
                <a href="export.php" class="btn-export"><i class="fas fa-file-csv"></i> CSV</a>
                <a href="pdf.php" class="btn-export"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient Details</th>
                        <th>Test Name</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if(isset($_GET['search']) && !empty($_GET['search'])){
                        $s = mysqli_real_escape_string($conn, $_GET['search']);
                        $search_query = " WHERE name LIKE '%$s%' OR mobile LIKE '%$s%' OR test LIKE '%$s%' OR email LIKE '%$s%'";
                    }
                    $q = mysqli_query($conn,"SELECT * FROM bookings $search_query ORDER BY id DESC");

                    $i = 1;
                    while($row = mysqli_fetch_assoc($q)){
                        $stClass = ($row['status']=='Completed') ? 'badge-done' : 'badge-pending';
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <div style="font-weight:600;"><?php echo $row['name']; ?></div>
                            <small style="color: var(--text-muted);"><?php echo $row['mobile']; ?></small>
                        </td>
                        <td><?php echo $row['test']; ?></td>
                        <td><?php echo date('d M, Y', strtotime($row['created_at'])); ?></td>
                        <td class="font-bold text-gray-800">₹<?php echo number_format($row['amount'], 2); ?></td>
                        <td>
                            <?php 
                                $payLabel = $row['payment_method'] ?? 'Online';
                                $payClass = ($row['payment_status'] == 'Success') ? 'text-green-600' : 'text-amber-500';
                            ?>
                            <div class="font-bold <?php echo $payClass; ?>"><?php echo $payLabel; ?></div>
                            <small class="text-xs text-slate-400 capitalize"><?php echo $row['payment_status']; ?></small>
                        </td>
                        <td>
                            <select onchange="updateStatus(<?php echo $row['id']; ?>, this.value)" class="px-2 py-1 rounded-lg text-[11px] font-bold uppercase border outline-none cursor-pointer transition-all
                                <?php 
                                    $s = strtolower($row['status']);
                                    echo ($s == 'completed') ? 'bg-green-50 text-green-700 border-green-200' : 
                                         (($s == 'cancelled') ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200');
                                ?>">
                                <option value="Booked" <?php if($row['status']=='Booked') echo 'selected'; ?>>Booked</option>
                                <option value="Collected" <?php if($row['status']=='Collected') echo 'selected'; ?>>Collected</option>
                                <option value="In Lab" <?php if($row['status']=='In Lab') echo 'selected'; ?>>In Lab</option>
                                <option value="Completed" <?php if($row['status']=='Completed') echo 'selected'; ?>>Completed</option>
                                <option value="Cancelled" <?php if($row['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                        </td>
                        <td class="whitespace-nowrap px-6">
                            <div class="flex items-center gap-3">
                                <!-- VIEW REPORT (Premium Style) -->
                                <?php if(!empty($row['report_file'])): ?>
                                    <a href="../uploads/reports/<?php echo $row['report_file']; ?>" target="_blank" title="View Report PDF" 
                                       class="w-10 h-10 flex items-center justify-center bg-white border border-emerald-100 text-emerald-500 rounded-2xl shadow-sm hover:bg-emerald-500 hover:text-white hover:shadow-lg hover:shadow-emerald-200 transition-all duration-300">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                <?php else: ?>
                                    <div title="Report not available" class="w-10 h-10 flex items-center justify-center bg-gray-50 border border-gray-100 text-gray-300 rounded-2xl cursor-not-allowed">
                                        <i class="fas fa-eye-slash text-sm"></i>
                                    </div>
                                <?php endif; ?>

                                <!-- UPLOAD REPORT (Premium Style) -->
                                <button onclick="openUploadModal(<?php echo $row['id']; ?>)" title="<?php echo $row['report_file'] ? 'Change Report' : 'Upload Report'; ?>" 
                                        class="w-10 h-10 flex items-center justify-center bg-white border border-blue-100 text-blue-500 rounded-2xl shadow-sm hover:bg-blue-600 hover:text-white hover:shadow-lg hover:shadow-blue-200 transition-all duration-300">
                                    <i class="fas fa-cloud-arrow-up text-sm"></i>
                                </button>

                                <!-- DELETE BOOKING (Premium Style) -->
                                <a onclick="return confirm('Permanently delete this booking?')" title="Delete Booking" 
                                   class="w-10 h-10 flex items-center justify-center bg-white border border-red-50 text-red-400 rounded-2xl shadow-sm hover:bg-red-500 hover:text-white hover:shadow-lg hover:shadow-red-100 transition-all duration-300" 
                                   href="delete.php?id=<?php echo $row['id']; ?>">
                                    <i class="fas fa-trash-can text-sm"></i>
                                </a>

                                <!-- PRESCRIPTION (If exists) -->
                                <?php if(!empty($row['prescription_file'])): ?>
                                    <div class="w-px h-6 bg-gray-100 mx-1"></div> <!-- Separator -->
                                    <a title="View Prescription" class="w-10 h-10 flex items-center justify-center bg-amber-50 text-amber-600 rounded-2xl border border-amber-100 hover:bg-amber-600 hover:text-white transition-all shadow-sm" href="../uploads/prescriptions/<?php echo $row['prescription_file']; ?>" target="_blank">
                                        <i class="fas fa-prescription text-xs"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Initialize Charts -->
    <script>
        // Trend Chart Data
        const dates = <?php echo $dates_json; ?>;
        const counts = <?php echo $counts_json; ?>;
        const revenues = <?php echo $revenues_json; ?>;

        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Bookings',
                        data: counts,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.05)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue (₹)',
                        data: revenues,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true } }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: { callback: value => '₹' + value }
                    }
                }
            }
        });

        // Status Doughnut Chart Data
        const pendingCount = <?php echo $pending; ?>;
        const completedCount = <?php echo $completed; ?>;

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Completed'],
                datasets: [{
                    data: [pendingCount, completedCount],
                    backgroundColor: ['#f59e0b', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 8, padding: 20 }
                    }
                }
            }
        });

        // Report Upload Modal Logic
        function openUploadModal(id) {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = '.pdf,.jpg,.jpeg,.png';
            fileInput.onchange = e => {
                const file = e.target.files[0];
                const formData = new FormData();
                formData.append('report', file);
                formData.append('booking_id', id);

                fetch('upload_report_ajax.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(data => {
                    if(data.trim() === 'Success') {
                        alert('Report Uploaded Successfully!');
                        location.reload();
                    } else {
                        alert('Upload failed: ' + data);
                    }
                });
            };
            fileInput.click();
        }

        // AJAX Status Update
        function updateStatus(id, status) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', status);

            fetch('update_status_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === 'Success') {
                    // Quick refresh or visual cue
                    location.reload(); 
                } else {
                    alert('Error updating status');
                }
            });
        }
    </script>
</body>
</html>