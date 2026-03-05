<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get the month and year from URL or use current
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Calculate previous and next month
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month == 13) {
    $next_month = 1;
    $next_year++;
}

// Fetch bookings for this month
$start_date = "$year-$month-01";
$end_date = "$year-$month-" . cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Schema Sync: Ensure all needed columns exist
mysqli_query($conn, "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS is_urgent TINYINT(1) DEFAULT 0");
mysqli_query($conn, "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS staff_id INT DEFAULT 0");
mysqli_query($conn, "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS timeslot VARCHAR(100) AFTER booking_date");

// Create Holidays table if not exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS holidays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    holiday_date DATE UNIQUE,
    reason VARCHAR(255)
)");

// Handle Holiday Marking
if(isset($_GET['mark_holiday'])) {
    $h_date = mysqli_real_escape_string($conn, $_GET['mark_holiday']);
    mysqli_query($conn, "INSERT IGNORE INTO holidays (holiday_date, reason) VALUES ('$h_date', 'Public Holiday')");
    header("Location: booking_calendar.php?month=$month&year=$year");
    exit();
}
if(isset($_GET['remove_holiday'])) {
    $h_date = mysqli_real_escape_string($conn, $_GET['remove_holiday']);
    mysqli_query($conn, "DELETE FROM holidays WHERE holiday_date = '$h_date'");
    header("Location: booking_calendar.php?month=$month&year=$year");
    exit();
}

// Fetch holidays for this month
$holidays_res = mysqli_query($conn, "SELECT holiday_date FROM holidays WHERE holiday_date BETWEEN '$start_date' AND '$end_date'");
$holidays = [];
while($h = mysqli_fetch_assoc($holidays_res)) $holidays[] = $h['holiday_date'];

// Fetch detailed bookings for this month
$query = "SELECT 
            booking_date, 
            COUNT(*) as total_count,
            SUM(amount) as total_revenue,
            SUM(CASE WHEN timeslot LIKE 'Morning%' THEN 1 ELSE 0 END) as morning_slots,
            SUM(CASE WHEN timeslot LIKE 'Day%' THEN 1 ELSE 0 END) as day_slots,
            SUM(CASE WHEN timeslot LIKE 'Evening%' THEN 1 ELSE 0 END) as evening_slots,
            MAX(is_urgent) as has_urgent,
            SUM(CASE WHEN staff_id > 0 THEN 1 ELSE 0 END) as assigned_count
          FROM bookings 
          WHERE booking_date BETWEEN '$start_date' AND '$end_date' 
          GROUP BY booking_date";
$res = mysqli_query($conn, $query);

$booked_dates = [];
while($row = mysqli_fetch_assoc($res)) {
    $booked_dates[$row['booking_date']] = $row;
}

