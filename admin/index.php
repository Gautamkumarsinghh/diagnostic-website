<?php
session_start();
include '../db/config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Stats Calculation
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
    $q_count = mysqli_query($conn, "SELECT COUNT(*) c FROM bookings WHERE DATE(created_at) = '$date'");
    $counts[] = mysqli_fetch_assoc($q_count)['c'] ?? 0;
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
    <title>Booking Dashboard | MyLab Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fcfcfd;
            color: #1a1a1a;
            margin: 0;
            display: flex;
        }

        .main-content {
            margin-left: 280px; 
            padding: 2.5rem 3rem;
            min-height: 100vh;
            width: calc(100% - 280px);
        }

        @media (max-width: 1024px) {
            .main-content { margin-left: 90px; padding: 1.5rem; width: calc(100% - 90px); }
        }

        /* Simple Card Style */
        .page-card {
            background: #ffffff;
            border: 1px solid #eef0f2;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            padding: 1.5rem;
        }

        /* Stats Cards - Minimalist */
        .stat-card-simple {
            background: white;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .icon-blue { background: #eff6ff; color: #3b82f6; }
        .icon-amber { background: #fffbeb; color: #d97706; }
        .icon-emerald { background: #ecfdf5; color: #10b981; }

        /* Table Style - Simple & Clean */
        .clean-table {
            width: 100%;
            border-collapse: collapse;
        }

        .clean-table th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #eef0f2;
        }

        .clean-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        .clean-table tr:hover { background-color: #fcfdfe; }

        /* Action Buttons */
        .action-btn-mini {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.8rem;
            transition: all 0.2s;
            background: white;
        }
        .action-btn-mini:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #f0f7ff;
        }

        /* Search input */
        .search-input {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            font-size: 0.85rem;
            width: 100%;
            transition: all 0.2s;
        }
        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08);
            outline: none;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <!-- Minimal Header -->
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Booking Dashboard</h2>
                <p class="text-slate-500 text-sm font-medium mt-0.5">Real-time overview of your lab activity</p>
            </div>
            <div class="text-slate-400 font-semibold text-xs bg-white border border-slate-100 px-3 py-1.5 rounded-full shadow-sm">
                <i class="far fa-calendar-alt mr-1.5"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </header>

        <!-- Stats Grid (Simple) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card-simple">
                <div class="stat-icon-box icon-blue"><i class="fas fa-folder-open"></i></div>
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Bookings</div>
                    <div class="text-2xl font-black text-slate-800"><?php echo $total; ?></div>
                </div>
            </div>
            <div class="stat-card-simple">
                <div class="stat-icon-box icon-amber"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pending</div>
                    <div class="text-2xl font-black text-slate-800"><?php echo $pending; ?></div>
                </div>
            </div>
            <div class="stat-card-simple">
                <div class="stat-icon-box icon-emerald"><i class="fas fa-check-double"></i></div>
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Completed</div>
                    <div class="text-2xl font-black text-slate-800"><?php echo $completed; ?></div>
                </div>
            </div>
        </div>

        <!-- Financial Overview (Semi-Simple) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="page-card !bg-slate-900 !border-slate-800">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Revenue</div>
                <div class="text-3xl font-black text-white">₹<?php echo number_format($total_revenue); ?></div>
                <div class="mt-4 flex items-center text-xs text-emerald-400 font-bold">
                    <i class="fas fa-arrow-up mr-1 text-[10px]"></i> Lifetime Earnings
                </div>
            </div>
            <div class="page-card border-none bg-emerald-50">
                <div class="text-xs font-bold text-emerald-600 uppercase tracking-widest mb-1 uppercase">Today's Earnings</div>
                <div class="text-3xl font-black text-emerald-700">₹<?php echo number_format($today_revenue); ?></div>
                <div class="mt-4 text-[10px] text-emerald-600 font-black flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> LIVE CALCULATION
                </div>
            </div>
        </div>

        <!-- Charts Grid (Clean) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            <div class="page-card lg:col-span-2">
                <div class="font-bold text-slate-800 mb-6 text-sm flex items-center justify-between">
                    <span>Activity Trend</span>
                    <span class="text-[10px] font-black text-slate-300 uppercase letter-spacing-1">Last 7 Days</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            <div class="page-card">
                <div class="font-bold text-slate-800 mb-6 text-sm">Status Mix</div>
                <div style="height: 250px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <form class="relative w-full md:w-96" method="GET">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" placeholder="Search by name, mobile or test..." 
                       value="<?php echo @$_GET['search']; ?>" class="search-input">
            </form>
            <div class="flex items-center gap-2">
                <a href="export.php" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
                <a href="pdf.php" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="page-card !p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient / Contact</th>
                            <th>Test Description</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>UTR Number</th>
                            <th>Payment Proof</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
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
                        ?>
                        <tr>
                            <td class="text-slate-400 font-bold"><?php echo $i++; ?></td>
                            <td>
                                <div class="font-bold text-slate-800"><?php echo $row['name']; ?></div>
                                <div class="text-xs text-slate-500"><?php echo $row['mobile']; ?></div>
                            </td>
                            <td>
                                <span class="font-medium text-slate-700"><?php echo $row['test']; ?></span>
                            </td>
                            <td class="text-slate-600 text-xs">
                                <?php echo date('d M, Y', strtotime($row['created_at'])); ?>
                            </td>
                            <td class="font-bold text-slate-800">
                                ₹<?php echo number_format($row['amount'], 2); ?>
                            </td>
                            <td>
                                <div class="text-xs font-black text-blue-600"><?php echo $row['payment_method'] ?? 'Online'; ?></div>
                                <div class="text-[10px] uppercase font-bold text-slate-400"><?php echo $row['payment_status']; ?></div>
                            </td>
                            <td>
                                <?php if(!empty($row['utr_number'])): ?>
                                    <span class="text-xs font-mono font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md">
                                        <?php echo $row['utr_number']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-[10px] text-slate-300 italic">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($row['payment_proof'])): ?>
                                    <a href="../uploads/payments/<?php echo $row['payment_proof']; ?>" target="_blank" 
                                       class="text-blue-500 hover:text-blue-700 text-xs font-bold flex items-center gap-1">
                                        <i class="fas fa-receipt"></i> View Proof
                                    </a>
                                <?php else: ?>
                                    <span class="text-[10px] text-slate-300 italic">Not Uploaded</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <select onchange="updateStatus(<?php echo $row['id']; ?>, this.value)" 
                                        class="px-2 py-1.5 rounded-lg text-[10px] font-black uppercase border-none outline-none shadow-sm cursor-pointer
                                    <?php 
                                        $s = strtolower($row['status']);
                                        echo ($s == 'completed') ? 'bg-emerald-50 text-emerald-600' : 
                                             (($s == 'cancelled') ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-600');
                                    ?>">
                                    <option value="Booked" <?php if($row['status']=='Booked') echo 'selected'; ?>>Booked</option>
                                    <option value="Collected" <?php if($row['status']=='Collected') echo 'selected'; ?>>Collected</option>
                                    <option value="In Lab" <?php if($row['status']=='In Lab') echo 'selected'; ?>>In Lab</option>
                                    <option value="Completed" <?php if($row['status']=='Completed') echo 'selected'; ?>>Completed</option>
                                    <option value="Cancelled" <?php if($row['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <?php if(!empty($row['report_file'])): ?>
                                        <a href="../uploads/reports/<?php echo $row['report_file']; ?>" target="_blank" class="action-btn-mini" title="View Report"><i class="fas fa-eye"></i></a>
                                    <?php endif; ?>
                                    <button onclick="openUploadModal(<?php echo $row['id']; ?>)" class="action-btn-mini" title="Upload Report"><i class="fas fa-upload"></i></button>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete booking?')" class="action-btn-mini hover:!text-red-500 hover:!border-red-200" title="Delete"><i class="fas fa-trash-can"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Chart Initialization
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
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue',
                        data: revenues,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: {  display: false },
                    y1: { display: false }
                }
            }
        });

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Completed'],
                datasets: [{
                    data: [<?php echo $pending; ?>, <?php echo $completed; ?>],
                    backgroundColor: ['#fbbf24', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 10 } } }
                }
            }
        });

        function openUploadModal(id) {
            const input = document.createElement('input');
            input.type = 'file';
            input.onchange = e => {
                const formData = new FormData();
                formData.append('report', e.target.files[0]);
                formData.append('booking_id', id);
                fetch('upload_report_ajax.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(data => data.trim() === 'Success' ? location.reload() : alert('Error: ' + data));
            };
            input.click();
        }

        function updateStatus(id, status) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', status);
            fetch('update_status_ajax.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => data.trim() === 'Success' ? location.reload() : alert('Error updating status'));
        }
    </script>
</body>
</html>