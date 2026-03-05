<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Auto-create table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$msg = "";

// Add FAQ
if (isset($_POST['add_faq'])) {
    $q = mysqli_real_escape_string($conn, $_POST['question']);
    $a = mysqli_real_escape_string($conn, $_POST['answer']);
    mysqli_query($conn, "INSERT INTO faqs (question, answer) VALUES ('$q', '$a')");
    $msg = "FAQ added successfully!";
}

// Delete FAQ
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM faqs WHERE id='$id'");
    header("Location: manage_faqs.php?status=deleted");
    exit();
}

if(isset($_GET['status'])) $msg = "FAQ " . $_GET['status'] . " successfully!";

$faqs = mysqli_query($conn, "SELECT * FROM faqs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage FAQs | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800">FAQ Manager</h1>
            <p class="text-slate-500 font-medium">Update the frequently asked questions on your website</p>
        </div>

        <?php if($msg != ""): ?>
            <div class="bg-emerald-100 text-emerald-700 p-4 rounded-2xl mb-8 font-bold flex items-center gap-3 border-l-4 border-emerald-500">
                <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 mb-6">Ask a Question</h3>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">The Question</label>
                            <input type="text" name="question" required class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">The Answer</label>
                            <textarea name="answer" rows="4" required class="w-full bg-slate-50 border-none rounded-xl p-4 font-medium text-slate-600 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                        </div>
                        <button type="submit" name="add_faq" class="w-full bg-slate-900 text-white py-4 rounded-xl font-black hover:bg-blue-600 transition-all shadow-xl shadow-slate-200">
                            Post FAQ <i class="fas fa-plus ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100">
                    <?php if(mysqli_num_rows($faqs) > 0): ?>
                        <div class="divide-y divide-slate-50">
                            <?php while($f = mysqli_fetch_assoc($faqs)): ?>
                                <div class="p-8 hover:bg-slate-50/50 transition-colors relative group">
                                    <div class="flex justify-between items-start mb-4">
                                        <h4 class="text-lg font-black text-slate-800 pr-10">Q: <?php echo $f['question']; ?></h4>
                                        <a href="?delete=<?php echo $f['id']; ?>" onclick="return confirm('Delete this FAQ?')" class="text-red-300 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                    <p class="text-slate-500 font-medium leading-relaxed">
                                        A: <?php echo $f['answer']; ?>
                                    </p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-20 text-center text-slate-400">
                            <i class="fas fa-question-circle text-4xl mb-4 opacity-20 block"></i>
                            No FAQs added yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
