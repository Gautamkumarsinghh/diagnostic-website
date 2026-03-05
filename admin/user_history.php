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
    <title>Patient History | MyLab Admin</title>
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
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="mb-10 flex justify-between items-end">
            <div>
                <a href="users.php" class="text-blue-600 font-black text-sm uppercase tracking-widest flex items-center gap-2 mb-2">
                    <i class="fas fa-arrow-left"></i> Back to Patients
                </a>
                <h1 class="text-3xl font-black text-slate-800">Patient Health History</h1>
                <p class="text-slate-500 font-medium">Complete medical journey of <?php echo $user['name']; ?></p>
            </div>
            <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4">
               <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xl font-black">
                   <?php echo strtoupper(substr($user['name'],0,1)); ?>
               </div>
               <div>
                   <div class="font-black text-slate-800"><?php echo $user['name']; ?></div>
                   <div class="text-xs text-slate-400 font-bold"><?php echo $user['email']; ?></div>
               </div>
            </div>
        </div>

        <div class="max-w-4xl">
            <?php if(mysqli_num_rows($bookings) > 0): ?>
                <?php while($b = mysqli_fetch_assoc($bookings)): ?>
                    <div class="timeline-item relative pl-16 pb-12">
                        <!-- Dot -->
                        <div class="absolute left-0 top-0 w-10 h-10 rounded-2xl border-4 border-white shadow-lg z-10 flex items-center justify-center
                            <?php echo ($b['status'] == 'Completed') ? 'bg-emerald-500' : 'bg-blue-500'; ?>">
                            <i class="fas <?php echo ($b['status'] == 'Completed') ? 'fa-check' : 'fa-clock'; ?> text-white text-xs"></i>
                        </div>

                        <!-- Card -->
                        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 group hover:shadow-xl hover:shadow-slate-200 transition-all">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">
                                        <?php echo date('d M Y, h:i A', strtotime($b['created_at'])); ?>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-800"><?php echo $b['test']; ?></h3>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                                    <?php echo ($b['status'] == 'Completed') ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600'; ?>">
                                    <?php echo $b['status']; ?>
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-4 py-4 border-y border-slate-50">
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Amount</div>
                                    <div class="font-black text-slate-800">₹<?php echo $b['amount']; ?></div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Payment</div>
                                    <div class="font-black text-slate-800"><?php echo $b['payment_method']; ?></div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Transaction</div>
                                    <div class="font-black text-slate-800 text-xs">#<?php echo $b['transaction_id']; ?></div>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-3">
                                <?php if($b['report_file']): ?>
                                    <a href="../uploads/reports/<?php echo $b['report_file']; ?>" target="_blank" class="flex-1 bg-emerald-600 text-white p-3 rounded-xl font-black text-center text-xs hover:bg-slate-900 transition-all">
                                        <i class="fas fa-download mr-2"></i> Download Report
                                    </a>
                                <?php else: ?>
                                    <div class="flex-1 bg-slate-100 text-slate-400 p-3 rounded-xl font-black text-center text-xs italic">
                                        Processing Report...
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-20 rounded-[2.5rem] text-center text-slate-300 border border-slate-100">
                    <i class="fas fa-history text-5xl mb-4 opacity-20 block"></i>
                    No medical history found for this patient.
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
