<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../db/config.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Get test name from URL (can be multiple separated by commas)
$raw_test = $_GET['test'] ?? '';

// We need to fetch prices for ALL packages to calculate total in JavaScript if needed, but also specifically for the selected ones
$packages_query = mysqli_query($conn, "SELECT name, price FROM packages ORDER BY name ASC");
$packages = [];
while($row = mysqli_fetch_assoc($packages_query)) {
    $packages[] = $row;
}

$selected_tests_array = [];
if (!empty($raw_test)) {
    // Split by comma in case multiple tests were sent
    $selected_tests_array = array_map('trim', explode(',', $raw_test));
}

// If multiple tests were selected, we will create a dummy 'Combined Package' option for them in the dropdown
$is_multiple = count($selected_tests_array) > 1;
$combined_test_name = "";
$combined_price = 0;

if ($is_multiple) {
    $combined_test_name = implode(', ', $selected_tests_array);
    // Calculate combined price by checking against all packages (or fallback to a default if not found, though ideally we pass prices or fetch them via ajax. For simplicity we assume prices are in the DB)
    foreach ($selected_tests_array as $t_name) {
        $found = false;
        foreach ($packages as $pkg) {
            if ($pkg['name'] == $t_name) {
                $combined_price += $pkg['price'];
                $found = true;
                break;
            }
        }
        // Fallback for scans like Ultrasound which might not be in packages table
        if(!$found) {
             if(strpos($t_name, 'Ultrasound') !== false) $combined_price += 500;
             else if(strpos($t_name, 'X-Ray') !== false) $combined_price += 300;
             else if(strpos($t_name, 'ECG') !== false) $combined_price += 200;
             else if(strpos($t_name, 'Echocardiogram') !== false) $combined_price += 149;
        }
    }
} else {
    $selected_test = $selected_tests_array[0] ?? '';
}

