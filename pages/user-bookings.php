<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../db/config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';

// SQL Query helper - Case Insensitive comparison
function getCount($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    return ($res) ? mysqli_num_rows($res) : 0;
}

// Counts fetch karein (LOWER use kiya taki pending/Pending dono count ho)
$count_all = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id'");
$count_pending = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id' AND LOWER(status) = 'pending'");
$count_completed = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id' AND LOWER(status) = 'completed'");
$count_cancelled = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id' AND LOWER(status) = 'cancelled'");

// Main Query
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
    <title>My Bookings - MyLab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .sidebar { background: #fff; min-height: 100vh; border-right: 1px solid #e2e8f0; }
        .sidebar a { display: block; padding: 12px 24px; color: #64748b; text-decoration: none; border-radius: 10px; margin: 4px 16px; font-weight: 500; }
        .sidebar a.active { background: #eff6ff; color: #2563eb; font-weight: 600; }
        .tab-btn { border: 1px solid #e2e8f0; background: #fff; padding: 8px 24px; border-radius: 30px; text-decoration: none; color: #64748b; font-size: 14px; margin-right: 12px; display: inline-flex; align-items: center; }
        .tab-btn.active { background: #2563eb; color: #fff; border-color: #2563eb; }
        .count-badge { background: rgba(0,0,0,0.05); border-radius: 50%; padding: 2px 8px; font-size: 11px; margin-left: 8px; font-weight: bold; }
        .tab-btn.active .count-badge { background: rgba(255,255,255,0.2); }
    </style>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 sidebar p-0 d-none d-md-block">
            <div class="mt-4">
                <a href="user-bookings.php" class="active"><i class="fa-solid fa-calendar-check me-3"></i> My Bookings</a>
                <a href="profile.php"><i class="fa-solid fa-location-dot me-3"></i> My Address</a>
                <a href="#"><i class="fa-solid fa-users me-3"></i> Manage Members</a>
                <a href="dashboard.php"><i class="fa-solid fa-file-medical me-3"></i> My Reports</a>
            </div>
        </div>

        <div class="col-md-9 p-4 p-md-5">
            <h2 class="fw-bold mb-4 text-slate-800">My Bookings</h2>

            <div class="flex flex-wrap gap-2 mb-8">
                <a href="?filter=All" class="tab-btn <?php echo ($filter == 'All') ? 'active' : ''; ?>">All <span class="count-badge"><?php echo $count_all; ?></span></a>
                <a href="?filter=Pending" class="tab-btn <?php echo ($filter == 'Pending') ? 'active' : ''; ?>">Pending <span class="count-badge"><?php echo $count_pending; ?></span></a>
                <a href="?filter=Completed" class="tab-btn <?php echo ($filter == 'Completed') ? 'active' : ''; ?>">Completed <span class="count-badge"><?php echo $count_completed; ?></span></a>
                <a href="?filter=Cancelled" class="tab-btn <?php echo ($filter == 'Cancelled') ? 'active' : ''; ?>">Cancelled <span class="count-badge"><?php echo $count_cancelled; ?></span></a>
            </div>

            <div class="space-y-4">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $rawStatus = strtolower(trim($row['status']));
                        
                        // Status colors setup
                        if($rawStatus == 'completed') {
                            $bClass = "text-green-700 bg-green-100";
                        } elseif($rawStatus == 'cancelled') {
                            $bClass = "text-red-700 bg-red-100";
                        } else {
                            $bClass = "text-orange-700 bg-orange-100";
                        }
                    ?>
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                                    <i class="fa-solid fa-file-medical text-xl"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-800 mb-1"><?php echo htmlspecialchars($row['test']); ?></h5>
                                    <div class="flex gap-4 text-sm text-slate-500 font-medium">
                                        <span><i class="fa-regular fa-calendar-alt me-2 text-blue-400"></i><?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                                        <span><i class="fa-solid fa-hashtag me-2 text-blue-400"></i>ID: <?php echo $row['id']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black tracking-widest uppercase <?php echo $bClass; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-200">
                        <h5 class="text-slate-800 font-bold">No bookings found for this user.</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>