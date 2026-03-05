<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$users_q = mysqli_query($conn, "SELECT name, mobile, email FROM users");
$users = [];
while($row = mysqli_fetch_assoc($users_q)) $users[] = $row;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Offers | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800">Bulk Offer Sender</h1>
            <p class="text-slate-500 font-medium">Send personalized offers to your patients via WhatsApp</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Message Composer -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-green-500"></i> Compose Offer
                </h3>
                
                <div class="space-y-6">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Offer Message</label>
                        <textarea id="offer_msg" rows="6" class="w-full bg-slate-50 border-none rounded-2xl p-4 font-medium text-slate-700 focus:ring-2 focus:ring-green-500 outline-none" 
                        placeholder="Hi {name}, we have a special offer for you! Use code HEALTH20 for 20% off on your next checkup.">Hi {name}, we have a special offer for you! Use code HEALTH20 for 20% off on your next checkup.</textarea>
                        <p class="text-[10px] text-slate-400 mt-2">Use <b>{name}</b> to automatically insert patient's name.</p>
                    </div>

                    <div class="bg-green-50 p-6 rounded-2xl border border-green-100">
                        <div class="text-green-800 font-black mb-2 flex items-center gap-2">
                            <i class="fas fa-lightbulb"></i> How it works:
                        </div>
                        <ul class="text-green-700 text-xs space-y-2 font-bold opacity-80">
                            <li>1. Click "Open Chat" for a patient in the list.</li>
                            <li>2. It will generate a custom WhatsApp link with your message.</li>
                            <li>3. Paste and send! (Manual bulk sending is safer for WhatsApp).</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Patient List -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Select Patient</span>
                    <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black"><?php echo count($users); ?> Patients Total</span>
                </div>
                
                <div class="max-h-[500px] overflow-y-auto divide-y divide-slate-50">
                    <?php foreach($users as $u): ?>
                    <div class="p-6 flex justify-between items-center hover:bg-slate-50/50 transition-all">
                        <div>
                            <div class="font-black text-slate-800"><?php echo $u['name']; ?></div>
                            <div class="text-xs text-slate-400 font-bold"><?php echo $u['mobile']; ?></div>
                        </div>
                        <button onclick="sendTo('<?php echo $u['mobile']; ?>', '<?php echo addslashes($u['name']); ?>')" class="bg-green-100 text-green-600 px-4 py-2 rounded-xl text-xs font-black hover:bg-green-500 hover:text-white transition-all flex items-center gap-2">
                            <i class="fab fa-whatsapp"></i> Open Chat
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function sendTo(mobile, name) {
        let msg = document.getElementById('offer_msg').value;
        msg = msg.replace('{name}', name);
        
        // Clean mobile number (keep only digits)
        let cleanMobile = mobile.replace(/\D/g, '');
        if(cleanMobile.length == 10) cleanMobile = "91" + cleanMobile;

        const url = `https://wa.me/${cleanMobile}?text=${encodeURIComponent(msg)}`;
        window.open(url, '_blank');
    }
    </script>

</body>
</html>
