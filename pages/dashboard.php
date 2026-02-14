<?php
session_start();
include '../db/config.php';

if(!isset($_SESSION['customer'])){
header("Location:login.php");
}

$email=$_SESSION['customer'];

$q=mysqli_query($conn,"SELECT * FROM bookings WHERE email='$email'");
?>

<h2>My Bookings</h2>

<table border="1" cellpadding="10">

<tr>
<th>ID</th>
<th>Name</th>
<th>Mobile</th>
<th>Test</th>
<th>Date</th>
<th>Status</th>
</tr>

<?php while($row=mysqli_fetch_assoc($q)){ ?>

<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['mobile']; ?></td>
<td><?php echo $row['test']; ?></td>
<td><?php echo $row['date']; ?></td>
<td><?php echo $row['status']; ?></td>
</tr>

<?php } ?>

</table>

<br>

<a href="logout.php">Logout</a>
