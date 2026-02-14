<?php
include '../db/config.php';

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=bookings.csv");
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen("php://output", "w");

fputcsv($output, ['ID','Name','Mobile','Test','Status']);

$q = mysqli_query($conn,"SELECT * FROM bookings");

while($row=mysqli_fetch_assoc($q)){
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['mobile'],
        $row['test'],
        $row['status']
    ]);
}

fclose($output);
exit;
?>
