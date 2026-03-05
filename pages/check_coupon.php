<?php
include '../db/config.php'; // Corrected path
header('Content-Type: application/json');

$code = mysqli_real_escape_string($conn, $_POST['code'] ?? '');
$amount = intval($_POST['amount'] ?? 0);

if(empty($code)) {
    echo json_encode(['status' => 'error', 'message' => 'Enter a coupon code.']);
    exit;
}

// Fixed column names to match the database schema (discount_percent, status)
$q = mysqli_query($conn, "SELECT * FROM coupons WHERE code = '$code' AND status = 'Active'");

if($row = mysqli_fetch_assoc($q)) {
    $percent = intval($row['discount_percent']);
    $discount = floor($amount * ($percent / 100));
    echo json_encode(['status' => 'success', 'discount' => $discount, 'message' => 'Coupon applied! You saved '.$percent.'% (₹'.$discount.')']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or inactive coupon code.']);
}
?>

