<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Schema Sync: Support patient sentiment tracking
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    rating INT DEFAULT 5,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Fetch Feedbacks
$feedbacks = mysqli_query($conn, "SELECT f.*, b.name as patient_name, b.test 
                                 FROM feedbacks f 
                                 JOIN bookings b ON f.booking_id = b.id 
                                 ORDER BY f.id DESC");

// Stats
$avg_res = mysqli_query($conn, "SELECT AVG(rating) as average FROM feedbacks");
$avg_data = mysqli_fetch_assoc($avg_res);
$average_rating = round($avg_data['average'], 1) ?: 5.0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Sentiment | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Patient Sentiment</h1>
            <p class="text-slate-500 font-medium">Analyze service ratings and patient feedback</p>
        </div>

        <!-- Feedback Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col items-center text-center">
                <div class="text-4xl font-black text-blue-600 mb-2"><?php echo $average_rating; ?></div>
                <div class="flex gap-1 text-yellow-400 mb-4">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <i class="fas fa-star <?php echo ($i <= $average_rating) ? '' : 'text-slate-200'; ?>"></i>
                    <?php endfor; ?>
                </div>
                <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">Average Score</div>
            </div>
            
            <div class="bg-slate-900 border-none p-8 rounded-[2.5rem] shadow-xl shadow-slate-200 col-span-1 md:col-span-3 flex items-center justify-between text-white">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-white/10 rounded-3xl flex items-center justify-center text-3xl text-blue-400">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-black">Patient Satisfaction is High</h4>
                        <p class="text-slate-400 text-sm font-medium">92% of patients prefer MyLab for recurring tests.</p>
                    </div>
                </div>
                <button class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-black text-xs hover:bg-white hover:text-slate-900 transition-all">Send Thanks</button>
            </div>
        </div>

        <!-- Feedback List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php if(mysqli_num_rows($feedbacks) > 0): ?>
                <?php while($f = mysqli_fetch_assoc($feedbacks)): ?>
                <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 relative group transition-all hover:bg-blue-50/50">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black">
                                <?php echo strtoupper(substr($f['patient_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-900"><?php echo $f['patient_name']; ?></h4>
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-widest"><?php echo $f['test']; ?></p>
                            </div>
                        </div>
                        <div class="flex gap-1 text-yellow-400">
                            <?php for($i=0; $i<$f['rating']; $i++): ?>
                                <i class="fas fa-star text-xs"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <p class="text-slate-600 leading-relaxed font-medium italic mb-6">"<?php echo htmlspecialchars($f['comment']); ?>"</p>
                    
                    <div class="flex items-center justify-between pt-6 border-t border-slate-100/50">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?php echo date('d M Y', strtotime($f['created_at'])); ?></span>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-emerald-500 hover:text-white transition-all"><i class="fas fa-check text-[10px]"></i></button>
                            <button class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i class="fas fa-share text-[10px]"></i></button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-2 bg-white p-20 rounded-[3rem] text-center border-2 border-dashed border-slate-200">
                    <i class="fas fa-comment-slash text-5xl mb-6 text-slate-200 block"></i>
                    <h3 class="font-black text-slate-900 text-xl">No Feedback Yet</h3>
                    <p class="text-slate-400 font-medium">Patient ratings will appear here after they complete their tests.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
