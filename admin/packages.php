<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
$edit_mode = false;
$id = $u_name = $u_price = $u_desc = $u_image = "";

// --- 1. EDIT MODE: Fetch data when edit button is clicked ---
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM packages WHERE id='$id'");
    $row = mysqli_fetch_assoc($res);
    
    $u_name  = $row['name'];
    $u_price = $row['price'];
    $u_desc  = $row['description'];
    $u_image = $row['image'];
}

// --- 2. ADD LOGIC ---
if (isset($_POST['add'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $desc  = mysqli_real_escape_string($conn, $_POST['description']);
    $image = $_FILES['image']['name'];
    $tmp   = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp, "../images/".$image);
    mysqli_query($conn, "INSERT INTO packages(name, price, image, description) VALUES('$name', '$price', '$image', '$desc')");
    $msg = "Package added successfully!";
}

// --- 3. UPDATE LOGIC ---
if (isset($_POST['update'])) {
    $pid   = $_POST['id'];
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $desc  = mysqli_real_escape_string($conn, $_POST['description']);
    
    $new_image = $_FILES['image']['name'];
    $tmp       = $_FILES['image']['tmp_name'];

    if ($new_image != "") {
        move_uploaded_file($tmp, "../images/".$new_image);
        mysqli_query($conn, "UPDATE packages SET name='$name', price='$price', description='$desc', image='$new_image' WHERE id='$pid'");
    } else {
        mysqli_query($conn, "UPDATE packages SET name='$name', price='$price', description='$desc' WHERE id='$pid'");
    }
    header("Location: packages.php?status=updated");
}

if(isset($_GET['status'])) $msg = "Package updated successfully!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages | MyLab Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --sidebar-width: 250px; --primary: #0d6efd; --bg-body: #f4f7fe; --sidebar-bg: #1e293b; --white: #ffffff; --text-muted: #64748b; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-body); margin: 0; display: flex; }
        
        /* Sidebar */
        .sidebar { width: var(--sidebar-width); background: var(--sidebar-bg); height: 100vh; position: fixed; color: white; display: flex; flex-direction: column; padding: 25px 15px; box-sizing: border-box; }
        .sidebar-brand { font-size: 20px; font-weight: 700; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
        .nav-menu { flex-grow: 1; }
        .nav-link { display: flex; align-items: center; padding: 12px 15px; color: #94a3b8; text-decoration: none; border-radius: 10px; margin-bottom: 8px; font-size: 14px; transition: 0.3s; }
        .nav-link.active { background: var(--primary); color: white; }
        .logout-btn { background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 12px; border-radius: 10px; text-decoration: none; text-align: center; font-size: 14px; border: 1px solid rgba(239, 68, 68, 0.2); }

        /* Main Content */
        .main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); padding: 35px; box-sizing: border-box; }
        header { margin-bottom: 25px; }
        
        /* Form Card */
        .card { background: white; padding: 25px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.02); margin-bottom: 30px; border: 2px solid transparent; }
        .edit-mode-active { border-color: var(--primary); background: #f0f7ff; }

        form { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .full { grid-column: span 3; }
        label { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
        input, textarea { padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; font-family: inherit; }
        input:focus { border-color: var(--primary); }

        .btn-save { grid-column: span 3; background: var(--primary); color: white; padding: 14px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-update { background: #10b981; }
        .btn-cancel { background: #64748b; color: white; text-decoration: none; text-align: center; padding: 12px; border-radius: 10px; font-size: 13px; grid-column: span 3; margin-top: -10px; font-weight: 600; }

        /* Table */
        .table-container { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px 20px; text-align: left; font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 15px 20px; border-top: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }

        .pkg-preview { width: 70px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
        .price-tag { font-weight: 800; color: #1e293b; font-size: 15px; }
        
        .action-btns { display: flex; gap: 8px; }
        .btn-edit { background: #e0f2fe; color: #0284c7; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; transition: 0.2s; }
        .btn-del { background: #fee2e2; color: #ef4444; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; transition: 0.2s; }
        .btn-edit:hover { background: #0284c7; color: white; }
        .btn-del:hover { background: #ef4444; color: white; }

        .alert { background: #dcfce7; color: #15803d; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; border-left: 5px solid #15803d; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-flask" style="color:var(--primary)"></i> MyLab Admin</div>
        <div class="nav-menu">
            <a href="index.php" class="nav-link"><i class="fas fa-chart-line"></i> Bookings</a>
            <a href="packages.php" class="nav-link active"><i class="fas fa-box-open"></i> Packages</a>
        </div>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main -->
    <div class="main-content">
        <header>
            <h2 style="margin:0">Manage Packages</h2>
            <p style="color:var(--text-muted); margin:5px 0 0 0">Add or edit your test packages here</p>
        </header>

        <?php if($msg != ""): ?>
            <div class="alert"><i class="fas fa-check-circle"></i> <?php echo $msg; ?></div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="card <?php echo $edit_mode ? 'edit-mode-active' : ''; ?>">
            <div style="font-weight:700; margin-bottom:20px; color:#1e293b">
                <i class="fas <?php echo $edit_mode ? 'fa-edit' : 'fa-plus-circle'; ?>"></i> 
                <?php echo $edit_mode ? 'Edit Package Details' : 'Create New Package'; ?>
            </div>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="form-group">
                    <label>Package Name</label>
                    <input type="text" name="name" value="<?php echo $u_name; ?>" placeholder="e.g. Full Body Checkup" required>
                </div>

                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" name="price" value="<?php echo $u_price; ?>" placeholder="999" required>
                </div>

                <div class="form-group">
                    <label>Image <?php echo $edit_mode ? '(Optional)' : ''; ?></label>
                    <input type="file" name="image" accept="image/*" <?php echo $edit_mode ? '' : 'required'; ?>>
                </div>

                <div class="form-group full">
                    <label>Package Description</label>
                    <textarea name="description" rows="3" placeholder="Describe the tests included..."><?php echo $u_desc; ?></textarea>
                </div>

                <?php if($edit_mode): ?>
                    <button name="update" class="btn-save btn-update"><i class="fas fa-save"></i> Update Package</button>
                    <a href="packages.php" class="btn-cancel">Cancel Editing</a>
                <?php else: ?>
                    <button name="add" class="btn-save"><i class="fas fa-plus"></i> Save Package</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Packages Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th width="180">Action</th>
                        <th width="120">Price</th>
                        <th>Package Details</th>
                        <th width="100">Preview</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($conn, "SELECT * FROM packages ORDER BY id DESC");
                    $i = 1; // Starting ID from 1
                    while($row = mysqli_fetch_assoc($q)){
                    ?>
                    <tr>
                        <!-- Serial Number (starts from 1) -->
                        <td style="font-weight:700; color:var(--text-muted)"><?php echo $i++; ?></td>
                        
                        <!-- Action Buttons -->
                        <td>
                            <div class="action-btns">
                                <a href="packages.php?edit=<?php echo $row['id']; ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_package.php?id=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Confirm delete?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>

                        <!-- Price -->
                        <td class="price-tag">₹<?php echo $row['price']; ?></td>

                        <!-- Package Details -->
                        <td>
                            <div style="font-weight:700; color:#1e293b"><?php echo $row['name']; ?></div>
                            <div style="font-size:12px; color:var(--text-muted); margin-top:4px; line-height:1.4">
                                <?php echo substr($row['description'], 0, 80); ?>...
                            </div>
                        </td>

                        <!-- Preview Image -->
                        <td>
                            <img src="../images/<?php echo $row['image']; ?>" class="pkg-preview">
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>