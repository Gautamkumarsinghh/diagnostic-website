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
    <title>Admin Login | MyLab</title>
    
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #0d6efd;
            --primary-dark: #0a58ca;
            --bg-gradient: linear-gradient(135deg, #0d6efd 0%, #4facfe 100%);
            --text-main: #2d3436;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.98);
            padding: 50px 40px;
            width: 100%;
            max-width: 400px;
            border-radius: 28px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .logo-area { margin-bottom: 25px; }
        .logo-area span {
            font-size: 34px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -1px;
        }

        .login-container h2 {
            font-weight: 700;
            color: var(--text-main);
            margin: 0 0 8px 0;
            font-size: 24px;
        }

        .login-container p {
            color: #636e72;
            font-size: 14px;
            margin-bottom: 35px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #636e72;
            margin-bottom: 8px;
            padding-left: 5px;
        }

        /* Password Wrapper for Eye Icon */
        .password-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #edf2f7;
            border-radius: 12px;
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            outline: none;
            background: #fcfcfc;
        }

        input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        /* Eye Icon Styling */
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #b2bec3;
            font-size: 18px;
            transition: color 0.3s;
            border: none;
            background: none;
            padding: 5px;
        }

        .toggle-password:hover {
            color: var(--primary);
        }

        button.login-btn {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2);
        }

        button.login-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(13, 110, 253, 0.3);
        }

        .error-msg {
            background: #fff5f5;
            color: #c0392b;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #feb2b2;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-text {
            margin-top: 30px;
            font-size: 11px;
            color: #b2bec3;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo-area">
            <span>MyLab<span style="color: #4facfe;">.</span> Admin</span>
        </div>

        <h2>Welcome Back</h2>
        <p>Enter your credentials to manage dashboard</p>

        <?php if(isset($error)): ?>
            <div class="error-msg">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="e.g. admin_mylab" required autofocus>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                    <!-- Eye Icon Toggle -->
                    <button type="button" class="toggle-password" onclick="togglePass()">
                        <i id="eye-icon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" name="login" class="login-btn">Login to Dashboard</button>
        </form>

        <div class="footer-text">Secure Server Access © 2026</div>
    </div>

    <script>
        function togglePass() {
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>