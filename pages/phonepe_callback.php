<?php
session_start();
include '../db/config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    header('Content-Type: text/html'); // Prevent JSON download issues on some browsers

    $transactionId = $_POST['transactionId'] ?? '';
    // The status code returned by PhonePe 
    // Examples: 'PAYMENT_SUCCESS', 'PAYMENT_ERROR', 'PAYMENT_PENDING'
    $code = $_POST['code'] ?? ''; 
    $merchantId = $_POST['merchantId'] ?? '';

    // Important: In a real production environment with PhonePe Live mode,
    // you must verify the signature/checksum to ensure that this request 
    // actually originated from PhonePe and hasn't been tampered with.
    // We are simulating Sandbox verification here.

    if(!empty($transactionId)){
        if($code === 'PAYMENT_SUCCESS'){
            $payment_status = 'Success';
            $alert_icon = '✅';
            $alert_msg = 'Payment Successful! Your test booking has been confirmed.';
        } else {
            $payment_status = 'Failed'; // or 'Cancelled' depending on exact scenario
            $alert_icon = '❌';
            $alert_msg = 'Payment Failed or Cancelled. Please try again.';
        }
        
        // Update database with final payment status
        $q = mysqli_query($conn, "UPDATE bookings SET payment_status='$payment_status' WHERE transaction_id='$transactionId'");
        
        if($q){
            // Return to the index displaying the relevant message
            echo "<script>
                    alert('$alert_icon $alert_msg');
                    window.location.href='../index.php';
                  </script>";
        } else {
            echo "Database error during callback processing.";
        }
    } else {
        echo "Invalid callback parameters from PhonePe gateway.";
    }
} else {
    echo "Direct access to callback URL is restricted.";
}
?>
