<?php
include '../db/config.php';

$id=$_GET['id'];

mysqli_query($conn,"DELETE FROM packages WHERE id='$id'");

header("Location:packages.php");
?>
