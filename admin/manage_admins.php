<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Schema Sync: Support multi-role administration
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    role ENUM('Super Admin', 'Receptionist', 'Lab Tech', 'Manager') DEFAULT 'Receptionist',
    status ENUM('Active', 'Suspended') DEFAULT 'Active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Add dynamic admin logic
$msg = "";
if(isset($_POST['add_admin'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $iq = mysqli_query($conn, "INSERT INTO admins (username, password, full_name, role) VALUES ('$user', '$pass', '$name', '$role')");
    if($iq) $msg = "New administrator account created!";
    else $msg = "Error: Username might already exist.";
}

$admins = mysqli_query($conn, "SELECT * FROM admins ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Accounts | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Team Accounts</h1>
                <p class="text-slate-500 font-medium">Manage administrative staff and system permissions</p>
            </div>
            <button onclick="document.getElementById('adminModal').classList.remove('hidden')" class="bg-slate-900 text-white px-8 py-4 rounded-[1.5rem] font-bold shadow-xl shadow-slate-200 hover:bg-blue-600 transition-all flex items-center gap-3">
                <i class="fas fa-user-plus"></i> Create Access Account
            </button>
        </div>

        <?php if($msg): ?>
            <div class="bg-blue-100 text-blue-700 p-4 rounded-2xl mb-8 font-bold border-l-4 border-blue-500">
                <i class="fas fa-info-circle mr-2"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Administrator</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Role & Permissions</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php while($a = mysqli_fetch_assoc($admins)): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-black">
                                    <?php echo strtoupper(substr($a['username'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800"><?php echo $a['full_name']; ?></div>
                                    <div class="text-xs text-slate-400 font-medium">@<?php echo $a['username']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-6">
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                                <?php echo $a['role']; ?>
                            </span>
                        </td>
                        <td class="p-6">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full <?php echo ($a['status'] == 'Active') ? 'bg-emerald-500' : 'bg-red-500'; ?>"></div>
                                <span class="text-xs font-bold text-slate-600"><?php echo $a['status']; ?></span>
                            </div>
                        </td>
                        <td class="p-6 text-right">
                            <button class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-slate-900 hover:text-white transition-all"><i class="fas fa-edit text-xs"></i></button>
                            <button class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-600 hover:text-white transition-all ml-2"><i class="fas fa-trash text-xs"></i></button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Admin Modal -->
    <div id="adminModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[10000] flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-lg rounded-[3rem] p-10 relative">
            <button onclick="document.getElementById('adminModal').classList.add('hidden')" class="absolute top-8 right-8 text-slate-400 hover:text-slate-900"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-black text-slate-900 mb-8">New Team Access</h2>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" name="full_name" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 font-bold focus:ring-2 focus:ring-blue-500" placeholder="e.g. Anjali Sharma">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Username</label>
                    <input type="text" name="username" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 font-bold focus:ring-2 focus:ring-blue-500" placeholder="anjali_mylab">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Access Role</label>
                    <select name="role" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 font-bold focus:ring-2 focus:ring-blue-500">
                        <option>Receptionist</option>
                        <option>Lab Tech</option>
                        <option>Manager</option>
                        <option>Super Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Secure Password</label>
                    <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 font-bold focus:ring-2 focus:ring-blue-500" placeholder="••••••••">
                </div>
                <button type="submit" name="add_admin" class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black shadow-xl shadow-blue-200 hover:bg-slate-900 transition-all">Enable Access</button>
            </form>
        </div>
    </div>

</body>
</html>
