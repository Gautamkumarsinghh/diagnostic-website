<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../db/config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';

function getCount($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    return ($res) ? mysqli_num_rows($res) : 0;
}

$count_all = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id'");
$count_pending = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id' AND LOWER(status) = 'pending'");
$count_completed = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id' AND LOWER(status) = 'completed'");
$count_cancelled = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id' AND LOWER(status) = 'cancelled'");

$sql = "SELECT * FROM bookings WHERE user_id = '$user_id'";
if ($filter !== 'All') {
    $f = mysqli_real_escape_string($conn, strtolower($filter));
    $sql .= " AND LOWER(status) = '$f'";
}
$sql .= " ORDER BY created_at DESC"; 
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | MyLab Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3f4f6; }
        
        /* Sidebar Colorful Icon Styles */
        .nav-link i { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 10px; transition: all 0.3s; }
        .icon-bookings { background: #e0f2fe; color: #0ea5e9; }
        .icon-address { background: #fef3c7; color: #d97706; }
        .icon-members { background: #f0fdf4; color: #22c55e; }
        .icon-reports { background: #fae8ff; color: #a855f7; }

        .nav-link.active { background: #ffffff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border-left: 4px solid #2563eb; }
        .nav-link:hover:not(.active) { background: #f9fafb; transform: translateX(5px); }
        
        .booking-card { transition: transform 0.2s, box-shadow 0.2s; }
        .booking-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="text-slate-900">

<?php include '../header.php'; ?>

<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-72 space-y-2">
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100">
                <nav class="space-y-1">
                    <a href="user-bookings.php" class="nav-link active flex items-center gap-4 p-3 rounded-2xl group">
                        <i class="fa-solid fa-calendar-check icon-bookings"></i>
                        <span class="font-bold text-slate-700">My Bookings</span>
                    </a>
                    <a href="profile.php" class="nav-link flex items-center gap-4 p-3 rounded-2xl group">
                        <i class="fa-solid fa-location-dot icon-address"></i>
                        <span class="font-semibold text-slate-500 group-hover:text-slate-800">My Address</span>
                    </a>
                    <a href="#" class="nav-link flex items-center gap-4 p-3 rounded-2xl group">
                        <i class="fa-solid fa-users icon-members"></i>
                        <span class="font-semibold text-slate-500 group-hover:text-slate-800">Manage Members</span>
                    </a>
                    <a href="dashboard.php" class="nav-link flex items-center gap-4 p-3 rounded-2xl group">
                        <i class="fa-solid fa-file-medical icon-reports"></i>
                        <span class="font-semibold text-slate-500 group-hover:text-slate-800">My Reports</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1">
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Booking History</h1>
                <p class="text-slate-500 mt-1">Manage and track your diagnostic appointments.</p>
            </div>

            <!-- Modern Filter Tabs -->
            <div class="flex flex-wrap items-center gap-3 mb-8 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 inline-flex">
                <?php 
                $tabs = [
                    'All' => $count_all, 
                    'Pending' => $count_pending, 
                    'Completed' => $count_completed, 
                    'Cancelled' => $count_cancelled
                ];
                foreach($tabs as $label => $count): 
                    $isActive = ($filter == $label);
                ?>
                    <a href="?filter=<?php echo $label; ?>" 
                       class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2
                       <?php echo $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-slate-50'; ?>">
                        <?php echo $label; ?>
                        <span class="px-2 py-0.5 rounded-md text-[11px] <?php echo $isActive ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-500'; ?>">
                            <?php echo $count; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Bookings List -->
            <div class="space-y-4">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $status = strtolower(trim($row['status']));
                        
                        // Dynamic Status Styling
                        $statusStyles = [
                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                            'pending'   => 'bg-amber-50 text-amber-700 border-amber-100'
                        ];
                        $currentStyle = $statusStyles[$status] ?? 'bg-slate-50 text-slate-700 border-slate-100';
                    ?>
                        <div class="booking-card bg-white p-5 rounded-3xl border border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner">
                                    <i class="fa-solid fa-microscope text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-slate-800 leading-none mb-2"><?php echo htmlspecialchars($row['test']); ?></h3>
                                    <div class="flex flex-wrap gap-4 items-center">
                                        <div class="flex items-center text-sm font-medium text-slate-500">
                                            <i class="fa-regular fa-calendar-check mr-2 text-blue-500"></i>
                                            <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                        </div>
                                        <div class="flex items-center text-sm font-bold text-slate-400">
                                            <span class="bg-slate-100 px-2 py-0.5 rounded-md text-[11px] mr-2 text-slate-500 uppercase">ID</span>
                                            #<?php echo $row['id']; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-0 pt-4 sm:pt-0">
                                <span class="px-4 py-2 rounded-xl text-[11px] font-black tracking-widest uppercase border <?php echo $currentStyle; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                                <a href="booking-details.php?id=<?php echo $row['id']; ?>" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-colors">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- Empty State Design -->
                    <div class="text-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-slate-200">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800">No appointments found</h2>
                        <p class="text-slate-500 mt-2 max-w-xs mx-auto">It looks like you haven't booked any tests yet. Start your health journey today!</p>
                        <a href="../tests.php" class="inline-block mt-8 bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">Book New Test</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>