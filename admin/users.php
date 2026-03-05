<?php
session_start();
include '../db/config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Stats Calculation
$total_users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) t FROM users"))['t'] ?? 0;
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) b FROM bookings"))['b'] ?? 0;

// Handle User Deletion
if(isset($_GET['delete_id'])){
    $del_id = (int)$_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$del_id");
    // Optionally delete their bookings too, or cascade on DB level
    mysqli_query($conn, "DELETE FROM bookings WHERE user_id=$del_id");
    echo "<script>alert('User deleted successfully'); window.location='users.php';</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management | MyLab</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Layout is now unified via sidebar.php */
        body { margin: 0; display: flex; background: #f8fafc; font-family: 'Inter', sans-serif; }

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
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 35px;
            max-width: 800px;
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

        .icon-users { background: #e0e7ff; color: #4338ca; }
        .icon-books { background: #dcfce7; color: #15803d; }

        .stat-info h3 { margin: 0; font-size: 13px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-info h2 { margin: 5px 0 0 0; font-size: 30px; font-weight: 800; }

        /* --- TOOLBAR --- */
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
            max-width: 500px;
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

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary);
            margin-right: 15px;
        }

        .user-cell { display: flex; align-items: center; }
        .user-details strong { display: block; font-weight: 600; color: #1e293b; }
        .user-details span { font-size: 12px; color: var(--text-muted); }

        .action-icon {
            padding: 8px;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            margin-right: 5px;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-del { background: #fee2e2; color: #dc2626; }
        .action-icon:hover { opacity: 0.8; transform: scale(1.1); }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h2>Users Management</h2>
            <div style="color: var(--text-muted); font-weight: 500;">
                <i class="far fa-calendar-alt"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </header>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-users"><i class="fas fa-user-friends"></i></div>
                <div class="stat-info">
                    <h3>Total Registered Users</h3>
                    <h2><?php echo $total_users; ?></h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-books"><i class="fas fa-check-double"></i></div>
                <div class="stat-info">
                    <h3>Global Bookings Generated</h3>
                    <h2><?php echo $total_bookings; ?></h2>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <form class="search-box" method="GET">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </form>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User Details</th>
                        <th>Registration Date</th>
                        <th>Total Bookings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if(isset($_GET['search']) && !empty($_GET['search'])){
                        $s = mysqli_real_escape_string($conn, $_GET['search']);
                        $search_query = " WHERE u.name LIKE '%$s%' OR u.email LIKE '%$s%'";
                    }
                    
                    // Join to get count of bookings per user
                    $query = "SELECT u.id, u.name, u.email, u.created_at, COUNT(b.id) as booking_count 
                              FROM users u 
                              LEFT JOIN bookings b ON u.id = b.user_id 
                              $search_query
                              GROUP BY u.id 
                              ORDER BY u.id DESC";
                              
                    $q = mysqli_query($conn, $query);

                    if(mysqli_num_rows($q) > 0){
                        $i = 1;
                        while($row = mysqli_fetch_assoc($q)){
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="avatar"><?php echo strtoupper(substr($row['name'],0,1)); ?></div>
                                <div class="user-details">
                                    <strong><a href="user_history.php?id=<?php echo $row['id']; ?>" class="hover:text-blue-600"><?php echo htmlspecialchars($row['name']); ?></a></strong>
                                    <span><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                            </div>
                        </td>
                        <td><?php echo date('d M, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="user_history.php?id=<?php echo $row['id']; ?>" class="hover:scale-105 transition-transform inline-block">
                                <span style="font-weight: 700; color: #4338ca; background: #e0e7ff; padding: 4px 10px; border-radius: 20px;">
                                    <?php echo $row['booking_count']; ?> Bookings
                                </span>
                            </a>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a title="View History" class="action-icon" style="background: #e0f2fe; color: #0369a1;" href="user_history.php?id=<?php echo $row['id']; ?>">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a title="Delete User" class="action-icon btn-del" href="users.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('WARNING: Are you sure you want to delete this user? This will also delete all their bookings!')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } } else { ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            No users found.
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