// Calendar Calculation
$first_day_of_month = mktime(0, 0, 0, $month, 1, $year);
$number_days = date('t', $first_day_of_month);
$day_of_week = date('w', $first_day_of_month);
$month_name = date('F', $first_day_of_month);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Calendar | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .calendar-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .day-box {
            min-height: 120px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .day-box:hover {
            background: #fdfdfd;
            transform: translateY(-2px);
            z-index: 10;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
        }
        .booking-badge {
            animation: pulse-blue 2s infinite;
        }
        @keyframes pulse-blue {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="text-blue-600 font-extrabold text-sm uppercase tracking-[0.2em]">Operational Insights</span>
                </div>
                <h1 class="text-4xl font-black text-slate-800 tracking-tight">Booking Calendar</h1>
            </div>
            
            <div class="flex items-center gap-1 bg-white p-1.5 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="w-12 h-12 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="px-6 py-2 text-center min-w-[180px]">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1"><?php echo $year; ?></div>
                    <div class="text-lg font-black text-slate-800 leading-none"><?php echo $month_name; ?></div>
                </div>
                <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="w-12 h-12 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Legend & Stats -->
        <div class="flex flex-wrap gap-6 mb-8 px-2">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-blue-600 booking-badge"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Has Bookings</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Urgent Needed</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-slate-300"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Public Holiday</span>
            </div>
        </div>

        <div class="calendar-card rounded-[3rem] shadow-2xl shadow-slate-200/40 overflow-hidden">
            <!-- Day Labels -->
            <div class="grid grid-cols-7 bg-slate-800/5 backdrop-blur-md border-b border-slate-100">
                <?php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; 
                foreach($days as $day): ?>
                    <div class="p-6 text-center text-[11px] font-black text-slate-500 uppercase tracking-[0.15em]"><?php echo $day; ?></div>
                <?php endforeach; ?>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 group/grid">
                <?php 
                // Empty slots before first day
                for($i=0; $i<$day_of_week; $i++): ?>
                    <div class="day-box p-4 border-r border-b border-slate-100/50 bg-slate-50/10"></div>
                <?php endfor;

                // Calendar days
                for($day=1; $day<=$number_days; $day++): 
                    $current_date = sprintf("%04d-%02d-%02d", $year, $month, $day);
                    $data = $booked_dates[$current_date] ?? null;
                    $is_holiday = in_array($current_date, $holidays);
                    $is_today = ($current_date == date('Y-m-d'));
                    
                    $border_class = ($data && $data['has_urgent']) ? 'border-red-200 bg-red-50/30' : 'border-slate-100';
                    $day_color = $is_today ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 scale-110' : ($is_holiday ? 'bg-slate-200 text-slate-500' : 'text-slate-300 group-hover/day:text-slate-800');
                ?>
                    <div class="day-box p-4 border-r border-b <?php echo $border_class; ?> relative group/day <?php echo $is_holiday ? 'bg-slate-50/50' : ''; ?> transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <span class="w-8 h-8 flex items-center justify-center rounded-xl font-black text-sm transition-all <?php echo $day_color; ?>">
                                <?php echo $day; ?>
                            </span>
                            
                            <div class="flex gap-1">
                                <?php if($is_holiday): ?>
                                    <a href="?remove_holiday=<?php echo $current_date; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>" title="Remove Holiday" class="p-1 text-red-400 hover:text-red-600 opacity-0 group-hover/day:opacity-100">
                                        <i class="fas fa-calendar-times"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="?mark_holiday=<?php echo $current_date; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>" title="Mark Holiday" class="p-1 text-slate-300 hover:text-blue-500 opacity-0 group-hover/day:opacity-100">
                                        <i class="fas fa-mug-hot"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($is_holiday): ?>
                            <div class="absolute inset-x-0 bottom-4 text-center">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest border border-slate-200 px-2 py-1 rounded-full">Holiday</span>
                            </div>
                        <?php elseif($data): ?>
                            <!-- Analytics Overlay -->
                            <div class="space-y-3 relative z-10">
                                <!-- Revenue & Urgency -->
                                <div class="flex justify-between items-center bg-white/50 p-2 rounded-xl border border-white/80">
                                    <div class="text-[10px] font-black text-emerald-600">₹<?php echo number_format($data['total_revenue']); ?></div>
                                    <?php if($data['has_urgent']): ?>
                                        <i class="fas fa-exclamation-circle text-red-500 text-[10px] animate-pulse"></i>
                                    <?php endif; ?>
                                </div>

                                <!-- Slots Progress -->
                                <div class="grid grid-cols-3 gap-1">
                                    <div class="h-1 rounded-full <?php echo $data['morning_slots'] > 0 ? 'bg-amber-400' : 'bg-slate-100'; ?>" title="Morning: <?php echo $data['morning_slots']; ?>"></div>
                                    <div class="h-1 rounded-full <?php echo $data['day_slots'] > 0 ? 'bg-blue-400' : 'bg-slate-100'; ?>" title="Day: <?php echo $data['day_slots']; ?>"></div>
                                    <div class="h-1 rounded-full <?php echo $data['evening_slots'] > 0 ? 'bg-indigo-400' : 'bg-slate-100'; ?>" title="Evening: <?php echo $data['evening_slots']; ?>"></div>
                                </div>

                                <a href="index.php?date=<?php echo $current_date; ?>" class="flex items-center justify-between bg-slate-900 text-white rounded-xl p-2 h-10 group/btn hover:bg-blue-600 transition-all">
                                    <span class="text-xs font-black ml-1"><?php echo $data['total_count']; ?></span>
                                    <div class="flex items-center gap-1">
                                        <?php if($data['assigned_count'] > 0): ?>
                                            <i class="fas fa-motorcycle text-[8px] opacity-70"></i>
                                        <?php endif; ?>
                                        <i class="fas fa-chevron-right text-[8px] mr-1 opacity-0 group-hover/btn:opacity-100 transition-opacity"></i>
                                    </div>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="mt-4 opacity-0 group-hover/day:opacity-100">
                                <div class="text-[10px] text-slate-400 font-bold text-center border border-dashed border-slate-200 rounded-xl py-2 uppercase tracking-wide">Available</div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endfor; 

                // Empty slots at the end
                $remaining_days = (7 - (($day_of_week + $number_days) % 7)) % 7;
                for($i=0; $i<$remaining_days; $i++): ?>
                    <div class="day-box p-4 border-r border-b border-slate-100/50 bg-slate-50/10"></div>
                <?php endfor; ?>
            </div>
        </div>
        
        <!-- Bottom Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <div class="p-8 bg-blue-900 rounded-[3rem] text-white col-span-2 flex items-center justify-between gap-6 shadow-xl">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center text-3xl">
                        <i class="fas fa-chart-pie text-blue-300"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-black mb-1">Monthly Outlook</h4>
                        <p class="text-blue-100/70 text-sm font-medium">Use the slot bars (Amber/Blue/Indigo) to judge peak hours.</p>
                    </div>
                </div>
                <a href="index.php" class="px-6 py-3 bg-white text-blue-900 rounded-2xl font-black hover:bg-blue-50 text-sm">View List</a>
            </div>

            <div class="p-8 bg-white rounded-[3rem] border border-slate-100 shadow-sm flex flex-col justify-center">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Month Revenue</div>
                <?php 
                    $monthly_revenue = 0;
                    foreach($booked_dates as $d) $monthly_revenue += $d['total_revenue'];
                ?>
                <div class="text-3xl font-black text-slate-800">₹<?php echo number_format($monthly_revenue); ?></div>
            </div>
        </div>
    </div>

</body>
</html>
