<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../db/config.php';

// Agar user login nahi hai toh login page par bhej dein (Zaruri for Patient ID)
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// URL se test ka naam lena
$selected_test = $_GET['test'] ?? '';

if(isset($_POST['submit'])){
    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $test   = mysqli_real_escape_string($conn, $_POST['test']);
    $email  = $_SESSION['user_email'] ?? ''; // Session se email lena

    $q = mysqli_query($conn, "INSERT INTO bookings(name, mobile, test, email, status) 
                              VALUES('$name', '$mobile', '$test', '$email', 'Pending')");

    if($q){
        echo "<script>
        alert('✅ Booking Successful! Our team will contact you soon.');
        window.location.href='../index.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Test | MyLab Diagnostic</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0f7ff; }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">

    <div class="glass-card w-full max-w-xl rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] overflow-hidden border border-white">
        
        <!-- Header Section -->
        <div class="bg-blue-600 p-8 text-white relative overflow-hidden">
            <div class="relative z-10 flex items-center gap-4">
                <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-md">
                    <i class="fas fa-notes-medical text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">Complete Your Booking</h2>
                    <p class="text-blue-100 text-sm opacity-90">Fill details for home sample collection</p>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
        </div>

        <div class="p-8 md:p-10">
            <form method="post" class="space-y-6">
                
                <!-- Test Selection (Pre-selected from URL) -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Selected Diagnostic Test</label>
                    <div class="relative">
                        <i class="fas fa-microscope absolute left-4 top-4 text-blue-500"></i>
                        <select name="test" required class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold appearance-none">
                            <option value="">Select a Test</option>
                            <!-- Database se tests list mangwane ki zarurat nahi, hum URL wala value use karenge -->
                            <option value="CBC Test" <?php if($selected_test == 'CBC Test') echo 'selected'; ?>>CBC (Complete Blood Count)</option>
                            <option value="TSH Test" <?php if($selected_test == 'TSH Test') echo 'selected'; ?>>Thyroid Profile (TSH)</option>
                            <option value="KFT" <?php if($selected_test == 'KFT') echo 'selected'; ?>>KFT (Kidney Function Test)</option>
                            <option value="Lipid Profile" <?php if($selected_test == 'Lipid Profile') echo 'selected'; ?>>Lipid Profile</option>
                            <option value="Sugar Test" <?php if($selected_test == 'Sugar Test') echo 'selected'; ?>>Blood Sugar (F/PP)</option>
                            <!-- Agar URL wala inme se nahi hai toh extra option -->
                            <?php if(!empty($selected_test) && !in_array($selected_test, ['CBC Test','TSH Test','KFT','Lipid Profile','Sugar Test'])): ?>
                                <option value="<?php echo $selected_test; ?>" selected><?php echo $selected_test; ?></option>
                            <?php endif; ?>
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-5 text-gray-300 text-xs"></i>
                    </div>
                </div>

                <!-- Patient Name -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Patient Full Name</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                        <input type="text" name="name" value="<?php echo $_SESSION['user_name'] ?? ''; ?>" placeholder="Enter patient name" required 
                               class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-medium">
                    </div>
                </div>

                <!-- Mobile Number -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Mobile Number</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-4 top-4 text-gray-400"></i>
                        <input type="tel" name="mobile" placeholder="e.g. 91115-91115" required 
                               class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-medium">
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4">
                    <button name="submit" class="w-full bg-blue-600 text-white font-bold py-5 rounded-2xl shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3">
                        Confirm Booking <i class="fas fa-check-circle"></i>
                    </button>
                </div>
            </form>

            <!-- Trust Footer -->
            <div class="mt-8 pt-6 border-t border-gray-50 flex justify-between text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                <span><i class="fas fa-shield-alt text-green-500 mr-1"></i> Trusted Lab</span>
                <span><i class="fas fa-file-invoice text-blue-500 mr-1"></i> Online Reports</span>
                <span><i class="fas fa-home text-orange-500 mr-1"></i> Home Collection</span>
            </div>
        </div>
    </div>

</body>
</html>