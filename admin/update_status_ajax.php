<?php
include '../db/config.php';
session_start();

if(!isset($_SESSION['admin'])) {
    die("Unauthorized");
}

$id = $_POST['id'];
$status = $_POST['status'];

$q = "UPDATE bookings SET status = '$status' WHERE id = '$id'";
if(mysqli_query($conn, $q)) {
    echo "Success";
} else {
    echo "Error";
}
?>
