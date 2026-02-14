<?php
session_start();
include '../db/config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Stats Calculation
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) t FROM bookings"))['t'] ?? 0;
$pending = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) p FROM bookings WHERE status='Pending'"))['p'] ?? 0;
$completed = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM bookings WHERE status='Completed'"))['c'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | MyLab</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary: #0d6efd;
            --bg-body: #f4f7fe;
            --sidebar-bg: #1e293b;
            --white: #ffffff;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            margin: 0;
            display: flex;
            color: #1e293b;
        }

        /* --- SIDEBAR DESIGN --- */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 25px 15px;
            box-sizing: border-box;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-brand {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 10px;
        }

        .nav-menu {
            flex-grow: 1; /* Pushes everything below to bottom */
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 8px;
            font-size: 15px;
            transition: 0.3s ease;
        }

        .nav-link i { width: 25px; font-size: 18px; }

        .nav-link:hover, .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            font-size: 14px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            transition: 0.3s;
            display: block;
        }

        .logout-btn:hover {
            background: #ef4444;
            color: white;
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 35px;
            box-sizing: border-box;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        header h2 { margin: 0; font-size: 24px; font-weight: 700; }

        /* --- STATS CARDS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.02);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .icon-total { background: #e0e7ff; color: #4338ca; }
        .icon-pending { background: #fef3c7; color: #d97706; }
        .icon-done { background: #dcfce7; color: #15803d; }

        .stat-info h3 { margin: 0; font-size: 13px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-info h2 { margin: 5px 0 0 0; font-size: 30px; font-weight: 800; }

        /* --- TOOLBAR (Search & Export) --- */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            gap: 20px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 500px; /* Limits search width to prevent overlap */
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            outline: none;
            background: #f8fafc;
            font-size: 14px;
            transition: 0.3s;
        }

        .search-box input:focus { border-color: var(--primary); background: white; }

        .search-box i { position: absolute; left: 18px; top: 15px; color: #94a3b8; }

        .action-btns { display: flex; gap: 10px; }

        .btn-export {
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: white;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-export:hover { background: #f1f5f9; border-color: #cbd5e1; }

        /* --- TABLE DESIGN --- */
        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 18px 20px; text-align: left; font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }
        td { padding: 18px 20px; border-top: 1px solid #f1f5f9; font-size: 14px; }
        tr:hover { background-color: #fcfdfe; }

        .badge { padding: 6px 12px; border-radius: 30px; font-size: 11px; font-weight: 700; }
        .badge-pending { background: #fff7ed; color: #c2410c; }
        .badge-done { background: #f0fdf4; color: #15803d; }

        .action-icon {
            padding: 8px;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            margin-right: 5px;
            transition: 0.2s;
        }
        .btn-sync { background: #e0f2fe; color: #0284c7; }
        .btn-del { background: #fee2e2; color: #dc2626; }
        .action-icon:hover { opacity: 0.8; transform: scale(1.1); }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-microscope" style="color: var(--primary);"></i> MyLab Admin
        </div>
        
        <div class="nav-menu">
            <a href="index.php" class="nav-link active">
                <i class="fas fa-clipboard-list"></i> Bookings
            </a>
            <a href="packages.php" class="nav-link">
                <i class="fas fa-box"></i> Packages
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-users"></i> Users
            </a>
        </div>

        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h2>Booking Dashboard</h2>
            <div style="color: var(--text-muted); font-weight: 500;">
                <i class="far fa-calendar-alt"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </header>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-total"><i class="fas fa-folder-open"></i></div>
                <div class="stat-info">
                    <h3>Total Bookings</h3>
                    <h2><?php echo $total; ?></h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-pending"><i class="fas fa-spinner"></i></div>
                <div class="stat-info">
                    <h3>Pending</h3>
                    <h2><?php echo $pending; ?></h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-done"><i class="fas fa-check-double"></i></div>
                <div class="stat-info">
                    <h3>Completed</h3>
                    <h2><?php echo $completed; ?></h2>
                </div>
            </div>
        </div>

        <!-- Toolbar: Search + Export Buttons Fix -->
        <div class="toolbar">
            <form class="search-box" method="GET">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search by name, mobile or test..." value="<?php echo @$_GET['search']; ?>">
            </form>
            <div class="action-btns">
                <a href="export.php" class="btn-export"><i class="fas fa-file-csv"></i> CSV</a>
                <a href="pdf.php" class="btn-export"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient Details</th>
                        <th>Test Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if(isset($_GET['search'])){
                        $s = mysqli_real_escape_string($conn, $_GET['search']);
                        $search_query = " WHERE name LIKE '%$s%' OR mobile LIKE '%$s%' OR test LIKE '%$s%'";
                    }
                    $q = mysqli_query($conn,"SELECT * FROM bookings $search_query ORDER BY id DESC");

                    $i = 1;
                    while($row = mysqli_fetch_assoc($q)){
                        $stClass = ($row['status']=='Completed') ? 'badge-done' : 'badge-pending';
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <div style="font-weight:600;"><?php echo $row['name']; ?></div>
                            <small style="color: var(--text-muted);"><?php echo $row['mobile']; ?></small>
                        </td>
                        <td><?php echo $row['test']; ?></td>
                        <td><?php echo date('d M, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <span class="badge <?php echo $stClass; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <a title="Change Status" class="action-icon btn-sync" href="status.php?id=<?php echo $row['id']; ?>&s=<?php echo $row['status']; ?>">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                            <a title="Delete" class="action-icon btn-del" href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this record?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>