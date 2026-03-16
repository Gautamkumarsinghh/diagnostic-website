<?php
session_start();
include '../db/config.php';

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($q) > 0){
        $user = mysqli_fetch_assoc($q);
        // Password verification
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_mobile'] = $user['mobile'];
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "Email ID not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | My Diagnostic Lab</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> 
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        .bg-pattern { background-image: radial-gradient(rgba(255, 255, 255, 0.2) 2px, transparent 2px); background-size: 30px 30px; }
    </style>
</head>
<body class="flex flex-col min-h-screen overflow-x-hidden">

    <!-- Header Inclusion -->
    <?php include '../header.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-grow flex items-center justify-center p-6 lg:p-12 relative">
        
        <!-- Background Decorations -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 bg-slate-50">
            <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-400/20 blur-[120px]"></div>
            <div class="absolute top-[60%] -right-[10%] w-[40%] h-[40%] rounded-full bg-indigo-400/20 blur-[120px]"></div>
        </div>

        <div class="w-full max-w-6xl bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] overflow-hidden flex flex-col lg:flex-row border border-white relative z-10">
            
            <!-- Left Side / Branding (Image & Gradient) -->
            <div class="lg:w-5/12 relative overflow-hidden hidden lg:flex flex-col justify-between p-12 text-white bg-blue-600">
                <!-- Overlay Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-indigo-700 to-blue-900 opacity-90 z-0"></div>
                <div class="absolute inset-0 bg-pattern opacity-30 z-0"></div>
                
                <!-- Content -->
                <div class="relative z-10">
                    <div class="bg-white/20 w-16 h-16 rounded-2xl flex items-center justify-center backdrop-blur-md mb-8 border border-white/30 shadow-xl">
                        <i class="fas fa-microscope text-3xl"></i>
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight mb-6">Your Health,<br><span class="text-blue-200">Our Priority.</span></h1>
                    <p class="text-blue-100/80 text-lg leading-relaxed mb-8 max-w-sm">Sign in to access your digital health records, book home collections, and track your diagnostic journey.</p>
                </div>
                
                <!-- Trust Badges -->
                <div class="relative z-10 bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-full bg-green-400/20 flex items-center justify-center text-green-300">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm tracking-wide text-white">NABL Accredited</p>
                            <p class="text-xs text-blue-200 uppercase tracking-widest mt-0.5">100% Accurate</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-400/20 flex items-center justify-center text-blue-300">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm tracking-wide text-white">Fast Results</p>
                            <p class="text-xs text-blue-200 uppercase tracking-widest mt-0.5">Within 24 Hours</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side / Form Container -->
            <div class="lg:w-7/12 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-white relative">
                
                <div class="max-w-md w-full mx-auto relative z-10">
                    <!-- Mobile Logo (Visible only on small screens) -->
                    <div class="lg:hidden bg-blue-50 w-16 h-16 rounded-2xl flex items-center justify-center mb-8 border border-blue-100 text-blue-600 shadow-sm">
                        <i class="fas fa-microscope text-3xl"></i>
                    </div>

                    <div class="mb-10 text-center lg:text-left">
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Welcome Back 👋</h2>
                        <p class="text-gray-500 font-medium">Please enter your details to sign in.</p>
                    </div>

                    <?php if(isset($error)): ?>
                        <div class="mb-6 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-pulse flex-row">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                            <span class="text-sm font-bold"><?php echo $error; ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="space-y-6">
                        <!-- Email Input -->
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

                        <!-- Password Input -->
                        <div class="space-y-2 group">
                            <label class="text-[11px] font-black tracking-widest text-gray-400 uppercase ml-1 group-focus-within:text-blue-600 transition-colors">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                                </div>
                                <input type="password" name="password" id="passwordInput" placeholder="••••••••" required 
                                       class="w-full pl-12 pr-12 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white outline-none transition-all font-medium text-gray-800 placeholder-gray-400">
                                
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                                    <i id="eyeIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                            <!-- Forgot Password Link -->
                            <div class="flex justify-end mt-2">
                                <a href="forgot-password.php" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline transition-all">Forgot password?</a>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button name="login" class="w-full bg-blue-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-blue-500/30 hover:bg-blue-700 hover:-translate-y-1 transition-all duration-300 active:scale-95 flex items-center justify-center gap-2 group">
                            Sign In <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="mt-8 flex items-center justify-center space-x-4">
                        <span class="h-px w-full bg-gray-200"></span>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">OR</span>
                        <span class="h-px w-full bg-gray-200"></span>
                    </div>

                    <!-- Register Link -->
                    <div class="mt-8 text-center bg-gray-50 rounded-2xl p-4 border border-gray-100">
                        <p class="text-gray-500 text-sm font-medium">New to MyLab? 
                            <a href="register.php" class="text-blue-600 font-extrabold ml-1 hover:underline decoration-2 underline-offset-4">Create an account</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Inclusion -->
    <?php include '../footer.php'; ?>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>