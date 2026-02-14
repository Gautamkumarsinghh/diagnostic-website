<?php
include '../db/config.php';

$id=$_GET['id'];
$s=$_GET['s'];

$new = ($s=='Pending') ? 'Completed' : 'Pending';

mysqli_query($conn,"UPDATE bookings SET status='$new' WHERE id='$id'");

header("Location:index.php");
?>