// Fetch active bank accounts
$bank_query = mysqli_query($conn, "SELECT * FROM bank_accounts WHERE is_active = 1 ORDER BY id DESC");
$bank_accounts = [];
if ($bank_query) {
    while($r = mysqli_fetch_assoc($bank_query)){
        $bank_accounts[] = $r;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Test | MyLab Diagnostic</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- We are using a Mock Payment Gateway for demonstration purposes -->

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .step-hidden { display: none; }
        .step-active { display: block; animation: slideIn 0.4s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        
        /* New Style for Instruction Box */
        .health-tip { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-left: 4px solid #3b82f6; }
        .toggle-btn.active { background-color: #2563eb; color: white; border-color: #2563eb; }
    </style>
</head>

<body class="flex flex-col min-h-screen overflow-x-hidden">

    <!-- Header Inclusion -->
    <?php include '../header.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-grow flex items-center justify-center p-6 py-12 relative overflow-hidden">
        
        <!-- Abstract Background -->
        <div class="absolute inset-0 z-0 opacity-40">
            <div class="absolute -top-[20%] right-[10%] w-[40%] h-[50%] rounded-full bg-blue-300 blur-[120px]"></div>
            <div class="absolute top-[40%] -left-[10%] w-[50%] h-[50%] rounded-full bg-cyan-300 blur-[130px]"></div>
        </div>

        <div class="glass-card w-full max-w-xl rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.08)] overflow-hidden border border-white/60 relative z-10 transition-all hover:shadow-[0_20px_60px_rgba(0,0,0,0.12)]">
            
    <!-- Dynamic Header -->
    <div class="bg-white border-b border-gray-100 sticky top-[72px] z-30 py-4 px-4 sm:px-6 shadow-sm">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-800 tracking-tight">Booking Portal</h1>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]" id="stepName">Step 1: Patient Details</p>
                    </div>
                </div>
                <!-- Mini Progress -->
                <div class="flex items-center gap-1.5 h-1.5">
                    <div id="dot1" class="step-dot w-6 h-full rounded-full bg-blue-600 transition-all duration-300"></div>
                    <div id="dot2" class="step-dot w-2 h-full rounded-full bg-gray-200 transition-all duration-300"></div>
                    <div id="dot3" class="step-dot w-2 h-full rounded-full bg-gray-200 transition-all duration-300"></div>
                </div>
            </div>
        </div>
    </div>

            <div class="p-8 md:p-10 bg-white/80">
                <form id="bookingForm" method="post" enctype="multipart/form-data" class="space-y-7">
                    
                    <!-- STEP 1: PATIENT DETAILS -->
                    <div id="step1" class="step-active space-y-7">
                        <!-- Test Selection -->
                        <div class="space-y-2 group">
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-600 transition-colors">Select Diagnostic Test</label>
                            <div class="relative">
                                <i class="fas fa-vial absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                                <select name="test" id="test-select" required onchange="updateHealthGuide()" class="w-full pl-14 pr-12 py-4 bg-gray-50/80 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-gray-800 appearance-none cursor-pointer">
                                    <option value="" disabled <?php if(empty($raw_test)) echo 'selected'; ?>>Choose a Test Package</option>
                                    
                                    <?php if($is_multiple): ?>
                                        <option value="<?php echo htmlspecialchars($combined_test_name); ?>" data-price="<?php echo $combined_price; ?>" selected>
                                            Multiple Tests: <?php echo htmlspecialchars($combined_test_name); ?>
                                        </option>
                                    <?php endif; ?>

                                    <?php foreach($packages as $pkg): ?>
                                        <option value="<?php echo htmlspecialchars($pkg['name']); ?>" 
                                                data-price="<?php echo $pkg['price']; ?>"
                                                <?php if(!$is_multiple && isset($selected_test) && $selected_test == $pkg['name']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($pkg['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    
                                    <!-- Fallbacks for scans so they appear in dropdown if single selected -->
                                    <?php if(!$is_multiple && isset($selected_test) && in_array($selected_test, ['Ultrasound', 'X-Ray', 'ECG', 'Echocardiogram'])): ?>
                                        <option value="<?php echo htmlspecialchars($selected_test); ?>" 
                                                data-price="<?php echo ($selected_test=='Ultrasound'?500:($selected_test=='X-Ray'?300:($selected_test=='ECG'?200:149))); ?>" selected>
                                            <?php echo htmlspecialchars($selected_test); ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                                <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none group-focus-within:text-blue-600 transition-colors"></i>
                            </div>
                        </div>

                        <!-- HEALTH GUIDE (Unique Feature) -->
                        <div id="health-guide" class="health-tip p-4 rounded-xl hidden">
                            <div class="flex gap-3">
                                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                                <div>
                                    <p id="guide-msg" class="text-sm font-semibold text-blue-900 leading-snug"></p>
                                </div>
                            </div>
                        </div>

                        <!-- PATIENT IDENTITY (Unique Feature) -->
                        <div class="space-y-3">
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Booking For?</label>
                            <div class="flex gap-4">
                                <button type="button" id="btn-self" onclick="setIdentity('self')" class="toggle-btn active flex-1 py-3 rounded-xl border border-gray-200 font-bold text-sm transition-all focus:outline-none">Self (Logged In)</button>
                                <button type="button" id="btn-family" onclick="setIdentity('family')" class="toggle-btn flex-1 py-3 rounded-xl border border-gray-200 font-bold text-sm transition-all text-gray-500 focus:outline-none">Family Member</button>
                                <input type="hidden" name="booking_for" id="booking_for" value="self">
                            </div>
                        </div>

                        <!-- Patient Name -->
                        <div class="space-y-2 group">
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-600 transition-colors">Patient Full Name</label>
                            <div class="relative">
                                <i class="fas fa-user-circle absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                                <input type="text" name="name" id="patient_name" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" placeholder="Enter Patient's Name" required 
                                       class="w-full pl-14 pr-5 py-4 bg-gray-50/80 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-gray-800 placeholder-gray-400">
                            </div>
                        </div>

                        <!-- Date & Slot Group -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2 group">
                                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-600 transition-colors">Booking Date</label>
                                <div class="relative">
                                    <i class="fas fa-calendar-day absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                                    <input type="date" name="booking_date" id="booking_date" required min="<?php echo date('Y-m-d'); ?>"
                                           class="w-full pl-14 pr-5 py-4 bg-gray-50/80 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-gray-800">
                                </div>
                            </div>
                            <!-- TIME SLOT (Unique Feature) -->
                            <div class="space-y-2 group">
                                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-600 transition-colors">Preferred Slot</label>
                                <div class="relative">
                                    <i class="fas fa-clock absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                                    <select name="timeslot" id="timeslot" required class="w-full pl-14 pr-12 py-4 bg-gray-50/80 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-gray-800 appearance-none cursor-pointer">
                                        <option value="Morning (07:00 - 10:00)">Morning (07-10 AM)</option>
                                        <option value="Day (10:00 - 02:00)">Day (10-02 PM)</option>
                                        <option value="Evening (03:00 - 07:00)">Evening (03-07 PM)</option>
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none group-focus-within:text-blue-600 transition-colors"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Number -->
                        <div class="space-y-2 group">
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-600 transition-colors">Contact Number</label>
                            <div class="relative">
                                <i class="fas fa-phone-alt absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                                <input type="tel" name="mobile" id="patient_mobile" placeholder="10 Digit Mobile Number" required 
                                       pattern="[0-9]{10}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                       class="w-full pl-14 pr-5 py-4 bg-gray-50/80 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-gray-800 placeholder-gray-400">
                            </div>
                        </div>

                        <!-- Prescription -->
                        <div class="space-y-2 group">
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-600 transition-colors">Upload Prescription (Optional)</label>
                            <div class="relative">
                                <i class="fas fa-file-prescription absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors z-10"></i>
                                <input type="file" name="prescription" accept=".jpg,.jpeg,.png,.pdf"
                                       class="w-full pl-14 pr-4 py-3 bg-gray-50/80 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-gray-800 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                            </div>
                        </div>

                        <button type="button" onclick="goToStep2()" class="w-full bg-blue-600 text-white font-bold text-lg py-5 rounded-2xl shadow-lg hover:bg-blue-700 transition-all flex items-center justify-center gap-3">
                            Confirm Details <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <!-- STEP 2: ORDER CONFIRMATION -->
                    <div id="step2" class="step-hidden space-y-6">
                        <div class="bg-blue-50 border border-blue-100 rounded-3xl p-6 space-y-4">
                            <h3 class="text-blue-800 font-bold flex items-center gap-2 border-b border-blue-100 pb-3">
                                <i class="fas fa-receipt"></i> Order Summary
                            </h3>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Patient Name:</span>
                                <span id="conf-name" class="text-gray-900 font-bold"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Selected Test:</span>
                                <span id="conf-test" class="text-blue-700 font-bold"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Selected Date:</span>
                                <span id="conf-date" class="text-blue-600 font-black"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Time Slot:</span>
                                <span id="conf-slot" class="text-gray-900 font-bold"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Mobile Number:</span>
                                <span id="conf-mobile" class="text-gray-900 font-bold"></span>
                            </div>
                            <div class="pt-4 border-t border-blue-100 space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Package Price:</span>
                                    <span id="conf-subtotal" class="text-gray-900 font-bold"></span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">GST (18%):</span>
                                    <span id="conf-gst" class="text-gray-900 font-bold text-red-500"></span>
                                </div>
                                <div id="discount-row" class="hidden flex justify-between items-center text-sm font-bold text-green-600 bg-green-50/50 p-3 rounded-2xl border border-green-100">
                                    <span class="flex items-center gap-2"><i class="fas fa-tags"></i> Coupon Discount:</span>
                                    <span id="conf-discount"></span>
                                </div>
                                <div class="flex justify-between items-center pt-4 border-t border-blue-100/50">
                                    <span class="text-lg font-extrabold text-blue-900">Total Amount:</span>
                                    <span id="conf-price" class="text-2xl font-black text-blue-600"></span>
                                </div>
                            </div>
                        </div>

                        <!-- COUPON SYSTEM (Unique Feature) -->
                        <div class="space-y-2">
                             <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Have a Coupon?</label>
                             <div class="flex gap-2">
                                 <input type="text" id="coupon_code" placeholder="e.g. HEALTH10" class="flex-1 px-4 py-3 bg-white border border-gray-200 rounded-xl outline-none focus:border-blue-500 font-bold uppercase placeholder:text-gray-300">
                                 <button type="button" onclick="applyCoupon()" class="bg-gray-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-600 transition-colors">Apply</button>
                             </div>
                             <p id="coupon-msg" class="text-[10px] font-bold mt-1"></p>
                             <input type="hidden" name="applied_coupon" id="applied_coupon" value="">
                             <input type="hidden" name="discount_amt" id="discount_amt" value="0">
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="space-y-2 group">
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Choose Payment Method</label>
                            <div class="relative">
                                <i class="fas fa-wallet absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                                <select name="payment_method" id="payment_method" required class="w-full pl-14 pr-12 py-4 bg-gray-50/80 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-gray-800 appearance-none cursor-pointer">
                                    <option value="" disabled selected>-- Select Payment Method --</option>
                                    <option value="Online">Online Payment</option>
                                    <option value="COD">Cash on Delivery (COD)</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-2">
                            <button type="button" onclick="goToStep1()" class="w-1/3 bg-gray-100 text-gray-600 font-bold py-4 rounded-2xl hover:bg-gray-200 transition-all">
                                <i class="fas fa-chevron-left mr-2"></i> Edit
                            </button>
                            <button type="button" onclick="proceedFromStep2()" class="w-2/3 bg-blue-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-3">
                                Proceed <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: MANUAL BANK DETAILS (Only for Online Payment) -->
                    <div id="step3" class="step-hidden space-y-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 p-6 rounded-3xl relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/40 z-0"></div>
                            <div class="relative z-10">
                                <h4 class="text-xs font-black text-blue-800 uppercase tracking-widest text-center mb-4 pb-2 border-b border-blue-200">Bank Account Details</h4>
                                
                                <?php if(empty($bank_accounts)): ?>
                                    <div class="bg-red-50 text-red-600 p-4 rounded-xl text-center text-sm font-semibold">
                                        No active bank account available for payment right now.
                                    </div>
                                <?php else: ?>
                                    <?php foreach($bank_accounts as $index => $bank): ?>
                                    <div class="space-y-3 text-sm font-semibold text-gray-700 bg-white p-4 rounded-xl shadow-sm mb-4 border border-gray-100">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Bank Name</span> 
                                            <span class="text-blue-900 font-bold flex items-center gap-2"><?php echo htmlspecialchars($bank['bank_name']); ?> <i class="fas fa-copy text-orange-500 cursor-pointer hover:text-orange-600 transition-colors" onclick="copyToClipboard('<?php echo addslashes($bank['bank_name']); ?>')" title="Copy"></i></span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">A/C No</span> 
                                            <span class="text-blue-900 font-bold flex items-center gap-2"><?php echo htmlspecialchars($bank['account_no']); ?> <i class="fas fa-copy text-orange-500 cursor-pointer hover:text-orange-600 transition-colors" onclick="copyToClipboard('<?php echo addslashes($bank['account_no']); ?>')" title="Copy"></i></span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">IFSC Code</span> 
                                            <span class="text-blue-900 font-bold uppercase flex items-center gap-2"><?php echo htmlspecialchars($bank['ifsc_code']); ?> <i class="fas fa-copy text-orange-500 cursor-pointer hover:text-orange-600 transition-colors" onclick="copyToClipboard('<?php echo addslashes($bank['ifsc_code']); ?>')" title="Copy"></i></span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Account Name</span> 
                                            <span class="text-blue-900 font-bold flex items-center gap-2"><?php echo htmlspecialchars($bank['account_name']); ?> <i class="fas fa-copy text-orange-500 cursor-pointer hover:text-orange-600 transition-colors" onclick="copyToClipboard('<?php echo addslashes($bank['account_name']); ?>')" title="Copy"></i></span>
                                        </div>
                                        <?php if(!empty($bank['qr_code'])): ?>
                                            <div class="mt-4 flex flex-col items-center justify-center border-t border-gray-100 pt-4">
                                                <span class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Or Scan QR to Pay</span>
                                                <img src="../uploads/qr_codes/<?php echo htmlspecialchars($bank['qr_code']); ?>" alt="QR Code" class="w-32 h-32 rounded-xl object-cover shadow-sm border border-gray-200">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <div class="space-y-4 pt-2">
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-black text-gray-500 uppercase tracking-widest ml-1">Unique Transaction Reference (UTR) <span class="text-red-500">*</span></label>
                                        <input type="text" name="utr_number" id="utr_number" placeholder="6 to 12 Digit UTR Number" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all font-semibold text-gray-800">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[11px] font-black text-gray-500 uppercase tracking-widest ml-1">Upload Your Payment Proof <span class="text-red-500">[Required]</span></label>
                                        <input type="file" name="payment_proof" id="payment_proof" accept="image/*,.pdf" class="w-full bg-white border border-gray-200 rounded-xl outline-none transition-all font-semibold text-gray-800 file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:font-semibold file:bg-orange-500 file:text-white hover:file:bg-orange-600 cursor-pointer">
                                    </div>
                                    
                                    <div class="flex items-start gap-2 pt-2">
                                        <input type="checkbox" id="terms" class="mt-1">
                                        <label for="terms" class="text-xs font-semibold text-gray-500">I have read and agree with the <span class="text-orange-500">terms of payment and withdrawal policy.</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-2">
                            <button type="button" onclick="goToStep2From3()" class="w-1/3 bg-gray-100 text-gray-600 font-bold py-4 rounded-2xl hover:bg-gray-200 transition-all">
                                <i class="fas fa-chevron-left mr-2"></i> Back
                            </button>
                            <button id="pay-button" type="submit" class="w-2/3 bg-blue-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-3 group">
                                Confirm & Pay <i class="fas fa-check-circle group-hover:scale-110 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Hidden submit button for COD programmatic submission -->
                    <button type="submit" id="hidden-submit" class="hidden"></button>
                </form>

                <!-- Trust Badges -->
                <div class="mt-8 pt-6 border-t border-gray-100/60 flex flex-wrap justify-center sm:justify-between items-center gap-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    <span class="flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100"><i class="fas fa-shield-alt text-green-500 text-sm"></i> NABL Certified</span>
                    <span class="flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100"><i class="fas fa-file-invoice text-blue-500 text-sm"></i> Smart Reports</span>
                    <span class="flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100"><i class="fas fa-home text-orange-500 text-sm"></i> Home Visit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Inclusion -->
    <?php include '../footer.php'; ?>

    <!-- Payment Processing Script -->
    <script>
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const patientNameOrig = "<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>";

    // Dynamic Identity Setting
    function setIdentity(type) {
        const btnSelf = document.getElementById('btn-self');
        const btnFamily = document.getElementById('btn-family');
        const nameInput = document.getElementById('patient_name');
        
        if(type === 'self') {
            btnSelf.classList.add('active');
            btnFamily.classList.remove('active');
            btnFamily.classList.add('text-gray-500');
            nameInput.value = patientNameOrig;
            nameInput.readOnly = true;
        } else {
            btnFamily.classList.add('active');
            btnFamily.classList.remove('text-gray-500');
            btnSelf.classList.remove('active');
            nameInput.value = "";
            nameInput.readOnly = false;
            nameInput.focus();
        }
    }

    // Dynamic Health Instructions
    function updateHealthGuide() {
        const test = document.getElementById('test-select').value;
        const guide = document.getElementById('health-guide');
        const msg = document.getElementById('guide-msg');
        
        let tip = "";
        if(test.includes('Sugar') || test.includes('Lipid') || test.includes('KFT') || test.includes('Thyroid')) {
            tip = "Note: 10-12 hours of fasting is required for this test. (सिर्फ पानी पी सकते हैं)";
        } else if(test.includes('CBC')) {
            tip = "Quick Tip: No special preparation needed. Fast results within 12 hours!";
        }
        
        if(tip) {
            msg.innerText = tip;
            guide.classList.remove('hidden');
        } else {
            guide.classList.add('hidden');
        }
    }

    function updateDots(step) {
        const stepNames = ['Patient Details', 'Summary & Payment', 'Final Confirmation'];
        document.getElementById('stepName').innerText = `Step ${step}: ${stepNames[step-1]}`;
        
        for(let i=1; i<=3; i++) {
            const dot = document.getElementById('dot' + i);
            if(i === step) {
                dot.className = 'step-dot w-6 h-full rounded-full bg-blue-600 transition-all duration-300';
            } else if(i < step) {
                dot.className = 'step-dot w-2 h-full rounded-full bg-emerald-500 transition-all duration-300';
            } else {
                dot.className = 'step-dot w-2 h-full rounded-full bg-gray-200 transition-all duration-300';
            }
        }
    }

    function goToStep2() {
        const testSelect = document.getElementById('test-select');
        const nameInput = document.getElementById('patient_name');
        const mobileInput = document.getElementById('patient_mobile');
        const dateInput = document.getElementById('booking_date');
        const slotInput = document.getElementById('timeslot');

        if (!testSelect.value || !nameInput.value || !mobileInput.value || !dateInput.value) {
            alert('Please fill all patient and schedule details first.');
            return;
        }

        // 10-Digit Mobile Validation
        const mobileVal = mobileInput.value.trim();
        if(!/^[0-9]{10}$/.test(mobileVal)) {
            alert('Please enter a valid 10-digit mobile number.');
            mobileInput.focus();
            return;
        }

        // Fetch data
        const selectedOption = testSelect.options[testSelect.selectedIndex];
        const price = parseInt(selectedOption.getAttribute('data-price')) || 0;
        const gst = Math.round(price * 0.18);
        const total = price + gst;

        // Populate Summary
        document.getElementById('conf-name').innerText = nameInput.value;
        // Truncate test name if it's too long (multiple tests)
        let displayName = testSelect.value;
        if(displayName.length > 40) displayName = displayName.substring(0, 37) + '...';
        document.getElementById('conf-test').innerText = displayName;
        document.getElementById('conf-mobile').innerText = mobileInput.value;
        document.getElementById('conf-date').innerText = dateInput.value;
        document.getElementById('conf-slot').innerText = slotInput.value;
        document.getElementById('conf-subtotal').innerText = '₹' + price;
        document.getElementById('conf-gst').innerText = '+ ₹' + gst;
        document.getElementById('conf-price').innerText = '₹' + total;

        // Switch Steps
        step1.classList.replace('step-active', 'step-hidden');
        step2.classList.replace('step-hidden', 'step-active');
        updateDots(2);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Coupon Logic
    function applyCoupon() {
        const code = document.getElementById('coupon_code').value;
        const testSelect = document.getElementById('test-select');
        const selectedOption = testSelect.options[testSelect.selectedIndex];
        const basePrice = parseInt(selectedOption.getAttribute('data-price')) || 0;
        const gst = Math.round(basePrice * 0.18);
        const totalBeforeDiscount = basePrice + gst;
        
        if(!code) return;

        fetch('check_coupon.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `code=${code}&amount=${totalBeforeDiscount}`
        })
        .then(res => res.json())
        .then(data => {
            const msg = document.getElementById('coupon-msg');
            if(data.status === 'success') {
                msg.className = "text-[10px] font-bold mt-1 text-green-600";
                msg.innerText = "✅ " + data.message;
                
                // Update UI Prices
                document.getElementById('discount-row').classList.remove('hidden');
                document.getElementById('conf-discount').innerText = '-₹' + data.discount;
                document.getElementById('conf-price').innerText = '₹' + (totalBeforeDiscount - data.discount);
                
                // Store in hidden fields
                document.getElementById('applied_coupon').value = code;
                document.getElementById('discount_amt').value = data.discount;
            } else {
                msg.className = "text-[10px] font-bold mt-1 text-red-500";
                msg.innerText = "❌ " + data.message;
            }
        });
    }

    function goToStep1() {
        step2.classList.replace('step-active', 'step-hidden');
        step1.classList.replace('step-hidden', 'step-active');
        updateDots(1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function proceedFromStep2() {
        const pm = document.getElementById('payment_method').value;
        if (!pm) {
            alert('Please select a payment method to proceed.');
            return;
        }

        if (pm === 'Online') {
            // Go to step 3 (Bank Details)
            step2.classList.replace('step-active', 'step-hidden');
            step3.classList.replace('step-hidden', 'step-active');
            updateDots(3);
            
            document.getElementById('utr_number').required = true;
            document.getElementById('payment_proof').required = true;
            document.getElementById('terms').required = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            // Process COD directly
            document.getElementById('utr_number').required = false;
            document.getElementById('payment_proof').required = false;
            document.getElementById('terms').required = false;
            
            // Programmatically click hidden submit button to trigger standard validation and submission
            document.getElementById('hidden-submit').click();
        }
    }

    function goToStep2From3() {
        step3.classList.replace('step-active', 'step-hidden');
        step2.classList.replace('step-hidden', 'step-active');
        updateDots(2);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.getElementById('bookingForm').addEventListener('submit', function(e){
        e.preventDefault(); 
        
        const testSelect = document.getElementById('test-select');
        const paymentMethod = document.getElementById('payment_method').value;
        const btn = document.getElementById('pay-button');
        
        const selectedOption = testSelect.options[testSelect.selectedIndex];
        let amt = parseInt(selectedOption.getAttribute('data-price')) || 0;
        let totalAmount = Math.round(amt * 1.18); 
        
        // Subtract Discount if any
        const discountAmt = parseInt(document.getElementById('discount_amt').value) || 0;
        totalAmount = totalAmount - discountAmt;

        if (btn) {
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
            btn.disabled = true;
        }

        let formData = new FormData(this);
        formData.append('amount', totalAmount);

        fetch('process_booking.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(text => {
            let data;
            try {
                data = JSON.parse(text);
            } catch(e) {
                console.error('Invalid JSON:', text);
                alert('Something went wrong. Please check console.');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-check-circle group-hover:scale-110 transition-transform"></i> Confirm & Pay';
                    btn.disabled = false;
                }
                return;
            }
            if(data.status === 'success') {
                if(data.type === 'ONLINE') {
                    window.location.href = data.redirect_url;
                } else if(data.type === 'MANUAL_ONLINE') {
                    alert('✅ Payment Details Submitted Successfully! Waiting for verification.');
                    localStorage.removeItem('mylab_cart');
                    
                    // Open WhatsApp in a new tab so user can still be redirected to bookings
                    let shortTest = testSelect.value;
                    if(shortTest.length > 50) shortTest = "Multiple Tests Package";
                    const waMsg = encodeURIComponent(`Hi, I just booked a *${shortTest}* test on My Diagnostic Lab and paid online.\nPatient: ${document.getElementById('patient_name').value}\nSlot: ${document.getElementById('timeslot').value}\nAmount: ₹${totalAmount}\nUTR: ${document.getElementById('utr_number').value}`);
                    window.open(`https://wa.me/919999999999?text=${waMsg}`, '_blank');
                    
                    window.location.href = 'user-bookings.php';
                } else {
                    // Success - WhatsApp Redirection
                    alert('✅ Booking Successful!');
                    localStorage.removeItem('mylab_cart');
                    
                    // Open WhatsApp in a new tab
                    let shortTest = testSelect.value;
                    if(shortTest.length > 50) shortTest = "Multiple Tests Package";
                    const waMsg = encodeURIComponent(`Hi, I just booked a *${shortTest}* test on My Diagnostic Lab.\nPatient: ${document.getElementById('patient_name').value}\nSlot: ${document.getElementById('timeslot').value}\nAmount: ₹${totalAmount}\nPayment: COD`);
                    window.open(`https://wa.me/919999999999?text=${waMsg}`, '_blank');
                    
                    window.location.href = 'user-bookings.php';
                }
            } else {
                alert('Error: ' + data.message);
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-check-circle group-hover:scale-110 transition-transform"></i> Confirm & Pay';
                    btn.disabled = false;
                }
            }
        })
        .catch(err => { 
            alert('Network error.'); 
            if (btn) {
                btn.innerHTML = '<i class="fas fa-check-circle group-hover:scale-110 transition-transform"></i> Confirm & Pay';
                btn.disabled = false; 
            }
        });
    });
    // Initial setup
    window.addEventListener('load', () => {
        updateHealthGuide();
        setIdentity('self');
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Optional: show a small toast or just let it be silent/alert
        }).catch(err => {
            console.error('Failed to copy', err);
        });
    }
    </script>
</body>
</html>