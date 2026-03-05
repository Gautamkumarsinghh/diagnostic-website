<?php
session_start();
include '../db/config.php';

$message = '';
$error = '';

if(isset($_POST['reset'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $q = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    
    if(mysqli_num_rows($q) > 0){
        // Simulate sending email
        $message = "A password reset link has been sent to your email address.";
    } else {
        $error = "We couldn't find an account with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | My Diagnostic Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> 
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .bg-pattern { background-image: radial-gradient(rgba(255, 255, 255, 0.2) 2px, transparent 2px); background-size: 30px 30px; }
    </style>
</head>
<body class="flex flex-col min-h-screen overflow-x-hidden">

    <!-- Header -->
    <?php include '../header.php'; ?>

    <div class="flex-grow flex items-center justify-center p-6 lg:p-12 relative">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 bg-slate-50">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-400/20 blur-[120px]"></div>
            <div class="absolute top-[60%] -right-[10%] w-[40%] h-[40%] rounded-full bg-indigo-400/20 blur-[120px]"></div>
        </div>

        <div class="w-full max-w-6xl bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] overflow-hidden flex flex-col lg:flex-row border border-white relative z-10">
            
            <!-- Left Side Branding -->
            <div class="lg:w-5/12 relative overflow-hidden hidden lg:flex flex-col justify-between p-12 text-white bg-blue-600">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-indigo-700 to-blue-900 opacity-90 z-0"></div>
                <div class="absolute inset-0 bg-pattern opacity-30 z-0"></div>
                
                <div class="relative z-10">
                    <div class="bg-white/20 w-16 h-16 rounded-2xl flex items-center justify-center backdrop-blur-md mb-8 border border-white/30 shadow-xl">
                        <i class="fas fa-microscope text-3xl"></i>
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight mb-6">Reset Your<br><span class="text-blue-200">Password</span></h1>
                    <p class="text-blue-100/80 text-lg leading-relaxed mb-8 max-w-sm">Don't worry, it happens to the best of us. Just enter your email and we'll send you a recovery link.</p>
                </div>
            </div>

            <!-- Right Side Form -->
            <div class="lg:w-7/12 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-white relative">
                <div class="max-w-md w-full mx-auto relative z-10">
                    
                    <div class="lg:hidden bg-blue-50 w-16 h-16 rounded-2xl flex items-center justify-center mb-8 border border-blue-100 text-blue-600 shadow-sm">
                        <i class="fas fa-lock text-3xl"></i>
                    </div>

                    <div class="mb-10 text-center lg:text-left">
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Forgot Password? 🔒</h2>
                        <p class="text-gray-500 font-medium">Enter your registered email below.</p>
                    </div>

                    <?php if(!empty($error)): ?>
                        <div class="mb-6 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-pulse flex-row">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                            <span class="text-sm font-bold"><?php echo $error; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($message)): ?>
                        <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm flex-row">
                            <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                            <span class="text-sm font-bold"><?php echo $message; ?></span>
                        </div>
                    <?php else: ?>
                        <form method="post" class="space-y-6">
                            <div class="space-y-2 group">
                                <label class="text-[11px] font-black tracking-widest text-gray-400 uppercase ml-1 group-focus-within:text-blue-600 transition-colors">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" placeholder="name@example.com" required 
                                           class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white outline-none transition-all font-medium text-gray-800 placeholder-gray-400">
                                </div>
                            </div>

                            <button type="submit" name="reset" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-2xl shadow-[0_10px_20px_-10px_rgba(37,99,235,0.6)] transition-all active:scale-95 flex items-center justify-center gap-2 group">
                                <span>Send Reset Link</span>
                                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <div class="mt-8 text-center">
                        <p class="text-gray-500 text-sm font-medium">Remembered your password? 
                            <a href="login.php" class="text-blue-600 hover:text-blue-700 font-bold ml-1 hover:underline decoration-2 underline-offset-4 transition-all">Back to Login</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

</body>
</html>
