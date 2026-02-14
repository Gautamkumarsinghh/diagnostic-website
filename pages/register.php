<?php
include '../db/config.php';

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $pass = $_POST['password']; 
    $cpass = $_POST['cpassword'];

    if($pass !== $cpass){
        $error = "पासवर्ड मैच नहीं हुआ!"; 
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style> 
        /* Page ko fix karne ke liye CSS */
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, #f0f7ff 0%, #e0e7ff 100%); 
        } 
        .input-icon-bg { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 10px; flex-shrink: 0; }
        
        .icon-name { background: #e0f2fe; color: #0ea5e9; }
        .icon-phone { background: #fef3c7; color: #d97706; }
        .icon-mail { background: #f0fdf4; color: #22c55e; }
        .icon-lock { background: #fae8ff; color: #a855f7; }

        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        
        /* Custom Scrollbar for mobile if card is too tall */
        .glass-card::-webkit-scrollbar { width: 0px; }
    </style>
</head>

<!-- h-screen: poori height lega | overflow-hidden: scroll band kar dega -->
<body class="h-screen overflow-hidden flex items-center justify-center p-4">

    <!-- max-h-[95vh] aur overflow-y-auto isliye taki agar screen choti ho to sirf card scroll ho, page nahi -->
    <div class="glass-card w-full max-w-md rounded-[3rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] overflow-y-auto max-h-[95vh] border border-white relative">
        
        <!-- Header Section - Sticky top par rakha hai taki scroll par header dikhta rahe -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 text-center text-white sticky top-0 z-10">
            <div class="bg-white/20 w-16 h-16 rounded-3xl flex items-center justify-center mx-auto mb-4 backdrop-blur-md">
                <i class="fas fa-user-plus text-2xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight">Create Account</h2>
            <p class="text-blue-100 text-sm mt-1">Start your healthy life with us</p>
        </div>

        <div class="p-8 pt-6">
            <?php if(isset($error)): ?>
                <div class="flex items-center gap-3 bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold p-3 rounded-2xl mb-6">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-5">
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Full Name</label>
                    <div class="flex items-center gap-3">
                        <div class="input-icon-bg icon-name"><i class="fa-solid fa-user-tag text-sm"></i></div>
                        <input type="text" name="name" required placeholder="Name" 
                               class="w-full p-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Mobile Number</label>
                    <div class="flex items-center gap-3">
                        <div class="input-icon-bg icon-phone"><i class="fa-solid fa-phone-flip text-sm"></i></div>
                        <input type="tel" name="mobile" required placeholder="+91 00000 00000" 
                               class="w-full p-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Email Address</label>
                    <div class="flex items-center gap-3">
                        <div class="input-icon-bg icon-mail"><i class="fa-solid fa-envelope-open-text text-sm"></i></div>
                        <input type="email" name="email" required placeholder="example@mail.com" 
                               class="w-full p-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Password</label>
                        <div class="relative flex items-center gap-3">
                            <div class="input-icon-bg icon-lock"><i class="fa-solid fa-shield-halved text-sm"></i></div>
                            <div class="relative flex-1">
                                <input type="password" id="password" name="password" required placeholder="••••••••" 
                                       class="w-full p-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:bg-white outline-none transition-all pr-10">
                                <button type="button" onclick="togglePass('password', 'eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 hover:text-purple-600 transition">
                                    <i id="eye1" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Confirm</label>
                        <div class="relative">
                            <input type="password" id="cpassword" name="cpassword" required placeholder="••••••••" 
                                   class="w-full p-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:bg-white outline-none transition-all pr-10">
                            <button type="button" onclick="togglePass('cpassword', 'eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 hover:text-purple-600 transition">
                                <i id="eye2" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button name="register" type="submit" class="w-full bg-blue-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-[1.5rem] shadow-xl shadow-blue-200 transform active:scale-[0.98] transition-all mt-6 mb-2">
                    Create My Account
                </button>
            </form>

            <div class="mt-4 pb-4 text-center">
                <p class="text-slate-400 text-sm font-medium">Already have an account?</p>
                <a href="login.php" class="text-blue-600 font-extrabold hover:text-indigo-700 transition-colors mt-1 inline-block">Sign In Instead</a>
            </div>
        </div>
    </div>

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