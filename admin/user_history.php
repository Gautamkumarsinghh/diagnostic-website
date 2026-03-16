<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($user_id == 0) header("Location: users.php");

// Fetch User Info
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));

// Fetch Booking Timeline
$bookings = mysqli_query($conn, "SELECT * FROM bookings WHERE user_id=$user_id ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 40px;
            bottom: -20px;
            width: 2px;
            background: #e2e8f0;
        }
        .timeline-item:last-child::before { display: none; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <!-- Breadcrumb & Title -->
        <div class="mb-8">
            <a href="users.php" class="text-blue-600 font-bold text-sm flex items-center gap-2 mb-2 hover:translate-x-[-4px] transition-transform">
                <i class="fas fa-arrow-left"></i> Back to Patient Base
            </a>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Patient Profile (360° View)</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT COLUMN: PROFILE & ADDRESSES -->
            <div class="lg:col-span-1 space-y-8">
                
                <!-- Profile Info Card -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-[2rem] flex items-center justify-center text-white text-4xl font-black mb-6 shadow-xl shadow-blue-200">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                        <h2 class="text-2xl font-black text-slate-800"><?php echo htmlspecialchars($user['name']); ?></h2>
                        <span class="px-4 py-1.5 bg-blue-50 text-blue-600 text-xs font-black uppercase tracking-widest rounded-full mt-2 border border-blue-100">
                            Verified Patient
                        </span>
                    </div>

                    <div class="mt-8 space-y-6 pt-8 border-t border-slate-50">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase text-slate-400 tracking-tighter">Email Address</span>
                                <span class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase text-slate-400 tracking-tighter">Phone Number</span>
                                <span class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($user['mobile']); ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase text-slate-400 tracking-tighter">Member Since</span>
                                <span class="text-sm font-bold text-slate-700"><?php echo date('d M, Y', strtotime($user['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saved Addresses Card -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-black text-slate-800">Saved Locations</h3>
                        <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 font-bold text-sm">
                            <?php $addr_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM user_addresses WHERE user_id=$user_id"))['c']; echo $addr_count; ?>
                        </span>
                    </div>

                    <div class="space-y-4">
                        <?php
                        $addrs = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id=$user_id ORDER BY id DESC");
                        if(mysqli_num_rows($addrs) > 0):
                            while($a = mysqli_fetch_assoc($addrs)):
                        ?>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 group transition-all hover:bg-white hover:border-blue-200">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-black uppercase bg-blue-100 text-blue-600 px-2 py-1 rounded-md">
                                    <?php echo htmlspecialchars($a['title']); ?>
                                </span>
                                <?php if($a['latitude'] != 0): ?>
                                    <a href="https://www.google.com/maps?q=<?php echo $a['latitude']; ?>,<?php echo $a['longitude']; ?>" target="_blank" class="text-emerald-500 hover:scale-110 transition-transform"><i class="fas fa-location-dot"></i></a>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs font-bold text-slate-700 leading-relaxed"><?php echo htmlspecialchars($a['address_line']); ?></p>
                            <p class="text-[10px] text-slate-400 font-bold mt-2 uppercase tracking-tight">Pin: <?php echo htmlspecialchars($a['pincode']); ?></p>
                        </div>
                        <?php endwhile; else: ?>
                            <div class="text-center py-6 border-2 border-dashed border-slate-100 rounded-2xl">
                                <p class="text-xs font-bold text-slate-300">No addresses saved</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: BOOKING JOURNEY -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm">
                        <div class="text-[10px] font-black uppercase text-slate-400 mb-1">Total Bookings</div>
                        <div class="text-2xl font-black text-slate-800"><?php echo mysqli_num_rows($bookings); ?></div>
                    </div>
                    <?php 
                    $revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) s FROM bookings WHERE user_id=$user_id"))['s'] ?? 0;
                    ?>
                    <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm">
                        <div class="text-[10px] font-black uppercase text-slate-400 mb-1">Total Revenue</div>
                        <div class="text-2xl font-black text-emerald-600">₹<?php echo number_format($revenue); ?></div>
                    </div>
                    <?php 
                    $completed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM bookings WHERE user_id=$user_id AND status='Completed'"))['c'];
                    ?>
                    <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm">
                        <div class="text-[10px] font-black uppercase text-slate-400 mb-1">Success Rate</div>
                        <div class="text-2xl font-black text-blue-600"><?php echo (mysqli_num_rows($bookings) > 0) ? round(($completed/mysqli_num_rows($bookings))*100) : 0; ?>%</div>
                    </div>
                    <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm">
                        <div class="text-[10px] font-black uppercase text-slate-400 mb-1">Health Reports</div>
                        <div class="text-2xl font-black text-amber-600"><?php echo $completed; ?></div>
                    </div>
                </div>

                <!-- Booking History Timeline -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 mb-10 border-b border-slate-50 pb-6">Health & Booking Journey</h3>

                    <div class="space-y-1">
                        <?php 
                        mysqli_data_seek($bookings, 0); // Reset result pointer
                        if(mysqli_num_rows($bookings) > 0): 
                        ?>
                            <?php while($b = mysqli_fetch_assoc($bookings)): ?>
                                <div class="timeline-item relative pl-16 pb-12">
                                    <!-- Status Dot -->
                                    <div class="absolute left-0 top-0 w-10 h-10 rounded-2xl border-4 border-white shadow-lg z-10 flex items-center justify-center
                                        <?php echo ($b['status'] == 'Completed') ? 'bg-emerald-500' : 'bg-blue-500'; ?> transition-transform hover:scale-110">
                                        <i class="fas <?php echo ($b['status'] == 'Completed') ? 'fa-check' : 'fa-clock'; ?> text-white text-xs"></i>
                                    </div>

                                    <!-- Content Card -->
                                    <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-100 group hover:bg-white hover:shadow-xl hover:shadow-slate-200 transition-all border-l-4 <?php echo ($b['status'] == 'Completed') ? 'border-l-emerald-500' : 'border-l-blue-500'; ?>">
                                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                                            <div>
                                                <div class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">
                                                    <?php echo date('d M Y, h:i A', strtotime($b['created_at'])); ?>
                                                </div>
                                                <h3 class="text-xl font-black text-slate-800 group-hover:text-blue-600 transition-colors"><?php echo htmlspecialchars($b['test']); ?></h3>
                                            </div>
                                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-white border border-slate-200
                                                <?php echo ($b['status'] == 'Completed') ? 'text-emerald-600' : 'text-blue-600'; ?>">
                                                ● <?php echo $b['status']; ?>
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 py-5 border-y border-slate-200/50">
                                            <div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Package Amount</div>
                                                <div class="font-black text-slate-800 text-lg">₹<?php echo number_format($b['amount']); ?></div>
                                            </div>
                                            <div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Payment Method</div>
                                                <div class="font-black text-slate-800"><?php echo $b['payment_method']; ?></div>
                                                <div class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-500 inline-block mt-1"><?php echo $b['payment_status']; ?></div>
                                            </div>
                                            <div class="col-span-2 md:col-span-1">
                                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Transaction Identity</div>
                                                <div class="font-black text-slate-800 text-xs truncate">#<?php echo $b['transaction_id']; ?></div>
                                            </div>
                                        </div>

                                        <div class="mt-5 flex flex-wrap gap-3">
                                            <?php if(!empty($b['report_file'])): ?>
                                                <a href="../uploads/reports/<?php echo $b['report_file']; ?>" target="_blank" class="flex-1 min-w-[150px] bg-slate-900 text-white p-3.5 rounded-xl font-bold text-center text-xs hover:bg-emerald-600 transition-all shadow-lg flex items-center justify-center gap-2">
                                                    <i class="fas fa-file-pdf"></i> View Report
                                                </a>
                                            <?php endif; ?>
                                            <?php if(!empty($b['payment_proof'])): ?>
                                                <a href="../uploads/payments/<?php echo $b['payment_proof']; ?>" target="_blank" class="flex-1 min-w-[150px] bg-white border border-slate-200 text-slate-600 p-3.5 rounded-xl font-bold text-center text-xs hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                                                    <i class="fas fa-receipt"></i> Payment Proof
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-20 text-slate-300">
                                <i class="fas fa-notes-medical text-6xl mb-4 opacity-10"></i>
                                <p class="font-bold">This patient has no booking records yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
