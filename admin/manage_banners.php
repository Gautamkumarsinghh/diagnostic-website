<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Auto-create banners table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    link VARCHAR(255),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$msg = "";
$error = "";

// Handle Add Banner
if (isset($_POST['add_banner'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $subtitle = mysqli_real_escape_string($conn, $_POST['subtitle']);
    $link = mysqli_real_escape_string($conn, $_POST['link']);
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $allowed)) {
            $upload_dir = '../images/banners/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $new_name = 'banner_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
                mysqli_query($conn, "INSERT INTO banners (image, title, subtitle, link) VALUES ('$new_name', '$title', '$subtitle', '$link')");
                $msg = "Banner added successfully!";
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid image format.";
        }
    } else {
        $error = "Please select a banner image.";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $res = mysqli_query($conn, "SELECT image FROM banners WHERE id='$id'");
    if($row = mysqli_fetch_assoc($res)) {
        @unlink('../images/banners/' . $row['image']);
        mysqli_query($conn, "DELETE FROM banners WHERE id='$id'");
        header("Location: manage_banners.php?status=deleted");
        exit();
    }
}

if(isset($_GET['status'])) $msg = "Banner " . $_GET['status'] . " successfully!";

$banners = mysqli_query($conn, "SELECT * FROM banners ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner Manager | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800">Banner & Slider Manager</h1>
            <p class="text-slate-500 font-medium">Manage the promotional banners on your homepage</p>
        </div>

        <?php if($msg != ""): ?>
            <div class="bg-emerald-100 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border-l-4 border-emerald-500">
                <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Form -->
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 mb-6">Add New Banner</h3>
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Banner Image</label>
                            <input type="file" name="image" required class="w-full bg-slate-50 border-none rounded-xl p-3 font-semibold text-slate-600 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Main Title</label>
                            <input type="text" name="title" placeholder="e.g. 50% Off Full Body Checkup" class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Subtitle/Description</label>
                            <input type="text" name="subtitle" placeholder="e.g. Valid for this month only" class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Link (URL)</label>
                            <input type="text" name="link" placeholder="pages/booking.php?test=..." class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <button type="submit" name="add_banner" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black hover:bg-slate-900 transition-all shadow-xl shadow-blue-200">
                            Upload Banner <i class="fas fa-plus ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- List -->
            <div class="lg:col-span-2 space-y-6">
                <?php while($b = mysqli_fetch_assoc($banners)): ?>
                <div class="bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 flex gap-6 items-center group">
                    <div class="w-48 h-28 rounded-2xl overflow-hidden shadow-inner flex-shrink-0 bg-slate-100">
                        <img src="../images/banners/<?php echo $b['image']; ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-grow">
                        <div class="font-black text-slate-800 text-lg"><?php echo $b['title']; ?></div>
                        <p class="text-slate-400 text-sm font-medium"><?php echo $b['subtitle']; ?></p>
                        <?php if($b['link']): ?>
                            <div class="text-[10px] font-black uppercase text-blue-500 mt-2 flex items-center gap-1">
                                <i class="fas fa-link"></i> <?php echo $b['link']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="pr-4 space-y-2">
                        <a href="?delete=<?php echo $b['id']; ?>" onclick="return confirm('Delete this banner?')" class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>

                <?php if(mysqli_num_rows($banners) == 0): ?>
                    <div class="bg-white p-20 rounded-[2rem] text-center text-slate-400 border border-dashed border-slate-200">
                        <i class="fas fa-images text-5xl mb-4 opacity-20 block"></i>
                        No banners added yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
