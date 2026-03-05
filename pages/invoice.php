<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Fetch booking data securely mapping to this user
$query = mysqli_query($conn, "SELECT * FROM bookings WHERE id=$id AND user_id='$user_id'");
if(mysqli_num_rows($query) == 0){
    die("Invalid Invoice or Unauthorized Access.");
}
$booking = mysqli_fetch_assoc($query);

// Actual Pricing Logic
$total_paid = (float)$booking['amount'];
$discount = (float)($booking['discount_amount'] ?? 0);

// Total before discount = Base + GST
$total_before_discount = $total_paid + $discount;

// Reverse calculate GST (Total = Base * 1.18)
$base_price = $total_before_discount / 1.18;
$tax = $base_price * 0.18;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $booking['id']; ?> | MyLab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            background: white;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            border-radius: 1.5rem;
            overflow: hidden;
        }
        @media print {
            body { background: white; }
            .invoice-box { box-shadow: none; max-width: 100%; border-radius: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="py-10 px-4">

    <!-- Action Buttons -->
    <div class="max-w-[800px] mx-auto flex justify-between items-center mb-6 no-print">
        <a href="user-bookings.php" class="text-slate-500 hover:text-slate-800 font-bold flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
            <i class="fas fa-print"></i> Print Invoice
        </button>
    </div>

    <div class="invoice-box p-10 relative">
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-slate-100 pb-8 mb-8">
            <div class="flex items-center gap-4">
               <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-microscope text-3xl"></i>
               </div>
               <div>
                   <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">MyLab<span class="text-blue-600">.</span></h1>
                   <p class="text-sm text-slate-500 font-medium">Professional Diagnostics</p>
               </div>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-black text-slate-200 uppercase tracking-widest mb-2">Invoice</h2>
                <div class="text-sm font-bold text-slate-400">#INV-<?php echo date('Y', strtotime($booking['created_at'])) . '-' . str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></div>
                <div class="text-xs text-slate-400 mt-1">Date: <?php echo date('d M Y', strtotime($booking['created_at'])); ?></div>
            </div>
        </div>

        <!-- Bill To / From -->
        <div class="flex flex-col sm:flex-row justify-between gap-10 mb-10">
            <div>
                <h3 class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-3">Invoice To:</h3>
                <div class="font-bold text-lg text-slate-800 mb-1"><?php echo htmlspecialchars($booking['name']); ?></div>
                <div class="text-sm text-slate-500 font-medium mb-1"><i class="fas fa-phone mr-2 text-slate-300"></i> +91 <?php echo htmlspecialchars($booking['mobile']); ?></div>
                <div class="text-sm text-slate-500 font-medium"><i class="fas fa-envelope mr-2 text-slate-300"></i> <?php echo htmlspecialchars($booking['email']); ?></div>
            </div>
            <div class="sm:text-right">
                <h3 class="text-[10px] font-black tracking-widest text-slate-400 uppercase mb-3">Pay To:</h3>
                <div class="font-bold text-lg text-slate-800 mb-1">MyLab Diagnostics Pvt Ltd.</div>
                <div class="text-sm text-slate-500 font-medium mb-1">123 Healthway Avenue</div>
                <div class="text-sm text-slate-500 font-medium">New Delhi, India - 110001</div>
            </div>
        </div>

        <!-- Table -->
        <div class="mb-10 text-sm border border-slate-100 rounded-2xl overflow-hidden">
            <div class="bg-slate-50 grid grid-cols-12 gap-4 p-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">
                <div class="col-span-8">Description</div>
                <div class="col-span-2 text-center">Qty</div>
                <div class="col-span-2 text-right">Amount</div>
            </div>
            <div class="grid grid-cols-12 gap-4 p-5 items-center border-t border-slate-50">
                <div class="col-span-8">
                    <span class="font-bold text-slate-800 text-base"><?php echo htmlspecialchars($booking['test']); ?></span>
                    <div class="text-slate-500 mt-1">Home sample collection, processing & digital reporting</div>
                </div>
                <div class="col-span-2 text-center font-bold text-slate-600">1</div>
                <div class="col-span-2 text-right font-bold text-slate-800">₹<?php echo number_format($base_price, 2); ?></div>
            </div>
        </div>

        <!-- Totals -->
        <div class="flex justify-end border-t border-slate-100 pt-8 mt-8">
            <div class="w-full max-w-xs space-y-4">
                <div class="flex justify-between text-sm font-medium text-slate-500">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($base_price, 2); ?></span>
                </div>
                <div class="flex justify-between text-sm font-medium text-slate-500">
                    <span>GST (18%)</span>
                    <span>₹<?php echo number_format($tax, 2); ?></span>
                </div>
                <?php if($discount > 0): ?>
                <div class="flex justify-between text-sm font-bold text-green-600 bg-green-50 p-2 rounded-lg">
                    <span>Discount (Coupon)</span>
                    <span>- ₹<?php echo number_format($discount, 2); ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between text-xl font-black text-slate-800 pt-4 border-t border-slate-100">
                    <span>Total Amount</span>
                    <span class="text-blue-600">₹<?php echo number_format($total_paid, 2); ?></span>
                </div>
                
                <?php if($booking['payment_status'] === 'Success'): ?>
                <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i> Payment Received Successfully
                </div>
                <?php else: ?>
                <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs font-bold text-amber-500 uppercase tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-clock"></i> Payment Pending (Cash on Delivery)
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-16 pt-8 border-t border-slate-100 text-center text-xs text-slate-400 font-medium">
            <p>Thank you for trusting MyLab Diagnostics with your health.</p>
            <p class="mt-1">If you have any questions regarding this invoice, please contact support@mylab.com</p>
        </div>
        
        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-bl-full -z-10"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-indigo-500/5 rounded-tr-full -z-10"></div>
    </div>

</body>
</html>
