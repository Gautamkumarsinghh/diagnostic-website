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
            
            <!-- Card Header Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-8 sm:p-10 text-white relative overflow-hidden">
                <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left">
                    <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-md border border-white/20 shadow-lg relative group">
                        <i class="fas fa-stethoscope text-4xl group-hover:scale-110 transition-transform duration-300"></i>
                        <div class="absolute -top-2 -right-2 bg-green-500 w-4 h-4 rounded-full border-2 border-white animate-pulse"></div>
                    </div>
                    <div class="mt-2 sm:mt-0">
                        <h2 class="text-3xl font-extrabold tracking-tight mb-2">Book Your Test</h2>
                        <p class="text-blue-100 text-sm opacity-90 leading-relaxed">Schedule a home sample collection. Fast, safe, and entirely hassle-free.</p>
                    </div>
                </div>
                <!-- Decorative Elements -->
                <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-black/10 rounded-full blur-xl"></div>
                <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
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
                                <input type="tel" name="mobile" id="patient_mobile" placeholder="e.g. +91 98765 43210" required 
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
                                    <option value="Online">Online Payment (PhonePe)</option>
                                    <option value="COD">Cash on Delivery (COD)</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-2">
                            <button type="button" onclick="goToStep1()" class="w-1/3 bg-gray-100 text-gray-600 font-bold py-4 rounded-2xl hover:bg-gray-200 transition-all">
                                <i class="fas fa-chevron-left mr-2"></i> Edit
                            </button>
                            <button id="pay-button" type="submit" class="w-2/3 bg-blue-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-3 group">
                                Confirm & Pay <i class="fas fa-check-circle group-hover:scale-110 transition-transform"></i>
                            </button>
                        </div>
                    </div>
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
        step1.classList.remove('step-active');
        step1.classList.add('step-hidden');
        step2.classList.remove('step-hidden');
        step2.classList.add('step-active');
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
        step2.classList.remove('step-active');
        step2.classList.add('step-hidden');
        step1.classList.remove('step-hidden');
        step1.classList.add('step-active');
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

        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
        btn.disabled = true;

        let formData = new FormData(this);
        formData.append('amount', totalAmount);

        fetch('process_booking.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                if(data.type === 'ONLINE') {
                    window.location.href = data.redirect_url;
                } else {
                    // Success - WhatsApp Redirection
                    alert('✅ Booking Successful!');
                    
                    // Clear cart after successful booking
                    localStorage.removeItem('mylab_cart');
                    
                    let shortTest = testSelect.value;
                    if(shortTest.length > 50) shortTest = "Multiple Tests Package";
                    const waMsg = encodeURIComponent(`Hi, I just booked a *${shortTest}* test on My Diagnostic Lab.\nPatient: ${document.getElementById('patient_name').value}\nSlot: ${document.getElementById('timeslot').value}\nAmount: ₹${totalAmount}\nPayment: COD`);
                    window.location.href = `https://wa.me/919999999999?text=${waMsg}`; // Replace with your number
                }
            } else {
                alert('Error: ' + data.message);
                btn.innerHTML = 'Confirm & Pay';
                btn.disabled = false;
            }
        })
        .catch(err => { alert('Network error.'); btn.disabled = false; });
    });
    // Initial setup
    window.addEventListener('load', () => {
        updateHealthGuide();
        setIdentity('self');
    });
    </script>
</body>
</html>