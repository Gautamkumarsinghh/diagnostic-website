<?php
session_start();
include '../db/config.php';

if(!isset($_SESSION['customer'])){
header("Location: ../pages/login.php");
exit();
}

$email = $_SESSION['customer'];

$q = mysqli_query($conn,"SELECT * FROM bookings WHERE email='$email'");
?>

<!DOCTYPE html>
<html>
<head>
<title>User Dashboard</title>

<style>
body{margin:0;font-family:Arial;background:#f4f6f9}

.sidebar{
width:220px;
height:100vh;
background:#0d6efd;
color:white;
position:fixed;
padding:20px;
}

.sidebar a{
display:block;
color:white;
padding:10px;
text-decoration:none;
margin:10px 0;
background:rgba(255,255,255,.2);
border-radius:5px;
}

.main{
margin-left:240px;
padding:30px;
}

table{
width:100%;
background:white;
border-collapse:collapse;
}

th{
background:#0d6efd;
color:white;
padding:10px;
}

td{
padding:10px;
border:1px solid #ddd;
text-align:center;
}

.btn{
background:#198754;
color:white;
padding:5px 10px;
border-radius:5px;
text-decoration:none;
}
</style>
</head>

<body>

<div class="sidebar">
<h3>👤 Customer</h3>
<a href="../index.php">🏠 Home</a>
<a href="dashboard.php">📄 My Bookings</a>
<a href="report.php">⬇ Download Report</a>
<a href="logout.php">🚪 Logout</a>
</div>

<div class="main">

<h2>My Bookings</h2>

<table>
<tr>
<th>ID</th>
<th>Test</th>
<th>Status</th>
<th>Date</th>
</tr>

<?php while($row=mysqli_fetch_assoc($q)){ ?>

<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['test'] ?></td>
<td><?= $row['status'] ?></td>
<td><?= $row['created_at'] ?></td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>
