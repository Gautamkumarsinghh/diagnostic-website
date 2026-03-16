<?php
session_start();
include '../db/config.php';

// Received via POST from AJAX
$name   = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$mobile = mysqli_real_escape_string($conn, $_POST['mobile'] ?? '');
$test   = mysqli_real_escape_string($conn, $_POST['test'] ?? '');
$booking_date = mysqli_real_escape_string($conn, $_POST['booking_date'] ?? date('Y-m-d'));
$email  = $_SESSION['user_email'] ?? ''; 
$user_id = $_SESSION['user_id'] ?? 0; 

// Server side validation for mobile
if(!preg_match('/^[0-9]{10}$/', $mobile)){
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Mobile number must be exactly 10 digits!"]);
    exit;
}
$amount = (float)($_POST['amount'] ?? 0);
$payment_method = $_POST['payment_method'] ?? 'Online';
$timeslot = mysqli_real_escape_string($conn, $_POST['timeslot'] ?? '');
$discount_amt = (float)($_POST['discount_amt'] ?? 0);
$coupon_used = mysqli_real_escape_string($conn, $_POST['applied_coupon'] ?? '');

$prescription_file = NULL;

if (isset($_FILES['prescription']) && $_FILES['prescription']['error'] == 0) {
    $ext = strtolower(pathinfo($_FILES['prescription']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    if (in_array($ext, $allowed)) {
        $upload_dir = '../uploads/prescriptions/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $new_name = 'presc_' . time() . '_' . rand(100, 999) . '.' . $ext;
        if (move_uploaded_file($_FILES['prescription']['tmp_name'], $upload_dir . $new_name)) {
            $prescription_file = $new_name;
        }
    }
}

$transaction_id = 'MT' . time() . rand(1000, 9999); 

// Clear any previous output buffers to ensure clean JSON
if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

if ($payment_method === 'COD') {
    $payment_status = 'Pending';
    $query = "INSERT INTO bookings (user_id, name, mobile, test, booking_date, timeslot, email, status, prescription_file, payment_status, transaction_id, amount, discount_amount, coupon_used, payment_method) 
              VALUES ('$user_id', '$name', '$mobile', '$test', '$booking_date', '$timeslot', '$email', 'Booked', '$prescription_file', '$payment_status', '$transaction_id', '$amount', '$discount_amt', '$coupon_used', 'COD')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success", "type" => "COD"]);
    } else {
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }
    exit;

} else {
    // --- MANUAL ONLINE PAYMENT ---
    $payment_status = 'Verification Pending';
    $utr_number = mysqli_real_escape_string($conn, $_POST['utr_number'] ?? '');
    
    $payment_proof = NULL;
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($ext, $allowed)) {
            $upload_dir = '../uploads/payments/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $new_name = 'pay_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $upload_dir . $new_name)) {
                $payment_proof = $new_name;
            }
        }
    }
    
    $query = "INSERT INTO bookings (user_id, name, mobile, test, booking_date, timeslot, email, status, prescription_file, payment_status, transaction_id, amount, discount_amount, coupon_used, payment_method, utr_number, payment_proof) 
              VALUES ('$user_id', '$name', '$mobile', '$test', '$booking_date', '$timeslot', '$email', 'Booked', '$prescription_file', '$payment_status', '$transaction_id', '$amount', '$discount_amt', '$coupon_used', 'Online', '$utr_number', '$payment_proof')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success", "type" => "MANUAL_ONLINE"]);
    } else {
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }
    exit;
}
