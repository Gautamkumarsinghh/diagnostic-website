<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
$error = "";

// Handle Report Upload
if (isset($_POST['upload_report'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    
    if (isset($_FILES['report']) && $_FILES['report']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['report']['name'], PATHINFO_EXTENSION));
        if ($ext == 'pdf') {
            $upload_dir = '../uploads/reports/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $new_name = 'report_' . $booking_id . '_' . time() . '.pdf';
            if (move_uploaded_file($_FILES['report']['tmp_name'], $upload_dir . $new_name)) {
                // Update Database
                mysqli_query($conn, "UPDATE bookings SET report_file='$new_name', status='Completed' WHERE id='$booking_id'");
                $msg = "Report uploaded and status updated to Completed!";
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Only PDF files are allowed.";
        }
    } else {
        $error = "Please select a valid PDF file.";
    }
}

// Fetch Bookings that need reports (Completed or In Lab)
$q = mysqli_query($conn, "SELECT * FROM bookings WHERE status IN ('In Lab', 'Completed', 'Collected') ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Center | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800">Report Center</h1>
            <p class="text-slate-500 font-medium">Upload and manage patient diagnostic reports</p>
        </div>

        <?php if($msg != ""): ?>
            <div class="bg-emerald-100 text-emerald-700 p-4 rounded-2xl mb-8 font-bold flex items-center gap-3 border-l-4 border-emerald-500">
                <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <?php if($error != ""): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-2xl mb-8 font-bold flex items-center gap-3 border-l-4 border-red-500">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Patient & Test</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Current Status</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Report File</th>
                        <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Upload Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php while($row = mysqli_fetch_assoc($q)): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-6">
                            <div class="font-bold text-slate-800"><?php echo $row['name']; ?></div>
                            <div class="text-xs text-blue-600 font-black mt-1"><?php echo $row['test']; ?></div>
                        </td>
                        <td class="p-6">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                                <?php echo ($row['status'] == 'Completed') ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600'; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td class="p-6">
                            <?php if($row['report_file']): ?>
                                <a href="../uploads/reports/<?php echo $row['report_file']; ?>" target="_blank" class="flex items-center gap-2 text-blue-600 font-bold hover:underline">
                                    <i class="fas fa-file-pdf"></i> View Report
                                </a>
                            <?php else: ?>
                                <span class="text-slate-300 italic text-sm">Not uploaded yet</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-6 text-right">
                            <form method="POST" enctype="multipart/form-data" class="flex items-center justify-end gap-2">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <div class="relative group">
                                    <input type="file" name="report" accept=".pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <button type="button" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-xl text-xs font-black group-hover:bg-blue-50 group-hover:text-blue-600 transition-all">
                                        <i class="fas fa-paperclip mr-2"></i> Select PDF
                                    </button>
                                </div>
                                <button type="submit" name="upload_report" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-slate-800 transition-all shadow-lg shadow-blue-200">
                                    Upload <i class="fas fa-upload ml-1"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php if(mysqli_num_rows($q) == 0): ?>
                <div class="p-20 text-center text-slate-400">
                    <i class="fas fa-clipboard-check text-5xl mb-4 opacity-20 block"></i>
                    <p class="font-bold">No active bookings found for report upload.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
