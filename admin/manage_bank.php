<?php
session_start();
require_once '../db/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$success_msg = '';
$error_msg = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get QR image to delete along with record
    $stmt = $conn->prepare("SELECT qr_code FROM bank_accounts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if(!empty($row['qr_code']) && file_exists("../uploads/qr_codes/" . $row['qr_code'])) {
            unlink("../uploads/qr_codes/" . $row['qr_code']);
        }
    }
    
    $delete_stmt = $conn->prepare("DELETE FROM bank_accounts WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    if ($delete_stmt->execute()) {
        $success_msg = "Bank account deleted successfully.";
    } else {
        $error_msg = "Error deleting bank account.";
    }
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $account_no = mysqli_real_escape_string($conn, $_POST['account_no']);
    $ifsc_code = mysqli_real_escape_string($conn, $_POST['ifsc_code']);
    $account_name = mysqli_real_escape_string($conn, $_POST['account_name']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    // Handle File Upload for QR Code
    $qr_code_filename = '';
    $upload_ok = true;
    if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/qr_codes/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES["qr_code"]["name"], PATHINFO_EXTENSION));
        $allowed_exts = array("jpg", "jpeg", "png", "webp");
        
        if (in_array($file_ext, $allowed_exts)) {
            $qr_code_filename = uniqid('qr_') . '.' . $file_ext;
            if (!move_uploaded_file($_FILES["qr_code"]["tmp_name"], $target_dir . $qr_code_filename)) {
                $error_msg = "Failed to upload QR Code image.";
                $upload_ok = false;
            }
        } else {
            $error_msg = "Only JPG, JPEG, PNG formats are allowed for QR Code.";
            $upload_ok = false;
        }
    }
    
    if ($upload_ok) {
        if ($id > 0) {
            // Update
            if (!empty($qr_code_filename)) {
                // Fetch old image to delete
                $os = $conn->prepare("SELECT qr_code FROM bank_accounts WHERE id = ?");
                $os->bind_param("i", $id);
                $os->execute();
                $or = $os->get_result();
                if($or->num_rows>0){
                    $old = $or->fetch_assoc();
                    if(!empty($old['qr_code']) && file_exists("../uploads/qr_codes/" . $old['qr_code'])){
                        unlink("../uploads/qr_codes/" . $old['qr_code']);
                    }
                }
                
                $stmt = $conn->prepare("UPDATE bank_accounts SET bank_name=?, account_no=?, ifsc_code=?, account_name=?, qr_code=?, is_active=? WHERE id=?");
                $stmt->bind_param("sssssii", $bank_name, $account_no, $ifsc_code, $account_name, $qr_code_filename, $is_active, $id);
            } else {
                $stmt = $conn->prepare("UPDATE bank_accounts SET bank_name=?, account_no=?, ifsc_code=?, account_name=?, is_active=? WHERE id=?");
                $stmt->bind_param("ssssii", $bank_name, $account_no, $ifsc_code, $account_name, $is_active, $id);
            }
            
            if ($stmt->execute()) {
                $success_msg = "Bank account updated successfully.";
            } else {
                $error_msg = "Error updating bank account.";
            }
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO bank_accounts (bank_name, account_no, ifsc_code, account_name, qr_code, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $bank_name, $account_no, $ifsc_code, $account_name, $qr_code_filename, $is_active);
            
            if ($stmt->execute()) {
                $success_msg = "New bank account added successfully.";
            } else {
                $error_msg = "Error adding bank account.";
            }
        }
    }
}

// Fetch all bank accounts
$result = $conn->query("SELECT * FROM bank_accounts ORDER BY id DESC");
$bank_accounts = [];
if ($result) {
    while($row = $result->fetch_assoc()){
        $bank_accounts[] = $row;
    }
}

