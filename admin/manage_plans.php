<?php
include '../db/config.php';
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }

// Handle Updates
if(isset($_POST['update_plan'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $features = mysqli_real_escape_string($conn, $_POST['features']);
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    
    $q = "UPDATE plans SET name='$name', price='$price', features='$features', is_popular='$is_popular' WHERE id='$id'";
    mysqli_query($conn, $q);
    $msg = "Plan updated successfully!";
}

$plans = mysqli_query($conn, "SELECT * FROM plans");
if (!$plans) {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded-xl mb-6'>Error: Could not fetch plans. Please check if the 'plans' table exists in the database.</div>";
    $plans = []; // Set to empty array to avoid loop error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Plans - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-black text-slate-800">Manage Health Plans</h1>
        </div>

        <?php if(isset($msg)): ?>
            <div class="bg-emerald-100 text-emerald-700 p-4 rounded-xl mb-6 font-bold flex items-center gap-3">
                <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php 
            if ($plans && mysqli_num_rows($plans) > 0):
                while($plan = mysqli_fetch_assoc($plans)): 
            ?>
                <form method="POST" class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col">
                    <input type="hidden" name="id" value="<?php echo $plan['id']; ?>">
                    
                    <div class="mb-4">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Plan Name</label>
                        <input type="text" name="name" value="<?php echo $plan['name']; ?>" class="w-full bg-slate-50 border-none rounded-xl p-3 font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Price (INR)</label>
                        <input type="number" name="price" value="<?php echo $plan['price']; ?>" class="w-full bg-slate-50 border-none rounded-xl p-3 font-bold text-blue-600 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-6">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Features (Comma separated)</label>
                        <textarea name="features" rows="6" class="w-full bg-slate-50 border-none rounded-xl p-3 text-sm text-slate-600 focus:ring-2 focus:ring-blue-500"><?php echo $plan['features']; ?></textarea>
                        <p class="text-[10px] text-slate-400 mt-2">Add 'X' at end of feature to show as Unavailable (e.g. ThyroidX)</p>
                    </div>

                    <div class="mb-8 flex items-center gap-3">
                        <input type="checkbox" name="is_popular" id="pop-<?php echo $plan['id']; ?>" <?php echo $plan['is_popular'] ? 'checked' : ''; ?> class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-slate-200">
                        <label for="pop-<?php echo $plan['id']; ?>" class="font-bold text-slate-700">Mark as Popular</label>
                    </div>

                    <button type="submit" name="update_plan" class="mt-auto w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition">Update Plan</button>
                </form>
            <?php 
                endwhile; 
            endif;
            ?>
        </div>
        </div>
    </div>
</body>
</html>
