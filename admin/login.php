<?php
session_start();
include '../db/config.php';

if(isset($_POST['login'])){
    // Security improvement: escaping inputs
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = md5($_POST['password']); // Note: password_hash() is recommended over md5

    $q = mysqli_query($conn, "SELECT * FROM admin WHERE username='$user' AND password='$pass'");

    if(mysqli_num_rows($q) > 0){
        $_SESSION['admin'] = $user;
        header("Location:index.php");
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
    <!-- Google Fonts for modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
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
            background: rgba(255, 255, 255, 0.95);
            padding: 50px 40px;
            width: 100%;
            max-width: 400px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .logo-area {
            margin-bottom: 30px;
        }

        .logo-area span {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -1px;
        }

        .login-container h2 {
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
            font-size: 24px;
        }

        .login-container p {
            color: #636e72;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #636e72;
            margin-bottom: 8px;
            margin-left: 5px;
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
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        button {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .error-msg {
            background: #fff5f5;
            color: #c0392b;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border-left: 4px solid #c0392b;
        }

        .footer-text {
            margin-top: 30px;
            font-size: 12px;
            color: #b2bec3;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo-area">
            <span>MyLab<span style="color: #4facfe;">.</span></span>
        </div>

        <h2>Admin Access</h2>
        <p>Please enter your credentials to continue.</p>

        <?php if(isset($error)): ?>
            <div class="error-msg">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="e.g. admin_mylab" required autofocus>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" name="login">Login to Dashboard</button>
        </form>

        <div class="footer-text">Secure Admin Portal © 2024</div>
    </div>

</body>
</html>