// If editing, get the specific record
$edit_record = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM bank_accounts WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows > 0){
        $edit_record = $res->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bank & Payments - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
    </style>
</head>
<body class="bg-gray-50 flex">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-8">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manage Bank & Payment Details</h1>
                    <p class="text-sm text-gray-500 mt-1">Configure the bank accounts and QR codes shown to users for manual online payments.</p>
                </div>
                <?php if ($edit_record): ?>
                    <a href="manage_bank.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Add New Account
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($success_msg): ?>
                <div class="bg-green-50 z-50 text-green-800 p-4 rounded-xl border border-green-200 mb-6 flex items-center shadow-sm">
                    <i class="fas fa-check-circle mr-3 text-green-500 text-lg"></i>
                    <p class="font-semibold text-sm"><?php echo htmlspecialchars($success_msg); ?></p>
                </div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div class="bg-red-50 z-50 text-red-800 p-4 rounded-xl border border-red-200 mb-6 flex items-center shadow-sm">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500 text-lg"></i>
                    <p class="font-semibold text-sm"><?php echo htmlspecialchars($error_msg); ?></p>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Form Area -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">
                            <?php echo $edit_record ? 'Edit Bank Account' : 'Add New Account'; ?>
                        </h2>
                        <form action="manage_bank.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <?php if ($edit_record): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_record['id']; ?>">
                            <?php endif; ?>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase">Bank Name</label>
                                <input type="text" name="bank_name" required value="<?php echo $edit_record ? htmlspecialchars($edit_record['bank_name']) : ''; ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-500 focus:bg-white transition-all text-sm font-semibold" placeholder="e.g. INDIAN BANK">
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase">Account Number</label>
                                <input type="text" name="account_no" required value="<?php echo $edit_record ? htmlspecialchars($edit_record['account_no']) : ''; ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-500 focus:bg-white transition-all text-sm font-semibold" placeholder="e.g. 8230850479">
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase">IFSC Code</label>
                                <input type="text" name="ifsc_code" required value="<?php echo $edit_record ? htmlspecialchars($edit_record['ifsc_code']) : ''; ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-500 focus:bg-white transition-all text-sm font-semibold uppercase" placeholder="e.g. idib000b559">
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase">Account Holder Name</label>
                                <input type="text" name="account_name" required value="<?php echo $edit_record ? htmlspecialchars($edit_record['account_name']) : ''; ?>" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-500 focus:bg-white transition-all text-sm font-semibold" placeholder="e.g. VIPIT taresh">
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase">QR Code Image (Optional)</label>
                                <input type="file" name="qr_code" accept="image/*" class="w-full px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none text-xs file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                <?php if ($edit_record && !empty($edit_record['qr_code'])): ?>
                                    <div class="mt-2 text-xs text-gray-500 flex items-center gap-2">
                                        <i class="fas fa-image"></i> Current QR uploaded
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center gap-2 pt-2">
                                <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo (!$edit_record || $edit_record['is_active'] == 1) ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 rounded">
                                <label for="is_active" class="text-sm font-semibold text-gray-700 cursor-pointer">Set as Active (Visible to users)</label>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20 active:scale-[0.98]">
                                <?php echo $edit_record ? 'Update Account' : 'Save Account'; ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Existing Accounts List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-bold text-gray-800">Saved Bank Accounts</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 bg-opacity-50">
                                    <tr>
                                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Bank Info</th>
                                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">QR Code</th>
                                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if(empty($bank_accounts)): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm font-medium text-gray-500">
                                            No bank accounts added yet. Add your first payment details to show it on the booking page.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach($bank_accounts as $ba): ?>
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-900"><?php echo htmlspecialchars($ba['bank_name']); ?></div>
                                                <div class="text-sm text-gray-600 mt-1"><span class="font-semibold text-gray-400">A/C:</span> <?php echo htmlspecialchars($ba['account_no']); ?></div>
                                                <div class="text-sm text-gray-600"><span class="font-semibold text-gray-400">IFSC:</span> <span class="uppercase"><?php echo htmlspecialchars($ba['ifsc_code']); ?></span></div>
                                                <div class="text-sm text-gray-600"><span class="font-semibold text-gray-400">Name:</span> <?php echo htmlspecialchars($ba['account_name']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 align-top">
                                                <?php if(!empty($ba['qr_code'])): ?>
                                                    <a href="../uploads/qr_codes/<?php echo htmlspecialchars($ba['qr_code']); ?>" target="_blank" class="inline-block relative group">
                                                        <img src="../uploads/qr_codes/<?php echo htmlspecialchars($ba['qr_code']); ?>" alt="QR" class="w-16 h-16 rounded-lg object-cover border border-gray-200 shadow-sm">
                                                        <div class="absolute inset-0 bg-black/50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <i class="fas fa-external-link-alt text-white text-xs"></i>
                                                        </div>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded-md">No QR</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 align-top">
                                                <?php if($ba['is_active']): ?>
                                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div> Active
                                                    </span>
                                                <?php else: ?>
                                                    <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div> Inactive
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 align-top text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="manage_bank.php?edit=<?php echo $ba['id']; ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors" title="Edit">
                                                        <i class="fas fa-pen text-sm"></i>
                                                    </a>
                                                    <a href="manage_bank.php?delete=<?php echo $ba['id']; ?>" onclick="return confirm('Are you sure you want to delete this bank account?');" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors" title="Delete">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</body>
</html>
