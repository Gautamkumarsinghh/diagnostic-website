<?php
include 'db/config.php';
$res = mysqli_query($conn, "DESCRIBE bookings");
echo "<pre>";
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
echo "</pre>";
?>
