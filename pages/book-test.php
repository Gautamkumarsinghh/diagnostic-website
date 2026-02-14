<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db/config.php'; 

// 1. Authentication Check
if(!isset($_SESSION['user_id'])){
    header("Location: pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$selected_test_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if(isset($_POST['submit'])){
    $name       = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile     = mysqli_real_escape_string($conn, $_POST['mobile']);
    $package_id = mysqli_real_escape_string($conn, $_POST['package_id']);
    
    // IMPORTANT: Pehle Package ID se Test ka Naam nikalte hain
    $get_pkg = mysqli_query($conn, "SELECT name FROM packages WHERE id = '$package_id'");
    $pkg_data = mysqli_fetch_assoc($get_pkg);
    $test_name = $pkg_data['name'];
    
    // User ki email session se le rahe hain (agar aapke login mein save hai)
    $email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

    // 2. Data ko Bookings table mein daalna (Automatic Status: Pending)
    // Aapke database columns: user_id, name, mobile, test, status, email
    $query = "INSERT INTO bookings (user_id, name, mobile, test, status, email) 
              VALUES ('$user_id', '$name', '$mobile', '$test_name', 'Pending', '$email')";
    
    if(mysqli_query($conn, $query)){
        // SUCCESS: Redirect to My Bookings page
        echo "<script>
        alert('Booking Successful! View your status in My Bookings.');
        window.location.href='pages/user-bookings.php';
        </script>";
    } else {
        $error = "Booking failed: " . mysqli_error($conn);
    }
}

// Packages fetch karna dropdown ke liye
$packages_q = mysqli_query($conn, "SELECT id, name FROM packages");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Diagnostic Test | MyLab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .glass-effect { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">

    <div class="mb-8 text-center">
        <a href="index.php" class="flex items-center gap-2 justify-center mb-4">
            <div class="bg-blue-600 p-2 rounded-xl text-white">
                <i class="fas fa-microscope text-2xl"></i>
            </div>
            <span class="text-2xl font-bold text-gray-800 tracking-tight">My<span class="text-blue-600">Lab</span></span>
        </a>
    </div>

    <div class="glass-effect w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white">
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 p-8 text-center text-white relative">
            <div class="relative z-10">
                <h2 class="text-2xl font-bold">🧪 Book Your Test</h2>
                <p class="text-blue-100 text-sm mt-1">Free Home Sample Collection Available</p>
            </div>
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
        </div>

        <div class="p-8 lg:p-10">
            <?php if(isset($error)): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Patient Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i class="fas fa-user text-sm"></i>
                        </span>
                        <input type="text" name="name" value="<?php echo $_SESSION['user_name']; ?>" required 
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i class="fas fa-phone-alt text-sm"></i>
                        </span>
                        <input type="tel" name="mobile" placeholder="Enter Mobile Number" required 
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Diagnostic Test</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i class="fas fa-vial text-sm"></i>
                        </span>
                        <select name="package_id" required class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition appearance-none cursor-pointer font-medium text-gray-700">
                            <option value="">-- Choose Test --</option>
                            <?php while($pkg = mysqli_fetch_assoc($packages_q)): ?>
                                <option value="<?php echo $pkg['id']; ?>" <?php echo ($selected_test_id == $pkg['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($pkg['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>

                <button name="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-200 transform active:scale-[0.98] transition duration-200 flex items-center justify-center gap-3">
                    Confirm Appointment <i class="fas fa-check-circle"></i>
                </button>
            </form>
        </div>
    </div>

    <a href="index.php" class="mt-8 text-gray-500 font-medium hover:text-blue-600 transition">
        <i class="fas fa-arrow-left mr-2"></i> Back to Home
    </a>

</body>
</html>