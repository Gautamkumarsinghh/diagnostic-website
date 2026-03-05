<?php
include '../db/config.php';
session_start();

if(!isset($_SESSION['admin'])) {
    die("Unauthorized");
}

if(isset($_FILES['report']) && isset($_POST['booking_id'])) {
    $id = $_POST['booking_id'];
    $target_dir = "../uploads/reports/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES["report"]["name"], PATHINFO_EXTENSION);
    $new_filename = "REPORT_" . $id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["report"]["tmp_name"], $target_file)) {
        $q = "UPDATE bookings SET report_file = '$new_filename', status = 'Completed' WHERE id = '$id'";
        if(mysqli_query($conn, $q)) {
            echo "Success";
        } else {
            echo "DB Error";
        }
    } else {
        echo "Upload Error";
    }
}
?>
