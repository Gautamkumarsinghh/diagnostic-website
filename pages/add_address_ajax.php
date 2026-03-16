<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../db/config.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Not logged in']));
}

$user_id = $_SESSION['user_id'];
$title = $_POST['title'] ?? 'Home';
$address = $_POST['address'] ?? '';
$landmark = $_POST['landmark'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$mobile = $_POST['mobile'] ?? ($_SESSION['user_mobile'] ?? '');

// If still empty, fetch from database as a final fallback
if (empty($mobile)) {
    $user_q = mysqli_query($conn, "SELECT mobile FROM users WHERE id = '$user_id'");
    if ($user_q && $u = mysqli_fetch_assoc($user_q)) {
        $mobile = $u['mobile'];
        $_SESSION['user_mobile'] = $mobile; // Update session for next time
    }
}

$lat = $_POST['lat'] ?? 0;
$lng = $_POST['lng'] ?? 0;

if (empty($address) || empty($pincode)) {
    die(json_encode(['status' => 'error', 'message' => 'Please fill all required fields (Address & Pincode)']));
}

$sql = "INSERT INTO user_addresses (user_id, title, address_line, landmark, pincode, mobile, latitude, longitude) 
        VALUES ('$user_id', '$title', '$address', '$landmark', '$pincode', '$mobile', '$lat', '$lng')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success', 'message' => 'Address saved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
}
?>
