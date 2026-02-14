<?php
include '../db/config.php';

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // PHP variables handle karna
    $pass = $_POST['password']; // Yahan input name 'password' hona chahiye
    $cpass = $_POST['cpassword'];

    // चेक करें कि पासवर्ड मैच हो रहे हैं या नहीं
    if($pass !== $cpass){
        $error = "पासवर्ड मैच नहीं हुआ!"; // Screenshot ke hisab se Hindi message
    } else {
        // चेक करें ईमेल पहले से तो नहीं है
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0){
            $error = "Email already registered!";
        } else {
            // पासवर्ड सुरक्षित करें (Hash)
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
    <title>Register | My Diagnostic Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0f7ff; } </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 py-10">

    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border">
        <!-- Header -->
        <div class="bg-blue-600 p-6 text-center text-white">
            <h2 class="text-2xl font-bold">Create Account</h2>
            <p class="text-blue-100 text-sm">Join us for a better health experience</p>
        </div>

        <div class="p-8">
            <?php if(isset($error)) { echo "<p class='text-red-500 text-sm mb-4 text-center bg-red-50 p-2 rounded'>$error</p>"; } ?>

            <form method="post" class="space-y-4">
                <div>
                    <label class="block text-[12px] font-bold text-gray-500 uppercase ml-1">Full Name</label>
                    <input type="text" name="name" required class="w-full p-3 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-gray-500 uppercase ml-1">Mobile Number</label>
                    <input type="tel" name="mobile" required class="w-full p-3 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-gray-500 uppercase ml-1">Email Address</label>
                    <input type="email" name="email" required class="w-full p-3 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-bold text-gray-500 uppercase ml-1">Password</label>
                        <!-- Pehle yahan name="passwords" tha, ab 'password' hai -->
                        <input type="password" name="password" placeholder="••••••••" required class="w-full p-3 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-gray-500 uppercase ml-1">Confirm</label>
                        <input type="password" name="cpassword" placeholder="••••••••" required class="w-full p-3 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>

                <button name="register" type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 transition mt-4">
                    Register Now
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-500 text-sm">Already have an account? <a href="login.php" class="text-blue-600 font-bold hover:underline">Login Here</a></p>
            </div>
        </div>
    </div>

</body>
</html>