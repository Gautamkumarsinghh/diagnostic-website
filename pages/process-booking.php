<?php
session_start();
include 'db/config.php'; // Database connection

if (isset($_POST['book_now'])) {
    // 1. Check karein ki user login hai ya nahi
    if (!isset($_SESSION['user_id'])) {
        header("Location: pages/login.php");
        exit();
    }

    // 2. Data taiyar karein
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name']; // Agar session mein hai
    $user_email = $_SESSION['user_email']; // Agar session mein hai
    $test_name = $_POST['test_name'];
    $status = "Pending"; // Automatic default status

    // 3. Database mein Insert karein
    // Aapki table structure ke hisaab se columns: user_id, name, test, status, email
    $query = "INSERT INTO bookings (user_id, name, test, status, email) 
              VALUES ('$user_id', '$user_name', '$test_name', '$status', '$user_email')";

    if (mysqli_query($conn, $query)) {
        // 4. Success hone par seedha "My Bookings" page par bhej dein
        header("Location: pages/user-bookings.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>