<?php
session_start();
include '../db/config.php';

// Ensure booking_date column exists
$check_date = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'booking_date'");
if(mysqli_num_rows($check_date) == 0) {
    mysqli_query($conn, "ALTER TABLE bookings ADD COLUMN booking_date DATE AFTER test");
}

// Receive data via POST from AJAX
$name   = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$mobile = mysqli_real_escape_string($conn, $_POST['mobile'] ?? '');
$test   = mysqli_real_escape_string($conn, $_POST['test'] ?? '');
$booking_date = mysqli_real_escape_string($conn, $_POST['booking_date'] ?? date('Y-m-d'));
$email  = $_SESSION['user_email'] ?? ''; // From session
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

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

$transaction_id = 'MT' . time() . rand(1000, 9999); // Unique Merchant Transaction ID

if ($payment_method === 'COD') {
    $payment_status = 'Pending';
    // Insert into Database
    $query = "INSERT INTO bookings (user_id, name, mobile, test, booking_date, timeslot, email, status, prescription_file, payment_status, transaction_id, amount, discount_amount, coupon_used, payment_method) 
              VALUES ('$user_id', '$name', '$mobile', '$test', '$booking_date', '$timeslot', '$email', 'Booked', '$prescription_file', '$payment_status', '$transaction_id', '$amount', '$discount_amt', '$coupon_used', 'COD')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success", "type" => "COD"]);
    } else {
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }

} else {
    // --- PHONEPE ONLINE PAYMENT (UAT SANDBOX) ---
    $payment_status = 'Initiated';
    
    // 1. Insert Booking as Initiated
    $query = "INSERT INTO bookings (user_id, name, mobile, test, booking_date, timeslot, email, status, prescription_file, payment_status, transaction_id, amount, discount_amount, coupon_used, payment_method) 
              VALUES ('$user_id', '$name', '$mobile', '$test', '$booking_date', '$timeslot', '$email', 'Booked', '$prescription_file', '$payment_status', '$transaction_id', '$amount', '$discount_amt', '$coupon_used', 'Online')";
    
    if (mysqli_query($conn, $query)) {
        
        // 2. Call PhonePe API
        $merchantId = 'PGTESTPAYUAT86'; // New Sandbox Merchant ID
        $saltKey = '96434309-7796-489d-8924-ab56988a6076'; // Corresponding Salt Key
        $saltIndex = 1;

        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $callback_url = $base_url . '/you/pages/phonepe_callback.php';

        $payload = [
            'merchantId' => $merchantId,
            'merchantTransactionId' => $transaction_id,
            'merchantUserId' => 'MUID' . $user_id,
            'amount' => round($amount * 100), // convert to paise
            'redirectUrl' => $callback_url,
            'redirectMode' => 'POST',
            'callbackUrl' => $callback_url,
            'mobileNumber' => $mobile,
            'paymentInstrument' => [
                'type' => 'PAY_PAGE'
            ]
        ];

        $encode = base64_encode(json_encode($payload));
        $string = $encode . '/pg/v1/pay' . $saltKey;
        $sha256 = hash('sha256', $string);
        $final_x_header = $sha256 . '###' . $saltIndex;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(['request' => $encode]),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-VERIFY: " . $final_x_header,
                "accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo json_encode(["status" => "error", "message" => "cURL Error: " . $err]);
        } else {
            $res = json_decode($response);
            if(isset($res->success) && $res->success == '1'){
                // Success - Get redirect URL
                $payUrl = $res->data->instrumentResponse->redirectInfo->url;
                echo json_encode(["status" => "success", "type" => "ONLINE", "redirect_url" => $payUrl]);
            } else {
                echo json_encode(["status" => "error", "message" => "PhonePe Error: " . ($res->message ?? 'Unknown error')]);
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }
}
?>
