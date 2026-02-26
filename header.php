<!-- Tailwind aur FontAwesome ke links hamesha hone chahiye -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Universal Path Logic: Check karein ki hum root mein hain ya 'pages' folder mein
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$path_prefix = ($current_dir == 'pages') ? '../' : '';
$page_prefix = ($current_dir == 'pages') ? '' : 'pages/';
?>
<nav class="bg-white shadow-sm border-b sticky top-0 z-[100]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
        
        <!-- Logo Section -->
        <a href="<?php echo $path_prefix; ?>index.php" class="flex items-center gap-2">
            <div class="bg-blue-600 p-1.5 rounded-lg text-white">
                <i class="fas fa-microscope text-xl"></i>
            </div>
            <span class="text-2xl font-black text-gray-800 tracking-tighter">MyLab</span>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-8 text-sm font-bold text-gray-500">
            <a href="<?php echo $path_prefix; ?>index.php" class="hover:text-blue-600 transition">Home</a>
            <a href="#popular" class="hover:text-blue-600 transition">Popular Tests</a>
            <a href="#Contact" class="hover:text-blue-600 transition">Supports</a>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-4">
            <?php if(isset($_SESSION['user_id'])): 
                $fullName = $_SESSION['user_name'];
                $words = explode(" ", $fullName);
                $initials = strtoupper(substr($words[0], 0, 1));
                if(count($words) > 1) $initials .= strtoupper(substr($words[1], 0, 1));
            ?>
                <!-- LOGGED IN: Profile Dropdown -->
                <div class="relative" id="profileDropdown">
                    <button onclick="toggleMenu()" class="flex items-center gap-3 bg-gray-50 p-1.5 pr-4 rounded-full border border-gray-100 hover:bg-gray-100 transition shadow-sm">
                        <div class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                            <?php echo $initials; ?>
                        </div>
                        <div class="text-left hidden md:block">
                            <p class="text-[10px] text-gray-400 font-extrabold leading-none mb-1 tracking-wider uppercase">ID: <?php echo $_SESSION['user_id']; ?></p>
                            <p class="text-sm font-bold text-gray-800 leading-none"><?php echo $_SESSION['user_name']; ?></p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-[10px] ml-1"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="dropdownMenu" class="hidden absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden ring-1 ring-black ring-opacity-5">
                        
                        <!-- Dropdown Header -->
                        <div class="p-6 bg-gray-50/50 flex items-center gap-4 border-b border-gray-100">
                            <div class="w-14 h-14 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold">
                                <?php echo $initials; ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg leading-tight"><?php echo $_SESSION['user_name']; ?></h4>
                                <p class="text-[10px] text-gray-400 font-black uppercase mt-1 tracking-widest">Logged In Patient</p>
                            </div>
                        </div>

                        <!-- Dropdown Items -->
                        <div class="py-2">
                            <!-- Link Updated Here -->
                            <a href="<?php echo $path_prefix; ?>pages/user-bookings.php" class="flex items-center gap-4 px-6 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-calendar-check w-5 text-gray-400"></i> My Bookings
                            </a>
                            
                            <!-- My Address (Points to profile.php) -->
                            <a href="<?php echo $path_prefix; ?>pages/profile.php" class="flex items-center gap-4 px-6 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-map-marker-alt w-5 text-gray-400"></i> My Address
                            </a>

                            <!-- Manage Members -->
                            <a href="#" class="flex items-center gap-4 px-6 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-users w-5 text-gray-400"></i> Manage Members
                            </a>

                            <!-- My Reports -->
                            <a href="<?php echo $path_prefix; ?>pages/dashboard.php" class="flex items-center gap-4 px-6 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-file-medical w-5 text-gray-400"></i> My Reports
                            </a>
                        </div>

                        <!-- Signout Button -->
                        <div class="bg-gray-50/80 p-3">
                            <a href="<?php echo $path_prefix; ?>pages/logout.php" class="flex items-center justify-between px-4 py-2.5 bg-white text-red-500 font-bold text-sm rounded-xl border border-red-50 hover:bg-red-500 hover:text-white transition group">
                                Sign out <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- LOGGED OUT: Default Buttons -->
                <a href="<?php echo $page_prefix; ?>login.php" class="text-sm font-bold text-gray-600 hover:text-blue-600 transition">Login</a>
                <a href="<?php echo $page_prefix; ?>register.php" class="bg-blue-600 text-white px-6 py-2.5 rounded-full text-sm font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
// Menu toggle function
function toggleMenu() {
    const menu = document.getElementById('dropdownMenu');
    menu.classList.toggle('hidden');
}

// Close menu if clicked outside
window.onclick = function(e) {
    const dropdown = document.getElementById('profileDropdown');
    const menu = document.getElementById('dropdownMenu');
    if (dropdown && !dropdown.contains(e.target)) {
        if(menu) menu.classList.add('hidden');
    }
}
</script>