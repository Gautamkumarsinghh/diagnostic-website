<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../db/config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';

function getCount($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    return ($res) ? mysqli_num_rows($res) : 0;
}

$count_all = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id'");
$count_pending = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id' AND LOWER(status) NOT IN ('completed', 'cancelled')");
$count_completed = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id' AND LOWER(status) = 'completed'");
$count_cancelled = getCount($conn, "SELECT id FROM bookings WHERE user_id = '$user_id' AND LOWER(status) = 'cancelled'");

$sql = "SELECT * FROM bookings WHERE user_id = '$user_id'";
if ($filter !== 'All') {
    $f = strtolower($filter);
    if ($f === 'pending') {
        $sql .= " AND LOWER(status) NOT IN ('completed', 'cancelled')";
    } else {
        $sql .= " AND LOWER(status) = '$f'";
    }
}
$sql .= " ORDER BY created_at DESC"; 
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | MyLab Diagnostic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Chart.js for Smart Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3f4f6; }
        
        /* Sidebar Colorful Icon Styles */
        .nav-link { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
        .nav-link i { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 10px; transition: all 0.3s; }
        .icon-bookings { background: #e0f2fe; color: #0ea5e9; }
        .icon-address { background: #fef3c7; color: #d97706; }
        .icon-members { background: #f0fdf4; color: #22c55e; }
        .icon-reports { background: #fae8ff; color: #a855f7; }

        .nav-link.active { background: #ffffff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border-left: 4px solid #2563eb; color: #1e293b !important; }
        .nav-link.active span { font-weight: 700; color: #1e293b; }
        .nav-link:hover:not(.active) { background: #f9fafb; transform: translateX(5px); }
        
        .booking-card { transition: transform 0.2s, box-shadow 0.2s; }
        .booking-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        
        .tab-view { animation: fade-in 0.4s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        #map-container { height: 100%; width: 100%; z-index: 1; border-radius: 1.5rem; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="text-slate-900 flex flex-col min-h-screen">

<?php include '../header.php'; ?>

<div class="flex-grow max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-72 shrink-0">
            <div class="bg-white md:bg-white/70 md:backdrop-blur-xl rounded-[2rem] p-2 sm:p-4 shadow-sm border border-slate-100 sticky top-24 z-20">
                <nav class="flex md:flex-col overflow-x-auto md:overflow-x-visible gap-2 no-scrollbar px-1 py-1">
                    <a onclick="switchTab('bookings')" id="btn-bookings" class="nav-link active flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-2xl group whitespace-nowrap md:whitespace-normal cursor-pointer flex-1 md:flex-none justify-center md:justify-start min-w-[140px] md:min-w-0">
                        <i class="fa-solid fa-calendar-check text-blue-600 bg-blue-50 w-10 h-10 flex items-center justify-center rounded-xl transition-colors group-hover:bg-blue-600 group-hover:text-white"></i>
                        <span class="font-extrabold text-sm sm:text-base text-slate-500 group-hover:text-slate-800">My Bookings</span>
                    </a>
                    <a onclick="switchTab('address')" id="btn-address" class="nav-link flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-2xl group whitespace-nowrap md:whitespace-normal cursor-pointer flex-1 md:flex-none justify-center md:justify-start min-w-[140px] md:min-w-0">
                        <i class="fa-solid fa-location-dot text-amber-600 bg-amber-50 w-10 h-10 flex items-center justify-center rounded-xl transition-colors group-hover:bg-amber-600 group-hover:text-white"></i>
                        <span class="font-extrabold text-sm sm:text-base text-slate-500 group-hover:text-slate-800">Addresses</span>
                    </a>
                    <a onclick="switchTab('members')" id="btn-members" class="nav-link flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-2xl group whitespace-nowrap md:whitespace-normal cursor-pointer flex-1 md:flex-none justify-center md:justify-start min-w-[140px] md:min-w-0">
                        <i class="fa-solid fa-users text-emerald-600 bg-emerald-50 w-10 h-10 flex items-center justify-center rounded-xl transition-colors group-hover:bg-emerald-600 group-hover:text-white"></i>
                        <span class="font-extrabold text-sm sm:text-base text-slate-500 group-hover:text-slate-800">Family</span>
                    </a>
                    <a onclick="switchTab('reports')" id="btn-reports" class="nav-link flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-2xl group whitespace-nowrap md:whitespace-normal cursor-pointer flex-1 md:flex-none justify-center md:justify-start min-w-[140px] md:min-w-0">
                        <i class="fa-solid fa-file-medical text-rose-600 bg-rose-50 w-10 h-10 flex items-center justify-center rounded-xl transition-colors group-hover:bg-rose-600 group-hover:text-white"></i>
                        <span class="font-extrabold text-sm sm:text-base text-slate-500 group-hover:text-slate-800">Reports</span>
                    </a>
                </nav>
            </div>
            
            <!-- User Info Summary -->
            <div class="mt-6 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-6 text-white shadow-lg shadow-blue-200 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <h4 class="font-extrabold text-lg mb-1 relative z-10"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h4>
                <p class="text-xs text-blue-200 uppercase tracking-widest font-bold relative z-10">Patient ID: #<?php echo $_SESSION['user_id']; ?></p>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 w-full min-w-0">
            
            <!-- VIEW 1: BOOKINGS -->
            <div id="view-bookings" class="tab-view block">
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Booking History</h1>
                    <p class="text-slate-500 mt-1">Manage and track your diagnostic appointments.</p>
                </div>

                <!-- Modern Filter Tabs -->
                <div class="flex flex-wrap items-center gap-3 mb-8 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 inline-flex">
                    <?php 
                    $tabs = [
                        'All' => $count_all, 
                        'Pending' => $count_pending, 
                        'Completed' => $count_completed, 
                        'Cancelled' => $count_cancelled
                    ];
                    foreach($tabs as $label => $count): 
                        $isActive = ($filter == $label);
                    ?>
                        <a href="?filter=<?php echo $label; ?>" 
                           class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2
                           <?php echo $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-slate-50'; ?>">
                            <?php echo $label; ?>
                            <span class="px-2 py-0.5 rounded-md text-[11px] <?php echo $isActive ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-500'; ?>">
                                <?php echo $count; ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Bookings List -->
                <div class="space-y-4">
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $status = strtolower(trim($row['status'] ?? 'booked'));
                            $statusStyles = [
                                'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                                'pending'   => 'bg-amber-50 text-amber-700 border-amber-100',
                                'booked'    => 'bg-blue-50 text-blue-700 border-blue-100',
                                'collected' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                'in lab'    => 'bg-purple-50 text-purple-700 border-purple-100'
                            ];
                            $currentStyle = $statusStyles[$status] ?? 'bg-slate-50 text-slate-700 border-slate-100';
                            
                            // Progress bar calculation
                            $steps = ['booked', 'collected', 'in lab', 'completed'];
                            $current_idx = array_search($status, $steps);
                            if($current_idx === false) $current_idx = 0; // default
                            $progress_percent = (($current_idx + 1) / count($steps)) * 100;
                        ?>
                            <div class="booking-card bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm space-y-5">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="flex items-center gap-4 sm:gap-5 w-full">
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner shrink-0">
                                        <i class="fa-solid fa-microscope text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-black text-base sm:text-lg text-slate-800 mb-0.5 truncate"><?php echo htmlspecialchars($row['test']); ?></h3>
                                        <div class="flex flex-wrap gap-x-4 gap-y-1 items-center mt-1 sm:mt-2">
                                            <div class="flex items-center text-[11px] sm:text-sm font-bold text-slate-500">
                                                <i class="fa-regular fa-calendar-check mr-2 text-blue-500"></i>
                                                <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                            </div>
                                            <div class="flex items-center text-[10px] sm:text-sm font-black text-slate-400">
                                                <span class="bg-slate-100 px-1.5 py-0.5 rounded text-[9px] mr-2 text-slate-500 uppercase font-black">ID</span>
                                                #<?php echo $row['id']; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-50">
                                    <span class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-[9px] sm:text-[11px] font-black tracking-widest uppercase border <?php echo $currentStyle; ?>">
                                        <?php echo htmlspecialchars($row['status'] ?? 'Booked'); ?>
                                    </span>
                                    <a href="invoice.php?id=<?php echo $row['id']; ?>" target="_blank" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition border border-slate-100" title="Download Invoice">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </a>
                                </div>
                            </div>
                                
                                <!-- LIVE TRACKER BAR -->
                                <?php if($status !== 'cancelled'): ?>
                                <div class="pt-4 border-t border-slate-50">
                                    <div class="flex justify-between mb-3">
                                        <span class="text-[10px] font-black uppercase text-blue-600 tracking-tighter">Live Status Tracking</span>
                                        <span class="text-[10px] font-bold text-slate-400"><?php echo round($progress_percent); ?>% Complete</span>
                                    </div>
                                    <div class="relative h-2.5 bg-slate-100 rounded-full overflow-hidden mb-6">
                                        <div class="absolute h-full bg-blue-600 rounded-full transition-all duration-1000" style="width: <?php echo $progress_percent; ?>%"></div>
                                    </div>
                                    
                                    <!-- Stepper Icons -->
                                    <div class="flex justify-between items-start relative px-1">
                                        <?php 
                                        $stepper = [
                                            ['label' => 'Booked', 'icon' => 'fa-check-circle'],
                                            ['label' => 'Collected', 'icon' => 'fa-vial'],
                                            ['label' => 'In Lab', 'icon' => 'fa-flask-vial'],
                                            ['label' => 'Completed', 'icon' => 'fa-file-circle-check']
                                        ];
                                        foreach($stepper as $idx => $s):
                                            $isDone = ($idx <= $current_idx);
                                            $isActive = ($idx === $current_idx);
                                        ?>
                                        <div class="flex flex-col items-center gap-2 group">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs transition-all
                                                <?php echo $isDone ? 'bg-blue-600 text-white animate-pulse' : 'bg-slate-200 text-slate-400'; ?>">
                                                <i class="fa-solid <?php echo $s['icon']; ?>"></i>
                                            </div>
                                            <span class="text-[9px] font-black uppercase tracking-widest text-center 
                                                <?php echo $isActive ? 'text-blue-600' : ($isDone ? 'text-slate-600' : 'text-slate-300'); ?>">
                                                <?php echo $s['label']; ?>
                                            </span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="text-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-slate-200">
                            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300"></i>
                            </div>
                            <h2 class="text-xl font-bold text-slate-800">No appointments found</h2>
                            <p class="text-slate-500 mt-2 max-w-xs mx-auto">You haven't booked any tests recently.</p>
                            <a href="../index.php" class="inline-block mt-8 bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">Book New Test</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- VIEW 2: ADDRESS -->
            <div id="view-address" class="tab-view hidden">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">My Addresses</h2>
                        <p class="text-slate-500 mt-1">Manage locations for home sample collection.</p>
                    </div>
                    <button onclick="toggleMapModal(true)" class="bg-blue-600 text-white px-6 py-3.5 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus text-sm"></i> Add Address
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php
                    $addr_q = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id = '$user_id' ORDER BY id DESC");
                    if(mysqli_num_rows($addr_q) > 0):
                        while($addr = mysqli_fetch_assoc($addr_q)):
                    ?>
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm booking-card">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-widest border border-blue-100">
                                <i class="fa-solid fa-location-dot mr-1"></i> <?php echo htmlspecialchars($addr['title']); ?>
                            </div>
                            <div class="flex gap-2 text-slate-400">
                                <button onclick="deleteAddress(<?php echo $addr['id']; ?>)" class="hover:text-rose-600 transition p-1"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        </div>
                        <h4 class="font-bold text-lg text-slate-800 mb-2"><?php echo htmlspecialchars($addr['title']); ?></h4>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            <?php echo htmlspecialchars($addr['address_line']); ?>
                            <?php if(!empty($addr['landmark'])) echo "<br><span class='text-xs text-slate-400 font-bold uppercase'>Landmark:</span> " . htmlspecialchars($addr['landmark']); ?>
                            <br><span class='text-xs text-slate-400 font-bold uppercase'>Pincode:</span> <?php echo htmlspecialchars($addr['pincode']); ?>
                        </p>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="col-span-full text-center py-10 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-100">
                        <p class="text-slate-400 font-bold">No saved addresses found. Add one to get started!</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- VIEW 3: MEMBERS -->
            <div id="view-members" class="tab-view hidden">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Family Members</h2>
                        <p class="text-slate-500 mt-1">Add members to easily book tests for them.</p>
                    </div>
                    <button onclick="toggleMemberModal(true)" class="bg-emerald-500 text-white px-6 py-3.5 rounded-2xl font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-600 transition active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-plus text-sm"></i> Add Member
                    </button>
                </div>
                
                <div id="members-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Sample Member 1 -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm booking-card flex items-center gap-5">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 flex items-center justify-center rounded-2xl font-black text-xl shadow-inner shrink-0">Me</div>
                        <div>
                            <h4 class="font-bold text-lg text-slate-800 leading-tight"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h4>
                            <div class="flex items-center gap-2 mt-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                                <span class="bg-slate-100 px-2.5 py-1 rounded-md text-slate-500">Self</span>
                                <span>28 Yrs</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Empty State for adding new -->
                    <div id="add-member-card" onclick="toggleMemberModal(true)" class="bg-slate-50 p-6 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center gap-3 text-slate-400 hover:text-blue-500 hover:border-blue-200 hover:bg-blue-50 transition cursor-pointer booking-card group">
                        <div class="w-12 h-12 rounded-full bg-white border border-slate-200 flex items-center justify-center text-xl group-hover:border-blue-200 shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <span class="font-bold text-sm">Add New Member</span>
                    </div>
                </div>
            </div>

            <!-- VIEW 4: REPORTS -->
            <div id="view-reports" class="tab-view hidden">
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">My Reports</h1>
                    <p class="text-slate-500 mt-1">Access all your medical test results digitally.</p>
                </div>
                
                <div class="space-y-4">
                    <?php
                    $reports_query = "SELECT * FROM bookings WHERE user_id = '$user_id' AND report_file IS NOT NULL AND report_file != '' ORDER BY created_at DESC";
                    $reports_res = mysqli_query($conn, $reports_query);

                    if($reports_res && mysqli_num_rows($reports_res) > 0):
                        while($rpt = mysqli_fetch_assoc($reports_res)):
                    ?>
                    <!-- Dynamic Report -->
                    <div class="bg-white p-5 lg:p-6 rounded-3xl border border-slate-100 shadow-sm booking-card flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center text-2xl shadow-inner shrink-0">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-slate-800 leading-none mb-2"><?php echo htmlspecialchars($rpt['test']); ?></h3>
                                <div class="flex items-center gap-4 text-sm font-medium text-slate-500">
                                    <span><i class="fa-regular fa-calendar-alt mr-1.5 text-blue-500"></i> Generated: <?php echo date('d M Y', strtotime($rpt['created_at'])); ?></span>
                                    <span class="hidden sm:inline"><i class="fa-regular fa-user mr-1.5 text-blue-500"></i> <?php echo htmlspecialchars($rpt['name']); ?></span>
                                </div>
                            </div>
                        </div>
                        <a href="../uploads/reports/<?php echo htmlspecialchars($rpt['report_file']); ?>" download class="w-full sm:w-auto bg-blue-50 text-blue-600 px-6 py-3 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-blue-600 hover:text-white transition-colors">
                            <i class="fa-solid fa-download"></i> Download PDF
                        </a>
                    </div>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <!-- Empty State for Reports -->
                        <div class="text-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-slate-200">
                            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-file-invoice text-4xl text-slate-300"></i>
                            </div>
                            <h2 class="text-xl font-bold text-slate-800">No reports available</h2>
                            <p class="text-slate-500 mt-2 max-w-xs mx-auto">Your test reports will be available here once they are processed by the lab.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<!-- Modern Address Map Modal -->
<div id="addressModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-[100] p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.2)] transform transition-all scale-95 opacity-0 duration-300">
        <div class="p-6 flex justify-between items-center border-b border-slate-50">
            <h3 class="font-extrabold text-2xl text-slate-800">Add New Address</h3>
            <button onclick="toggleMapModal(false)" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-500 transition-colors">✕</button>
        </div>
        
        <div class="p-6 max-h-[80vh] overflow-y-auto no-scrollbar">
            <!-- GPS & Map Section -->
            <div class="flex gap-3 mb-4">
                <button onclick="detectMyLocation()" class="flex-1 bg-amber-50 text-amber-600 py-3 rounded-xl font-bold text-sm border border-amber-100 flex items-center justify-center gap-2 hover:bg-amber-100">
                    <i class="fa-solid fa-location-crosshairs"></i> Detect My Location
                </button>
            </div>

            <div class="relative h-48 rounded-[1.5rem] overflow-hidden border border-slate-100 mb-4 shadow-inner">
                <div id="map-container"></div>
                <div class="absolute top-2 left-2 z-[500] bg-white/90 backdrop-blur px-2 py-1 rounded-md text-[10px] font-bold text-slate-500 shadow-sm border border-slate-100">
                    <i class="fa-solid fa-hand-pointer mr-1"></i> Tap map to move pin
                </div>
            </div>
            
            <!-- Manual Form -->
            <form id="addressForm" onsubmit="event.preventDefault(); saveAddress();" class="space-y-4">
                <input type="hidden" id="addr_lat" name="lat" value="20.3778">
                <input type="hidden" id="addr_lng" name="lng" value="72.9038">

                <div class="grid grid-cols-1 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Title (e.g. Home / Office)</label>
                        <input type="text" name="title" id="addr_title" placeholder="Home / Office" required class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-semibold">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Address / Flat No / Street</label>
                    <textarea name="address" id="addr_line" rows="2" placeholder="Enter complete address" required class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-semibold resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Landmark (Optional)</label>
                        <input type="text" name="landmark" id="addr_landmark" placeholder="Near by..." class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-semibold">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pincode</label>
                        <input type="text" name="pincode" id="addr_pincode" placeholder="6 Digit Code" required maxlength="6" pattern="[0-9]{6}" class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-semibold">
                    </div>
                </div>

                <div class="flex items-center gap-2 bg-blue-50 border border-blue-100 p-3 rounded-xl mb-4">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    <p class="text-[10px] font-bold text-blue-800 leading-tight">GPS Coordinates: <span id="coords-text" class="font-mono">20.3778, 72.9038</span></p>
                </div>

                <button type="submit" id="save-addr-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-[1.5rem] font-bold shadow-xl shadow-blue-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check-circle"></i> Save Address
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modern Add Member Modal -->
<div id="memberModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-[100] p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.2)] transform transition-all scale-95 opacity-0 duration-300">
        <div class="p-6 flex justify-between items-center border-b border-slate-50">
            <h3 class="font-extrabold text-2xl text-slate-800">Add Family Member</h3>
            <button onclick="toggleMemberModal(false)" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-500 transition-colors">✕</button>
        </div>
        
        <div class="p-6">
            <form class="space-y-4" onsubmit="event.preventDefault(); saveMockMember();">
                <!-- Name -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Full Name</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input id="member-name" type="text" placeholder="Enter member name" required 
                               class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-medium text-sm">
                    </div>
                </div>

                <!-- Relationship -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Relationship</label>
                    <div class="relative">
                        <i class="fa-solid fa-users absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select id="member-relation" required class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-medium text-sm appearance-none">
                            <option value="">Select Relationship</option>
                            <option value="father">Father</option>
                            <option value="mother">Mother</option>
                            <option value="spouse">Spouse</option>
                            <option value="child">Child</option>
                            <option value="sibling">Sibling</option>
                            <option value="other">Other</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs text-sm pointer-events-none"></i>
                    </div>
                </div>

                <!-- Age & Gender Row -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Age</label>
                        <div class="relative">
                            <i class="fa-solid fa-cake-candles absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input id="member-age" type="number" placeholder="Years" required min="1" max="120"
                                   class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-medium text-sm">
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Gender</label>
                        <div class="relative">
                            <i class="fa-solid fa-venus-mars absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="member-gender" required class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-medium text-sm appearance-none">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none z-10"></i>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <button type="submit" class="w-full mt-6 bg-emerald-500 hover:bg-emerald-600 text-white py-4 rounded-[1.5rem] font-bold shadow-xl shadow-emerald-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> Add Member
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    let map, marker;

    function toggleMapModal(show) {
        const modal = document.getElementById('addressModal');
        const modalInner = modal.querySelector('div');
        
        if(show) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalInner.classList.remove('scale-95', 'opacity-0');
                modalInner.classList.add('scale-100', 'opacity-100');
                initMap();
            }, 10);
        } else {
            modalInner.classList.add('scale-95', 'opacity-0');
            modalInner.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    }

    function initMap() {
        if (!map) {
            map = L.map('map-container', { zoomControl: false }).setView([20.3778, 72.9038], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            marker = L.marker([20.3778, 72.9038], { draggable: true }).addTo(map);
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            // Update coords on marker drag
            marker.on('dragend', function(e) {
                const latlng = marker.getLatLng();
                updateCoords(latlng.lat, latlng.lng);
            });

            // Update marker on map click
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateCoords(e.latlng.lat, e.latlng.lng);
            });
        } else { 
            setTimeout(() => map.invalidateSize(), 150); 
        }
    }

    function updateCoords(lat, lng) {
        document.getElementById('addr_lat').value = lat;
        document.getElementById('addr_lng').value = lng;
        document.getElementById('coords-text').innerText = lat.toFixed(4) + ', ' + lng.toFixed(4);
    }

    function detectMyLocation() {
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser");
            return;
        }

        const btn = event.currentTarget;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Detecting...';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.setView([lat, lng], 17);
                marker.setLatLng([lat, lng]);
                updateCoords(lat, lng);
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            },
            (error) => {
                alert("Unable to retrieve your location. Check GPS settings.");
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        );
    }

    function saveAddress() {
        const btn = document.getElementById('save-addr-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Saving...';

        const formData = new FormData(document.getElementById('addressForm'));
        
        fetch('add_address_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Save Address';
            }
        });
    }

    function deleteAddress(id) {
        if(!confirm('Are you sure you want to delete this address?')) return;
        
        const fd = new FormData();
        fd.append('id', id);

        fetch('delete_address_ajax.php', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    // Member Modal Logic
    function toggleMemberModal(show) {
        const modal = document.getElementById('memberModal');
        const modalInner = modal.querySelector('div');
        
        if(show) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalInner.classList.remove('scale-95', 'opacity-0');
                modalInner.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            modalInner.classList.add('scale-95', 'opacity-0');
            modalInner.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    }

    function saveMockMember() {
        const nameInput = document.getElementById('member-name');
        const relationInput = document.getElementById('member-relation');
        const ageInput = document.getElementById('member-age');
        
        const name = nameInput.value;
        const relation = relationInput.options[relationInput.selectedIndex].text;
        const age = ageInput.value;
        const initials = name.substring(0, 2).toUpperCase();

        const newMemberHTML = `
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm booking-card flex items-center gap-5 tab-view">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 flex items-center justify-center rounded-2xl font-black text-xl shadow-inner shrink-0">${initials}</div>
                <div>
                    <h4 class="font-bold text-lg text-slate-800 leading-tight">${name}</h4>
                    <div class="flex items-center gap-2 mt-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                        <span class="bg-slate-100 px-2.5 py-1 rounded-md text-slate-500">${relation}</span>
                        <span>${age} Yrs</span>
                    </div>
                </div>
            </div>
        `;

        const grid = document.getElementById('members-grid');
        const addCard = document.getElementById('add-member-card');
        
        // Insert new member right before the "Add New Member" button
        addCard.insertAdjacentHTML('beforebegin', newMemberHTML);

        // Reset form
        nameInput.value = '';
        ageInput.value = '';
        relationInput.selectedIndex = 0;
        document.getElementById('member-gender').selectedIndex = 0;

        alert('Family member added successfully!');
        toggleMemberModal(false);
    }

    // Simple script to switch tabs on the user dashboard
    function switchTab(tabId) {
        // Hide all views
        const views = document.querySelectorAll('.tab-view');
        views.forEach(view => {
            view.classList.remove('block');
            view.classList.add('hidden');
        });

        // Remove active state from all buttons
        const btns = document.querySelectorAll('.nav-link');
        btns.forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected view and activate button
        document.getElementById('view-' + tabId).classList.remove('hidden');
        document.getElementById('view-' + tabId).classList.add('block');
        document.getElementById('btn-' + tabId).classList.add('active');
    }

    // Ensure we stay on 'bookings' tab when a filter is applied or switch to specified tab
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('tab')) {
        switchTab(urlParams.get('tab'));
    } else if(urlParams.has('filter')) {
        switchTab('bookings');
    }



</script>

</body>
</html>