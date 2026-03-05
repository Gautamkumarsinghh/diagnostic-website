<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Schema Sync
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    stock_quantity INT DEFAULT 0,
    unit VARCHAR(50) DEFAULT 'pcs',
    low_stock_level INT DEFAULT 10,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$msg = "";

// Add Item
if(isset($_POST['add_item'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $qty = (int)$_POST['qty'];
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $low = (int)$_POST['low_level'];
    
    mysqli_query($conn, "INSERT INTO inventory (item_name, stock_quantity, unit, low_stock_level) VALUES ('$name', '$qty', '$unit', '$low')");
    $msg = "Item added to inventory successfully!";
}

// Update Stock
if(isset($_POST['update_stock'])) {
    $id = (int)$_POST['item_id'];
    $qty = (int)$_POST['qty'];
    mysqli_query($conn, "UPDATE inventory SET stock_quantity = '$qty' WHERE id='$id'");
    $msg = "Stock level updated!";
}

$inventory = mysqli_query($conn, "SELECT * FROM inventory ORDER BY item_name ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Center | MyLab Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Supply Center</h1>
                <p class="text-slate-500 font-medium">Manage lab kits, chemicals and medical disposables</p>
            </div>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-8 py-4 rounded-[1.5rem] font-bold shadow-xl shadow-blue-200 hover:bg-slate-900 transition-all flex items-center gap-3">
                <i class="fas fa-plus"></i> New Supply Item
            </button>
        </div>

        <?php if($msg): ?>
            <div class="bg-emerald-100 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border-l-4 border-emerald-500">
                <i class="fas fa-check-circle mr-2"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- Inventory List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while($item = mysqli_fetch_assoc($inventory)): 
                $is_low = ($item['stock_quantity'] <= $item['low_stock_level']);
            ?>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative group transition-all hover:shadow-xl hover:shadow-slate-200/50">
                <?php if($is_low): ?>
                <div class="absolute -top-3 -right-3 bg-red-500 text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest animate-pulse shadow-lg">Low Stock</div>
                <?php endif; ?>
                
                <h3 class="text-xl font-black text-slate-800 mb-2"><?php echo $item['item_name']; ?></h3>
                <div class="flex items-end gap-2 mb-6">
                    <span class="text-4xl font-black <?php echo $is_low ? 'text-red-500' : 'text-blue-600'; ?>"><?php echo $item['stock_quantity']; ?></span>
                    <span class="text-slate-400 font-bold mb-1 uppercase tracking-widest text-[10px]"><?php echo $item['unit']; ?> Remaining</span>
                </div>

                <form method="POST" class="flex gap-2">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="number" name="qty" required class="flex-grow bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500" placeholder="Adjust Qty">
                    <button type="submit" name="update_stock" class="bg-slate-900 text-white px-4 py-3 rounded-xl hover:bg-blue-600 transition-all">
                        <i class="fas fa-save"></i>
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-50 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    <span>Low Alert @ <?php echo $item['low_stock_level']; ?></span>
                    <span>Updated: <?php echo date('d M', strtotime($item['last_updated'])); ?></span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[10000] flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-lg rounded-[3rem] p-10 relative">
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="absolute top-8 right-8 text-slate-400 hover:text-slate-900"><i class="fas fa-times text-xl"></i></button>
            <h2 class="text-2xl font-black text-slate-900 mb-8">Add New Supply</h2>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Item Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 font-bold focus:ring-2 focus:ring-blue-500" placeholder="e.g. EDTA Blood Tubes">
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Initial Qty</label>
                        <input type="number" name="qty" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 font-bold focus:ring-2 focus:ring-blue-500" value="0">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Unit</label>
                        <select name="unit" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 font-bold focus:ring-2 focus:ring-blue-500">
                            <option>pcs</option>
                            <option>kits</option>
                            <option>boxes</option>
                            <option>ml</option>
                            <option>packets</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Low Stock Threshold</label>
                    <input type="number" name="low_level" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 font-bold focus:ring-2 focus:ring-blue-500" value="10">
                </div>
                <button type="submit" name="add_item" class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black shadow-xl shadow-blue-200 hover:bg-slate-900 transition-all">Add to Supplies</button>
            </form>
        </div>
    </div>

</body>
</html>
