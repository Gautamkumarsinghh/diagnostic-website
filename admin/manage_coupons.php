<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Robust Schema Sync: Ensure table and all columns exist
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_percent INT NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Check and fix missing columns (Commonly happens during updates)
$cols = mysqli_query($conn, "SHOW COLUMNS FROM coupons");
$has_status = false;
$has_discount_percent = false;
$has_old_discount = false;

while($c = mysqli_fetch_assoc($cols)) {
    if($c['Field'] == 'status') $has_status = true;
    if($c['Field'] == 'discount_percent') $has_discount_percent = true;
    if($c['Field'] == 'discount_percentage') $has_old_discount = true;
}

if(!$has_status) {
    mysqli_query($conn, "ALTER TABLE coupons ADD COLUMN status ENUM('Active', 'Inactive') DEFAULT 'Active' AFTER discount_percent");
}

if(!$has_discount_percent) {
    if($has_old_discount) {
        mysqli_query($conn, "ALTER TABLE coupons CHANGE discount_percentage discount_percent INT NOT NULL");
    } else {
        mysqli_query($conn, "ALTER TABLE coupons ADD COLUMN discount_percent INT NOT NULL DEFAULT 0 AFTER code");
    }
}

$msg = "";

// Handle Add Coupon
if (isset($_POST['add_coupon'])) {
    $code = mysqli_real_escape_string($conn, strtoupper(trim($_POST['code'])));
    $discount = intval($_POST['discount']);
    
    $check = mysqli_query($conn, "SELECT id FROM coupons WHERE code='$code'");
    if(mysqli_num_rows($check) > 0) {
        $msg = "Error: Coupon code already exists!";
    } else {
        mysqli_query($conn, "INSERT INTO coupons (code, discount_percent) VALUES ('$code', '$discount')");
        $msg = "Coupon added successfully!";
    }
}

// Handle Status Toggle
if (isset($_GET['toggle'])) {
    $tid = $_GET['toggle'];
    mysqli_query($conn, "UPDATE coupons SET status = IF(status='Active', 'Inactive', 'Active') WHERE id='$tid'");
    header("Location: manage_coupons.php?status=updated");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $did = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM coupons WHERE id='$did'");
    header("Location: manage_coupons.php?status=deleted");
    exit();
}

if(isset($_GET['status'])) $msg = "Coupon " . $_GET['status'] . " successfully!";

$coupons = mysqli_query($conn, "SELECT * FROM coupons ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coupons | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content p-10">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-800">Coupon Manager</h1>
                <p class="text-slate-500 font-medium">Create and manage discount codes for patients</p>
            </div>
        </div>

            <?php if($msg != ""): ?>
                <div class="bg-emerald-100 text-emerald-700 p-4 rounded-2xl mb-8 font-bold flex items-center gap-3 border-l-4 border-emerald-500">
                    <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Add Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 sticky top-10">
                        <h3 class="text-xl font-black text-slate-800 mb-6">Create Coupon</h3>
                        <form method="POST" class="space-y-6">
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Coupon Code</label>
                                <input type="text" name="code" placeholder="E.g. HEALTH30" required class="w-full bg-slate-50 border-none rounded-xl p-4 font-black text-slate-800 placeholder:text-slate-300 focus:ring-2 focus:ring-blue-500 outline-none uppercase">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Discount Percentage (%)</label>
                                <input type="number" name="discount" placeholder="30" min="1" max="100" required class="w-full bg-slate-50 border-none rounded-xl p-4 font-black text-blue-600 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <button type="submit" name="add_coupon" class="w-full bg-slate-900 text-white py-4 rounded-xl font-black hover:bg-blue-600 transition-all shadow-xl shadow-slate-200">
                                Save New Coupon <i class="fas fa-plus ml-2 text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- List Table -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest">Code</th>
                                    <th class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest">Discount</th>
                                    <th class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest">Status</th>
                                    <th class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($coupons) > 0): ?>
                                    <?php while($c = mysqli_fetch_assoc($coupons)): ?>
                                        <tr class="border-t border-slate-50 hover:bg-slate-50/50 transition-colors">
                                            <td class="p-6">
                                                <div class="bg-blue-50 text-blue-700 px-4 py-1.5 rounded-lg inline-block font-black text-md">
                                                    <?php echo $c['code']; ?>
                                                </div>
                                            </td>
                                             <td class="p-6 font-black text-slate-700 text-lg">
                                                <?php echo $c['discount_percent']; ?>% OFF
                                            </td>
                                            <td class="p-6">
                                                <?php if($c['status'] == 'Active'): ?>
                                                    <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Active</span>
                                                <?php else: ?>
                                                    <span class="bg-slate-100 text-slate-400 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-6 text-right">
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="?toggle=<?php echo $c['id']; ?>" title="Change Status" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-500 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                                                        <i class="fas fa-power-off"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('Delete this coupon?')" class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="p-20 text-center text-slate-400">
                                            <i class="fas fa-ticket-alt text-4xl mb-4 opacity-20 block"></i>
                                            No coupons found. Start by creating one!
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
