<?php
session_start();
include '../db/config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// Handle Address Deletion
if(isset($_GET['delete_id'])){
    $del_id = (int)$_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM user_addresses WHERE id=$del_id");
    echo "<script>alert('Address deleted successfully'); window.location='manage_addresses.php';</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Addresses Management | MyLab</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { margin: 0; display: flex; background: #f8fafc; font-family: 'Inter', sans-serif; }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        header h2 { margin: 0; font-size: 24px; font-weight: 700; color: #1e293b; }

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

        .search-box input:focus { border-color: #3b82f6; background: white; }
        .search-box i { position: absolute; left: 18px; top: 15px; color: #94a3b8; }

        /* --- TABLE DESIGN --- */
        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 18px 20px; text-align: left; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600; }
        td { padding: 18px 20px; border-top: 1px solid #f1f5f9; font-size: 14px; color: #475569; }
        tr:hover { background-color: #fcfdfe; }

        .user-link { font-weight: 700; color: #3b82f6; text-decoration: none; }
        .user-link:hover { text-decoration: underline; }

        .address-box {
            max-width: 300px;
            line-height: 1.5;
        }

        .tag {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 5px;
        }
        .tag-home { background: #e0f2fe; color: #0369a1; }
        .tag-office { background: #fef3c7; color: #92400e; }
        .tag-other { background: #f1f5f9; color: #475569; }

        .gps-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #059669;
            background: #ecfdf5;
            padding: 5px 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }
        .gps-link:hover { background: #d1fae5; }

        .btn-del { 
            background: #fee2e2; 
            color: #dc2626; 
            padding: 8px; 
            border-radius: 8px; 
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-del:hover { background: #fecaca; transform: scale(1.1); }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h2>Patient Addresses</h2>
            <div style="color: #94a3b8; font-weight: 500;">
                <i class="far fa-calendar-alt"></i> <?php echo date('D, d M Y'); ?>
            </div>
        </header>

        <!-- Toolbar -->
        <div class="toolbar">
            <form class="search-box" method="GET">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search by name, address or pincode..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </form>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User Name</th>
                        <th>Address Details</th>
                        <th>GPS Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if(isset($_GET['search']) && !empty($_GET['search'])){
                        $s = mysqli_real_escape_string($conn, $_GET['search']);
                        $search_query = " WHERE u.name LIKE '%$s%' OR a.address_line LIKE '%$s%' OR a.pincode LIKE '%$s%' OR a.title LIKE '%$s%'";
                    }
                    
                    $query = "SELECT a.*, u.name as user_name 
                              FROM user_addresses a 
                              JOIN users u ON a.user_id = u.id 
                              $search_query
                              ORDER BY a.id DESC";
                              
                    $q = mysqli_query($conn, $query);

                    if(mysqli_num_rows($q) > 0){
                        $i = 1;
                        while($row = mysqli_fetch_assoc($q)){
                            $title_class = 'tag-other';
                            if(strtolower($row['title']) == 'home') $title_class = 'tag-home';
                            if(strtolower($row['title']) == 'office') $title_class = 'tag-office';
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <a href="user_history.php?id=<?php echo $row['user_id']; ?>" class="user-link">
                                <?php echo htmlspecialchars($row['user_name']); ?>
                            </a>
                        </td>
                        <td>
                            <div class="address-box">
                                <span class="tag <?php echo $title_class; ?>"><?php echo htmlspecialchars($row['title']); ?></span>
                                <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['address_line']); ?></div>
                                <div style="font-size: 12px; color: #94a3b8;">
                                    <?php if(!empty($row['landmark'])) echo "Landmark: " . htmlspecialchars($row['landmark']) . " | "; ?>
                                    Pincode: <?php echo htmlspecialchars($row['pincode']); ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($row['latitude'] != 0): ?>
                                <a href="https://www.google.com/maps?q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>" target="_blank" class="gps-link">
                                    <i class="fas fa-location-arrow"></i> Google Maps
                                </a>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-size: 12px; italic">No Lat/Lng</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a title="Delete Address" class="btn-del" href="manage_addresses.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this address?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } } else { ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 30px;">
                            No addresses found.
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
