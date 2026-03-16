<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../db/config.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Not logged in']));
}

$user_id = $_SESSION['user_id'];
$address_id = $_POST['id'] ?? 0;

if (!$address_id) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid address ID']));
}

$sql = "DELETE FROM user_addresses WHERE id = '$address_id' AND user_id = '$user_id'";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success', 'message' => 'Address deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
}
?>
