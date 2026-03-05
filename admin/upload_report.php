<?php
session_start();
include '../db/config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['report_file'])){
    $booking_id = (int)$_POST['booking_id'];
    
    // Check for errors
    if($_FILES['report_file']['error'] == 0){
        $file_name = $_FILES['report_file']['name'];
        $file_tmp = $_FILES['report_file']['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if($ext === 'pdf'){
            // Ensure uploads directory exists
            $upload_dir = '../uploads/reports/';
            if(!is_dir($upload_dir)){
                mkdir($upload_dir, 0777, true);
            }
            
            $new_file_name = 'report_' . $booking_id . '_' . time() . '.pdf';
            $dest = $upload_dir . $new_file_name;
            
            if(move_uploaded_file($file_tmp, $dest)){
                // Save to db
                mysqli_query($conn, "UPDATE bookings SET report_file='$new_file_name' WHERE id=$booking_id");
                
                echo "<script>alert('Report uploaded successfully!'); window.location.href='index.php';</script>";
            }else{
                echo "<script>alert('Error uploading file.'); window.location.href='index.php';</script>";
            }
        } else {
            echo "<script>alert('Only PDF files are allowed!'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('File upload error!'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>
