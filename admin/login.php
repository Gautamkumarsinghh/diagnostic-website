<?php
session_start();
include '../db/config.php';

if(isset($_POST['login'])){
    // Security: escaping inputs
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = md5($_POST['password']); // Note: Database mein md5 hai to ye chalega, warna password_verify use karein.

    $q = mysqli_query($conn, "SELECT * FROM admin WHERE username='$user' AND password='$pass'");

    if(mysqli_num_rows($q) > 0){
        $_SESSION['admin'] = $user;
        header("Location:index.php");
        exit();
    } else {
        $error = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Login | MyLab Secure</title>
    
    <!-- Google Fonts & Tailwind CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        /* Floating shapes animation */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        @keyframes float-reverse {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(20px) rotate(-10deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .shape-1 { animation: float 6s ease-in-out infinite; }
        .shape-2 { animation: float-reverse 8s ease-in-out infinite; }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center relative overflow-hidden selection:bg-blue-200">

    <!-- Abstract Background Elements -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="shape-1 absolute top-[-10%] left-[-10%] w-[40%] h-[50%] rounded-full bg-blue-600/10 blur-[100px]"></div>
        <div class="shape-2 absolute bottom-[-10%] right-[-10%] w-[50%] h-[60%] rounded-full bg-indigo-500/10 blur-[120px]"></div>
    </div>

    <!-- Main Login Container -->
    <div class="relative z-10 w-full max-w-[1000px] flex flex-col md:flex-row mx-4 rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] overflow-hidden glass-panel border border-white/80">
        
        <!-- Left Side: Branding/Image Area -->
        <div class="w-full md:w-5/12 bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-800 p-10 md:p-14 text-white relative flex flex-col justify-between overflow-hidden">
            <!-- Decorative overlay -->
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.05]"></div>
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-lg shrink-0">
                        <i class="fa-solid fa-microscope text-2xl"></i>
                    </div>
                </div>
                
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight mb-6">
                    Admin<br><span class="text-blue-200">Workspace</span>
                </h1>
                <p class="text-blue-100 text-lg opacity-90 leading-relaxed font-medium max-w-[280px]">
                    Manage diagnostics, monitor patient flows, and oversee health records securely.
                </p>
            </div>
            
            <div class="relative z-10 mt-12 md:mt-24">
                <div class="flex items-center gap-4 bg-white/10 p-5 rounded-3xl backdrop-blur-md border border-white/20">
                    <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shrink-0 shadow-inner">
                        <i class="fa-solid fa-shield-halved text-xl text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-xs uppercase tracking-widest">Enhanced Security</h4>
                        <p class="text-blue-100 text-xs font-semibold mt-1 flex items-center gap-1"><i class="fa-solid fa-lock text-[10px]"></i> 256-bit secure session</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-7/12 p-8 md:p-14 lg:p-16 flex flex-col justify-center bg-white/60">
            <div class="max-w-sm w-full mx-auto">
                <div class="text-center md:text-left mb-10">
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Welcome Back</h2>
                    <p class="text-slate-500 font-medium mt-2">Enter your admin credentials to continue</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="bg-rose-50 text-rose-600 px-5 py-4 rounded-2xl mb-8 flex items-center gap-3 border border-rose-100 animate-[slideUp_0.3s_ease-out]">
                        <i class="fa-solid fa-circle-exclamation text-lg"></i>
                        <span class="font-bold text-sm"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <!-- Username -->
                    <div class="space-y-2 group">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-600 transition-colors">Username</label>
                        <div class="relative">
                            <i class="fa-solid fa-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors z-10"></i>
                            <input type="text" name="username" placeholder="e.g. admin_mylab" required autofocus autocomplete="off"
                                   class="w-full pl-14 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white outline-none transition-all font-semibold text-slate-800 placeholder-slate-400">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2 group">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-600 transition-colors">Password</label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors z-10"></i>
                            <input type="password" name="password" id="password" placeholder="••••••••" required 
                                   class="w-full pl-14 pr-14 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white outline-none transition-all font-semibold text-slate-800 placeholder-slate-400 tracking-wider">
                            
                            <button type="button" onclick="togglePass()" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors p-1 focus:outline-none z-10">
                                <i id="eye-icon" class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/30 transition-all cursor-pointer">
                            <span class="text-sm font-semibold text-slate-500 group-hover:text-slate-700 transition-colors">Remember me</span>
                        </label>
                        
                        <a href="#" tabindex="-1" class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">Forgot Password?</a>
                    </div>

                    <button type="submit" name="login" class="w-full bg-blue-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3 mt-4">
                        Secure Login <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </button>
                </form>
                
                <div class="mt-12 text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center justify-center gap-1.5 opacity-60">
                        <i class="fa-solid fa-shield-halved"></i> MyLab Diagnostic Admin
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePass() {
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>
</html>