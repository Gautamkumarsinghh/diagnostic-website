<?php
session_start();
include '../db/config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Search Logic
$search_query = "";
if(isset($_GET['search']) && !empty($_GET['search'])){
    $s = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " WHERE (name LIKE '%$s%' OR mobile LIKE '%$s%' OR test LIKE '%$s%')";
}

// Fetch Bookings
$q = mysqli_query($conn,"SELECT * FROM bookings $search_query ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Center | MyLab Admin</title>
    <!-- Google Fonts: Inter for that clean SaaS look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS for clean layout utilities -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fcfcfd; /* Very light gray/white */
            color: #1a1a1a;
        }

        .main-content {
            margin-left: 280px; /* Offset for sidebar */
            padding: 2rem 3rem;
            min-height: 100vh;
        }

        @media (max-width: 1024px) {
            .main-content { margin-left: 90px; padding: 1.5rem; }
        }

        /* Simple Card Style */
        .page-card {
            background: #ffffff;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            overflow: hidden;
        }

        /* Clean Table Styles */
        .clean-table th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #eef0f2;
        }

        .clean-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .clean-table tr:last-child td {
            border-bottom: none;
        }

        /* Status Pills - Very Minimal */
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-completed { background: #ecfdf5; color: #059669; }
        .status-pending { background: #fffbeb; color: #d97706; }
        .status-other { background: #f0f9ff; color: #0284c7; }

        /* Search Input - Clean & Focused */
        .search-input {
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
        }
        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        /* Action Buttons - Simple outline style */
        .action-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.2s;
            background: white;
        }
        .action-btn:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #f0f7ff;
        }
        .action-btn.btn-delete:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }

        /* Upload Button - Accent */
        .btn-upload {
            background: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: opacity 0.2s;
        }
        .btn-upload:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <!-- Reuse existing Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <!-- Minimal Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Report Center</h1>
                <p class="text-slate-500 text-sm mt-1">Manage diagnostic reports and delivery status</p>
            </div>
            
            <div class="flex items-center gap-3">
                <form class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" placeholder="Search patients..." 
                           value="<?php echo @$_GET['search']; ?>"
                           class="search-input pl-10 pr-4 py-2 bg-white rounded-lg text-sm w-64 md:w-80">
                </form>
                <div class="h-8 w-px bg-slate-200 hidden md:block"></div>
                <div class="flex gap-2">
                    <a href="export.php" title="Export CSV" class="action-btn"><i class="fas fa-file-csv"></i></a>
                    <a href="pdf.php" title="Export PDF" class="action-btn"><i class="fas fa-file-pdf"></i></a>
                </div>
            </div>
        </div>

        <!-- Clean Table Area -->
        <div class="page-card">
            <div class="overflow-x-auto">
                <table class="w-full clean-table text-left border-collapse">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Test / Lab Service</th>
                            <th>Date</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>UTR Number</th>
                        <th>Proof</th>
                        <th>Status</th>
                        <th>Report</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php
                    $i = 1;
                    while($row = mysqli_fetch_assoc($q)){
                            $status = $row['status'];
                            $stClass = ($status == 'Completed') ? 'status-completed' : (($status == 'Booked') ? 'status-pending' : 'status-other');
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td>
                                <div class="font-semibold text-slate-800"><?php echo $row['name']; ?></div>
                                <div class="text-xs text-slate-500 mt-0.5"><?php echo $row['mobile']; ?></div>
                            </td>
                            <td>
                                <div class="text-slate-700"><?php echo $row['test']; ?></div>
                            </td>
                            <td class="text-slate-600">
                                <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                            </td>
                        <td class="font-bold text-slate-700">₹<?php echo number_format($row['amount'], 2); ?></td>
                        <td>
                            <div class="text-xs font-black text-blue-600 uppercase tracking-tight"><?php echo $row['payment_method'] ?? 'Online'; ?></div>
                            <small class="text-[10px] text-slate-400 font-bold uppercase"><?php echo $row['payment_status']; ?></small>
                        </td>
                        <td>
                            <?php if(!empty($row['utr_number'])): ?>
                                <span class="text-[10px] font-mono font-bold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">
                                    <?php echo $row['utr_number']; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-[10px] text-slate-300 italic">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(!empty($row['payment_proof'])): ?>
                                <a href="../uploads/payments/<?php echo $row['payment_proof']; ?>" target="_blank" class="text-blue-500 hover:text-blue-700 font-bold text-[10px] flex items-center gap-1">
                                    <i class="fas fa-file-invoice"></i> Proof
                                </a>
                            <?php else: ?>
                                <span class="text-[10px] text-slate-300 italic">None</span>
                            <?php endif; ?>
                        </td>
                            <td>
                                <span class="status-pill <?php echo $stClass; ?>">
                                    <?php echo $status; ?>
                                </span>
                            </td>
                            <td>
                                <?php if(!empty($row['report_file'])): ?>
                                    <a href="../uploads/reports/<?php echo $row['report_file']; ?>" target="_blank" 
                                       class="text-blue-600 font-semibold hover:text-blue-800 flex items-center gap-1.5">
                                        <i class="fas fa-file-pdf text-base"></i>
                                        <span>View PDF</span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-300 italic">No report</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="openUploadModal(<?php echo $row['id']; ?>)" 
                                            class="btn-upload" title="Upload Report">
                                        <i class="fas fa-cloud-arrow-up"></i>
                                        <span class="hidden sm:inline">Upload</span>
                                    </button>
                                    
                                    <div class="flex gap-1 border-l border-slate-100 pl-2">
                                        <?php if(!empty($row['prescription_file'])): ?>
                                            <a href="../uploads/prescriptions/<?php echo $row['prescription_file']; ?>" 
                                               target="_blank" class="action-btn" title="View Prescription">
                                                <i class="fas fa-prescription"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" 
                                           onclick="return confirm('Delete this record?')" 
                                           class="action-btn btn-delete" title="Delete">
                                            <i class="fas fa-trash-can"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if(mysqli_num_rows($q) == 0): ?>
                            <tr>
                                <td colspan="6" class="p-20 text-center">
                                    <div class="text-slate-300 mb-2"><i class="fas fa-inbox text-5xl"></i></div>
                                    <p class="text-slate-500 font-medium">No bookings found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Minimal Scripts -->
    <script>
        function openUploadModal(id) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf,.jpg,.jpeg,.png';
            input.onchange = e => {
                const file = e.target.files[0];
                const formData = new FormData();
                formData.append('report', file);
                formData.append('booking_id', id);

                // Use the existing AJAX file
                fetch('upload_report_ajax.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(data => {
                    if(data.trim() === 'Success') {
                        location.reload();
                    } else {
                        alert('Upload failed: ' + data);
                    }
                });
            };
            input.click();
        }
    </script>
</body>
</html>
