<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Auto-create testimonials table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(100) NOT NULL,
    rating INT DEFAULT 5,
    comment TEXT NOT NULL,
    status ENUM('Visible', 'Hidden') DEFAULT 'Visible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$msg = "";

// Handle Add
if (isset($_POST['add_review'])) {
    $name = mysqli_real_escape_string($conn, $_POST['patient_name']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    mysqli_query($conn, "INSERT INTO testimonials (patient_name, rating, comment) VALUES ('$name', '$rating', '$comment')");
    $msg = "Testimonial added successfully!";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM testimonials WHERE id='$id'");
    header("Location: manage_testimonials.php?status=deleted");
    exit();
}

// Handle Toggle
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    mysqli_query($conn, "UPDATE testimonials SET status = IF(status='Visible', 'Hidden', 'Visible') WHERE id='$id'");
    header("Location: manage_testimonials.php?status=updated");
    exit();
}

if(isset($_GET['status'])) $msg = "Testimonial " . $_GET['status'] . " successfully!";

$reviews = mysqli_query($conn, "SELECT * FROM testimonials ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials Manager | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800">Testimonials Manager</h1>
            <p class="text-slate-500 font-medium">Manage patient reviews and ratings on the website</p>
        </div>

        <?php if($msg != ""): ?>
            <div class="bg-emerald-100 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border-l-4 border-emerald-500">
                <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 sticky top-10">
                    <h3 class="text-xl font-black text-slate-800 mb-6">Add New Review</h3>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Patient Name</label>
                            <input type="text" name="patient_name" placeholder="e.g. Rahul Sharma" required class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Rating (1-5)</label>
                            <select name="rating" class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Comment</label>
                            <textarea name="comment" rows="4" required placeholder="Write the testimonial here..." class="w-full bg-slate-50 border-none rounded-xl p-4 font-medium text-slate-600 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                        </div>
                        <button type="submit" name="add_review" class="w-full bg-slate-900 text-white py-4 rounded-xl font-black hover:bg-blue-600 transition-all">
                            Post Testimonial <i class="fas fa-star text-yellow-500 ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <?php while($r = mysqli_fetch_assoc($reviews)): ?>
                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 group relative">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="font-black text-slate-800 text-lg"><?php echo $r['patient_name']; ?></div>
                                <div class="flex gap-1 text-yellow-500 text-xs mt-1">
                                    <?php for($i=0; $i<$r['rating']; $i++): ?>
                                        <i class="fas fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="?toggle=<?php echo $r['id']; ?>" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-500 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                                    <i class="fas <?php echo ($r['status'] == 'Visible') ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                                </a>
                                <a href="?delete=<?php echo $r['id']; ?>" onclick="return confirm('Delete this review?')" class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-slate-500 font-medium italic">"<?php echo $r['comment']; ?>"</p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded bg-slate-100 text-slate-400">
                                Status: <?php echo $r['status']; ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

</body>
</html>
