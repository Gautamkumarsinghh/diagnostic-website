<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Auto-create table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    status ENUM('Available', 'Busy', 'On Leave') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$msg = "";

// Add Staff
if (isset($_POST['add_staff'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    mysqli_query($conn, "INSERT INTO staff (name, mobile) VALUES ('$name', '$mobile')");
    $msg = "Staff member added successfully!";
}

// Delete Staff
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM staff WHERE id='$id'");
    header("Location: manage_staff.php?status=deleted");
    exit();
}

if(isset($_GET['status'])) $msg = "Staff record " . $_GET['status'] . " successfully!";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Field Operations | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="mb-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Field Operations</h1>
                <p class="text-slate-500 font-medium">Coordinate phlebotomists and live collection duties</p>
            </div>
            <div class="flex bg-white p-1.5 rounded-2xl shadow-sm border border-slate-100">
                <button onclick="switchTab('manage')" id="btn-manage" class="tab-btn active bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all">Manage Team</button>
                <button onclick="switchTab('duty')" id="btn-duty" class="tab-btn text-slate-500 px-6 py-2.5 rounded-xl font-bold text-sm transition-all hover:bg-slate-50">Live Duty Board</button>
            </div>
        </div>

        <?php if($msg != ""): ?>
            <div class="bg-emerald-100 text-emerald-700 p-4 rounded-2xl mb-8 font-bold flex items-center gap-3 border-l-4 border-emerald-500">
                <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- Tab 1: Manage Team -->
        <div id="tab-manage" class="tab-content grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 mb-6">Onboard New Phleb</h3>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Full Name</label>
                            <input type="text" name="name" placeholder="John Doe" required class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Mobile Number</label>
                            <input type="text" name="mobile" placeholder="9999999999" required class="w-full bg-slate-50 border-none rounded-2xl p-4 font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <button type="submit" name="add_staff" class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black hover:bg-slate-900 transition-all shadow-xl shadow-blue-200">
                            Register Member
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Team Member</th>
                                <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Duty Status</th>
                                <th class="p-6 text-[11px] font-black text-slate-400 uppercase text-right tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php 
                            $staff_list = mysqli_query($conn, "SELECT * FROM staff ORDER BY id DESC");
                            while($s = mysqli_fetch_assoc($staff_list)): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black">
                                            <?php echo strtoupper(substr($s['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800"><?php echo $s['name']; ?></div>
                                            <div class="text-xs text-slate-400 font-medium"><?php echo $s['mobile']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                                        <?php echo ($s['status']=='Available') ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600'; ?>">
                                        <?php echo $s['status']; ?>
                                    </span>
                                </td>
                                <td class="p-6 text-right">
                                    <a href="?delete=<?php echo $s['id']; ?>" onclick="return confirm('Remove staff permanently?')" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-red-50 text-red-400 hover:bg-red-600 hover:text-white transition-all">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 2: Live Duty Board -->
        <div id="tab-duty" class="tab-content hidden space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $staff_q = mysqli_query($conn, "SELECT * FROM staff");
                while($s = mysqli_fetch_assoc($staff_q)):
                    $sid = $s['id'];
                    $bookings_q = mysqli_query($conn, "SELECT COUNT(*) as active FROM bookings WHERE staff_id='$sid' AND status NOT IN ('Completed', 'Cancelled')");
                    $b_data = mysqli_fetch_assoc($bookings_q);
                    $active_count = $b_data['active'];
                ?>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black">
                            <?php echo strtoupper(substr($s['name'], 0, 1)); ?>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Active Leads</div>
                            <div class="text-2xl font-black text-slate-900"><?php echo $active_count; ?></div>
                        </div>
                    </div>
                    <h4 class="font-black text-slate-800 mb-6"><?php echo $s['name']; ?></h4>
                    
                    <?php if($active_count > 0): ?>
                        <div class="space-y-3">
                            <?php 
                            $leads = mysqli_query($conn, "SELECT * FROM bookings WHERE staff_id='$sid' AND status NOT IN ('Completed', 'Cancelled') LIMIT 2");
                            while($l = mysqli_fetch_assoc($leads)):
                            ?>
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-center justify-between">
                                    <div class="text-[10px] font-bold text-slate-600 truncate mr-2"><?php echo $l['name']; ?></div>
                                    <span class="text-[8px] font-black uppercase text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"><?php echo $l['status']; ?></span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-[10px] text-slate-300 italic font-bold">No active collections assigned</div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-blue-600', 'text-white', 'active');
                el.classList.add('text-slate-500');
            });

            document.getElementById('tab-' + tabId).classList.remove('hidden');
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.add('bg-blue-600', 'text-white', 'active');
            activeBtn.classList.remove('text-slate-500');
        }
    </script>
</body>
</html>
