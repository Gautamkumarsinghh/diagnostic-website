<?php
include '../db/config.php';

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $pass = $_POST['password']; 
    $cpass = $_POST['cpassword'];

    if(!preg_match('/^[0-9]{10}$/', $mobile)){
        $error = "Mobile number must be exactly 10 digits!";
    } elseif($pass !== $cpass){
        $error = "Passwords do not match!"; 
    } else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0){
            $error = "Email already registered!";
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            $ins = mysqli_query($conn, "INSERT INTO users (name, mobile, email, password) VALUES ('$name', '$mobile', '$email', '$hashed_pass')");
            
            if($ins){
                echo "<script>alert('Registration successful! Login now.'); window.location='login.php';</script>";
            } else {
                $error = "Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | My Diagnostic Lab</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> 
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .glass-panel { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        .bg-pattern { background-image: radial-gradient(rgba(255, 255, 255, 0.2) 2px, transparent 2px); background-size: 30px 30px; }
        input[type="number"]::-webkit-inner-spin-button, input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="flex flex-col min-h-screen overflow-x-hidden">

    <!-- Header Inclusion -->
    <?php include '../header.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-grow flex items-center justify-center p-6 lg:p-12 relative">
        
        <!-- Background Decorations -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 bg-slate-50">
            <div class="absolute top-[10%] -left-[10%] w-[50%] h-[50%] rounded-full bg-indigo-400/20 blur-[120px]"></div>
            <div class="absolute bottom-[10%] -right-[10%] w-[40%] h-[40%] rounded-full bg-purple-400/20 blur-[120px]"></div>
        </div>

        <div class="w-full max-w-6xl bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] overflow-hidden flex flex-col lg:flex-row-reverse border border-white relative z-10">
            
            <!-- Right Side / Branding (Image & Gradient) -->
            <div class="lg:w-5/12 relative overflow-hidden hidden lg:flex flex-col justify-between p-12 text-white bg-indigo-600">
                <!-- Overlay Gradient -->
                <div class="absolute inset-0 bg-gradient-to-bl from-indigo-600 via-purple-700 to-indigo-900 opacity-90 z-0"></div>
                <div class="absolute inset-0 bg-pattern opacity-30 z-0"></div>
                
                <!-- Content -->
                <div class="relative z-10">
                    <div class="bg-white/20 w-16 h-16 rounded-2xl flex items-center justify-center backdrop-blur-md mb-8 border border-white/30 shadow-xl">
                        <i class="fas fa-heartbeat text-3xl text-pink-200"></i>
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight mb-6">Start Your<br><span class="text-purple-200">Health Journey</span></h1>
                    <p class="text-indigo-100/90 text-lg leading-relaxed mb-8 max-w-sm">Join thousands of satisfied patients. Get your diagnostic tests done from the comfort of your home with our premium services.</p>
                </div>
                
                <!-- Community Stats -->
                <div class="relative z-10 bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm tracking-wide text-white">50,000+ Patients</p>
                            <p class="text-xs text-indigo-200 uppercase tracking-widest mt-0.5">Trust Our Services</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm tracking-wide text-white">100+ Premium Tests</p>
                            <p class="text-xs text-indigo-200 uppercase tracking-widest mt-0.5">Comprehensive Portfolios</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Left Side / Form Container -->
            <div class="lg:w-7/12 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-white relative">
                
                <div class="max-w-xl w-full mx-auto relative z-10">
                    <!-- Mobile Logo -->
                    <div class="lg:hidden bg-indigo-50 w-16 h-16 rounded-2xl flex items-center justify-center mb-8 border border-indigo-100 text-indigo-600 shadow-sm">
                        <i class="fas fa-user-plus text-3xl"></i>
                    </div>

                    <div class="mb-10 text-center lg:text-left">
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Create an Account ✨</h2>
                        <p class="text-gray-500 font-medium">Join us today to book tests and access your health reports online.</p>
                    </div>

                    <?php if(isset($error)): ?>
                        <div class="mb-6 bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-pulse flex-row">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                            <span class="text-sm font-bold"><?php echo $error; ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name Input -->
                            <div class="space-y-2 group">
                                <label class="text-[11px] font-black tracking-widest text-gray-400 uppercase ml-1 group-focus-within:text-indigo-600 transition-colors">Full Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" placeholder="John Doe" required 
                                           class="w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-medium text-gray-800 placeholder-gray-400">
                                </div>
                            </div>

                            <!-- Mobile Input -->
                            <div class="space-y-2 group">
                                <label class="text-[11px] font-black tracking-widest text-gray-400 uppercase ml-1 group-focus-within:text-indigo-600 transition-colors">Phone Number</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-phone-alt text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                    </div>
                                    <input type="tel" name="mobile" id="mobile" placeholder="10 Digit Mobile Number" required 
                                           maxlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                           class="w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-medium text-gray-800 placeholder-gray-400">
                                </div>
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="space-y-2 group">
                            <label class="text-[11px] font-black tracking-widest text-gray-400 uppercase ml-1 group-focus-within:text-indigo-600 transition-colors">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                </div>
                                <input type="email" name="email" placeholder="example@mail.com" required 
                                       class="w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-medium text-gray-800 placeholder-gray-400">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password Input -->
                            <div class="space-y-2 group">
                                <label class="text-[11px] font-black tracking-widest text-gray-400 uppercase ml-1 group-focus-within:text-indigo-600 transition-colors">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                    </div>
                                    <input type="password" id="password" name="password" placeholder="••••••••" required 
                                           class="w-full pl-11 pr-11 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-medium text-gray-800 placeholder-gray-400">
                                    <button type="button" onclick="togglePass('password', 'eye1')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none">
                                        <i id="eye1" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-2 group">
                                <label class="text-[11px] font-black tracking-widest text-gray-400 uppercase ml-1 group-focus-within:text-indigo-600 transition-colors">Confirm Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-check-circle text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                    </div>
                                    <input type="password" id="cpassword" name="cpassword" placeholder="••••••••" required 
                                           class="w-full pl-11 pr-11 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all font-medium text-gray-800 placeholder-gray-400">
                                    <button type="button" onclick="togglePass('cpassword', 'eye2')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none">
                                        <i id="eye2" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button name="register" type="submit" class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 hover:-translate-y-1 transition-all duration-300 active:scale-95 flex items-center justify-center gap-2 mt-4 group">
                            Create My Account <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="mt-10 text-center bg-gray-50 rounded-2xl p-5 border border-gray-100">
                        <p class="text-gray-500 text-sm font-medium">Already have an account? 
                            <a href="login.php" class="text-indigo-600 font-extrabold ml-1 hover:underline decoration-2 underline-offset-4">Sign in instead</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Inclusion -->
    <?php include '../footer.php'; ?>

    <script>
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>