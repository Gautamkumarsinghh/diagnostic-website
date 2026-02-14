<?php
session_start();
include '../db/config.php';

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($q) > 0){
        $user = mysqli_fetch_assoc($q);
        // पासवर्ड चेक करें
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0f7ff; } </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border">
        <!-- Header -->
        <div class="bg-blue-600 p-8 text-center text-white">
            <div class="bg-white w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-blue-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold">Welcome Back</h2>
            <p class="text-blue-100 text-sm">Login to your health portal</p>
        </div>

        <div class="p-8">
            <?php if(isset($error)) { echo "<p class='text-red-500 text-sm mb-4 text-center bg-red-50 p-2 rounded'>$error</p>"; } ?>

            <form method="post" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" placeholder="name@example.com" required class="w-full p-3.5 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                <!-- Password Field with Show/Hide Button -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordInput" placeholder="••••••••" required 
                               class="w-full p-3.5 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition pr-12">
                        
                        <!-- Toggle Icon -->
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition">
                            <i id="eyeIcon" class="fas fa-eye text-lg"></i>
                        </button>
                    </div>
                </div>

                <button name="login" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 transition transform active:scale-95">
                    Sign In
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-500 text-sm">Don't have an account?</p>
                <a href="register.php" class="text-blue-600 font-bold hover:underline">Create Account</a>
            </div>
        </div>
    </div>

    <!-- JavaScript for Show/Hide Toggle -->
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