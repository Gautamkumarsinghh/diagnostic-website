<!-- Tailwind aur FontAwesome ke links hamesha hone chahiye -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Universal Path Logic: Check karein ki hum root mein hain ya 'pages' folder mein
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$path_prefix = ($current_dir == 'pages' || $current_dir == 'admin') ? '../' : '';
$page_prefix = ($current_dir == 'pages' || $current_dir == 'admin') ? '' : 'pages/';
?>
<nav class="sticky top-0 z-[100] flex flex-col w-full shadow-sm">
    <!-- Top Promotional Banner -->
    <div id="topOfferBanner" class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 text-white w-full overflow-hidden transition-all duration-500 group relative">
        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-center py-2.5 relative">
            <div class="flex items-center gap-3 text-[10px] sm:text-[11px] font-black uppercase tracking-[0.1em] sm:tracking-[0.2em] text-center">
                <span class="animate-bounce inline-block text-yellow-300 text-sm"><i class="fas fa-gift text-shadow-sm"></i></span>
                <span class="truncate max-w-[200px] sm:max-w-none">Special Offer: Flat 20% OFF on Full Body Packages!</span>
                <span class="bg-yellow-400 text-slate-900 px-3 py-0.5 rounded-full ml-1 sm:ml-3 shadow-sm animate-pulse whitespace-nowrap hidden sm:inline-block">Use Code: HEALTH20</span>
            </div>
<<<<<<< HEAD
            <button onclick="closeOfferBanner()" class="absolute right-4 text-white hover:text-yellow-300 transition-colors bg-white/20 hover:bg-white/30 rounded-full w-5 h-5 flex items-center justify-center">
                <i class="fas fa-times text-[10px]"></i>
            </button>
=======
            <span class="text-2xl font-black text-gray-800 tracking-tighter">MyLab</span>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-8 text-sm font-bold text-gray-500">
            <a href="<?php echo $path_prefix; ?>index.php" class="hover:text-blue-600 transition">Home</a>
            <a href="#popular" class="hover:text-blue-600 transition">Popular Tests</a>
            <a href="#Contact" class="hover:text-blue-600 transition">Supports</a>
>>>>>>> b311c1453f909a077d5bc7d1e66a490ee131fd11
        </div>
    </div>

    <!-- Main Navbar Wrapper -->
    <div class="bg-white border-b border-gray-100 w-full" id="mainNavWrapper">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex justify-between items-center gap-3 xl:gap-6">
            
            <!-- Left Group: Logo & Location -->
            <div class="flex items-center gap-3 lg:gap-5 shrink-0">
                <!-- Logo Section -->
                <a href="<?php echo $path_prefix; ?>index.php" class="flex items-center gap-1.5 sm:gap-2 group">
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-1.5 sm:p-2 rounded-lg sm:rounded-xl text-white shadow-md shadow-blue-200">
                        <i class="fas fa-microscope text-base sm:text-xl"></i>
                    </div>
                    <span class="text-xl sm:text-3xl font-black text-blue-600 tracking-tighter lowercase">mylab<span class="hidden sm:inline-block text-xs text-gray-500 tracking-normal ml-1 align-top italic uppercase font-bold">diagnostics</span></span>
                </a>

                <!-- Divder -->
                <div class="hidden lg:block w-[1.5px] h-10 bg-gray-200"></div>

                <!-- Location Dropdown Pill -->
                <button onclick="toggleLocationModal()" class="hidden lg:flex items-center gap-2.5 bg-gray-50 hover:bg-gray-100 px-2 py-1.5 pr-3 rounded-full transition-colors border border-gray-100 shadow-sm cursor-pointer">
                    <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-700">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <span id="currentLocationText" class="text-[13px] font-bold text-gray-700 capitalize">New delhi</span>
                    <i class="fas fa-chevron-down text-gray-500 text-[10px] ml-0.5"></i>
                </button>
            </div>

            <!-- Middle Group: Search Bar -->
            <div class="hidden md:flex flex-1 max-w-2xl px-2">
                <div class="relative w-full flex items-center bg-gray-50 border border-gray-200 rounded-full focus-within:ring-[3px] focus-within:ring-blue-100 focus-within:border-blue-400 transition-all shadow-inner">
                    <input type="text" id="mainSearch" onkeyup="filterTests()" placeholder="Search for Tests/Packages" autocomplete="off" class="w-full bg-transparent border-none pl-6 pr-14 py-2.5 text-sm font-bold text-gray-800 focus:outline-none placeholder:text-gray-500">
                    <button class="absolute right-1.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 hover:scale-105 transition-all shadow-sm">
                        <i class="fas fa-search text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Right Group: Actions -->
            <div class="flex items-center gap-4 shrink-0">
                
                <!-- Home Collection Contact -->
                <div class="hidden xl:flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-gray-700 border border-orange-100/50 shadow-sm">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[11px] font-bold text-gray-500 leading-tight">Home Collection</span>
                        <a href="tel:9111591115" class="text-sm font-black text-slate-800 hover:text-blue-600 transition-colors leading-tight">86516-11893</a>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-2.5">
                    <!-- Mobile Search Icon -->
                    <button class="md:hidden w-8 h-8 flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl transition-colors border border-gray-200 shadow-sm">
                        <i class="fas fa-search text-sm"></i>
                    </button>

                    <!-- Cart Icon -->
                    <button onclick="toggleCart()" class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl transition-colors border border-gray-200 shadow-sm relative group cursor-pointer">
                        <i class="fas fa-shopping-cart text-sm sm:text-base group-hover:scale-110 transition-transform"></i>
                        <span id="cartCountBadge" class="absolute -top-1.5 -right-1.5 hidden bg-blue-600 text-white text-[9px] font-black w-4 h-4 rounded-full border border-white items-center justify-center shadow-sm">0</span>
                    </button>

                    <!-- Coins / Wallet -->
                    <a href="#" class="hidden sm:flex items-center gap-2 bg-blue-50/70 px-3.5 py-2 rounded-full border border-blue-100 hover:bg-blue-100 transition-colors shadow-sm">
                        <div class="relative w-5 h-5 flex items-center justify-center">
                            <i class="fas fa-coins text-yellow-500 text-lg drop-shadow-sm"></i>
                        </div>
                        <span class="font-black text-sm text-blue-700">0</span>
                    </a>

                    <?php if(isset($_SESSION['user_id'])): 
                        $fullName = $_SESSION['user_name'];
                        $words = explode(" ", $fullName);
                        $initials = strtoupper(substr($words[0], 0, 1));
                        if(count($words) > 1) $initials .= strtoupper(substr($words[1], 0, 1));
                    ?>
                        <!-- LOGGED IN: Profile Dropdown -->
                        <div class="relative" id="profileDropdown">
                            <button onclick="toggleMenu()" class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-slate-800 rounded-xl border border-gray-200 shadow-sm transition-all group overflow-hidden">
                                <div class="w-full h-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs sm:text-sm shadow-inner group-hover:scale-110 transition-transform">
                                    <?php echo $initials; ?>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="dropdownMenu" class="hidden absolute right-0 sm:-right-4 mt-3 w-64 sm:w-72 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-[200]">
                                <div class="p-6 bg-white flex flex-col items-center gap-3 border-b border-gray-100 text-center">
                                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold shadow-md">
                                        <?php echo $initials; ?>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-base sm:text-lg leading-tight uppercase"><?php echo $_SESSION['user_name']; ?></h4>
                                        <p class="text-[10px] sm:text-[11px] text-gray-400 font-black uppercase mt-1 tracking-widest leading-none">Logged In Patient</p>
                                    </div>
                                </div>
                                <div class="py-2">
                                    <a href="<?php echo $path_prefix; ?>pages/user-bookings.php?tab=bookings" class="flex items-center gap-4 px-6 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                        <i class="fas fa-calendar-check w-5 text-gray-400 text-center"></i> My Bookings
                                    </a>
                                    <a href="<?php echo $path_prefix; ?>pages/user-bookings.php?tab=address" class="flex items-center gap-4 px-6 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                        <i class="fas fa-map-marker-alt w-5 text-gray-400 text-center"></i> My Address
                                    </a>
                                    <a href="<?php echo $path_prefix; ?>pages/user-bookings.php?tab=members" class="flex items-center gap-4 px-6 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                        <i class="fas fa-users w-5 text-gray-400 text-center"></i> Manage Members
                                    </a>
                                    <a href="<?php echo $path_prefix; ?>pages/user-bookings.php?tab=reports" class="flex items-center gap-4 px-6 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition">
                                        <i class="fas fa-file-medical w-5 text-gray-400 text-center"></i> My Reports
                                    </a>
                                </div>
                                <div class="bg-gray-50/80 p-3 mt-1">
                                    <a href="<?php echo $path_prefix; ?>pages/logout.php" class="flex items-center justify-between px-4 py-3 bg-white text-red-500 font-bold text-sm rounded-xl border border-red-50 hover:bg-red-500 hover:text-white transition group shadow-sm">
                                        Sign out <i class="fas fa-sign-out-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- LOGGED OUT: Person Icon Button -->
                        <a href="<?php echo $page_prefix; ?>login.php" class="w-8 h-8 sm:w-10 sm:h-10 flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl transition-colors border border-gray-200 shadow-sm group">
                            <i class="fas fa-user text-sm sm:text-base group-hover:scale-110 transition-transform"></i>
                        </a>
                    <?php endif; ?>
                </div>

        </div>
    </div>

    <!-- FEATURE: Location Selection Modal -->
    <div id="locationModal" class="hidden fixed inset-0 z-[200] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]" onclick="toggleLocationModal()"></div>
        <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transform transition-all animate-[slideUp_0.3s_ease-out]">
            <!-- Modal Header -->
            <div class="px-5 py-4 sm:px-6 sm:py-5 bg-blue-600 text-white flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg sm:text-xl font-black tracking-tight leading-tight">Select Location</h3>
                    <p class="text-blue-100 text-[10px] sm:text-xs font-medium mt-0.5">Choose your state and city</p>
                </div>
                <button onclick="toggleLocationModal()" class="ml-auto w-8 h-8 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="p-6 space-y-6">
                <!-- Multi-Step Selection -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- State Selection -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-gray-600 uppercase tracking-wider ml-1">1. State</label>
                        <div class="relative">
                            <select id="stateSelect" onchange="updateCities()" class="w-full bg-gray-50 border border-gray-200 py-3 pl-3 pr-8 rounded-xl text-sm font-semibold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 appearance-none transition-all">
                                <option value="" disabled selected>Select State</option>
                                <option value="Gujarat">Gujarat</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Delhi">Delhi, NCR</option>
                                <option value="Bihar">Bihar</option>
                                <option value="UP">Uttar Pradesh</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- City Selection -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-gray-600 uppercase tracking-wider ml-1">2. City</label>
                        <div class="relative">
                            <select id="citySelect" class="w-full bg-gray-50 border border-gray-200 py-3 pl-3 pr-8 rounded-xl text-sm font-semibold text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 appearance-none transition-all" disabled>
                                <option value="" disabled selected>Select City</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <!-- Popular Cities Quick Pick -->
                <div class="space-y-3 pt-5 border-t border-gray-100">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Popular in Gujarat</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <button onclick="setQuickLocation('Ahmedabad', 'Gujarat')" class="px-4 py-2 rounded-lg bg-gray-100 text-xs font-bold text-gray-700 hover:bg-blue-600 hover:text-white transition-all shadow-sm">Ahmedabad</button>
                        <button onclick="setQuickLocation('Surat', 'Gujarat')" class="px-4 py-2 rounded-lg bg-gray-100 text-xs font-bold text-gray-700 hover:bg-blue-600 hover:text-white transition-all shadow-sm">Surat</button>
                        <button onclick="setQuickLocation('Vadodara', 'Gujarat')" class="px-4 py-2 rounded-lg bg-gray-100 text-xs font-bold text-gray-700 hover:bg-blue-600 hover:text-white transition-all shadow-sm">Vadodara</button>
                    </div>
                </div>

                <button onclick="confirmLocation()" class="w-full py-4 bg-blue-600 text-white font-bold text-sm tracking-wide rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all">
                    Confirm Selection
                </button>
            </div>
        </div>
    </div>

    <!-- FEATURE: Slide-Out Cart Sidebar -->
    <div id="cartSidebarOverlay" onclick="toggleCart()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-[300] hidden transition-opacity opacity-0"></div>
    <div id="cartSidebar" class="fixed top-0 right-0 h-full w-full sm:w-[400px] bg-white shadow-2xl z-[350] transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <!-- Cart Header -->
        <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight leading-tight">Your Cart</h3>
                <p id="cartItemsCountText" class="text-xs sm:text-[13px] font-semibold text-gray-500">No tests</p>
            </div>
            <button onclick="toggleCart()" class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-right text-sm"></i>
            </button>
        </div>

        <!-- Cart Body Area -->
        <div id="cartBody" class="flex-1 overflow-y-auto w-full custom-scrollbar relative">
            <!-- Empty Cart State -->
            <div id="emptyCartMessage" class="absolute inset-0 flex flex-col items-center justify-center p-6 sm:p-8 text-center bg-white">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mb-3 flex items-center justify-center rounded-full bg-blue-50/50 text-blue-100">
                    <i class="fas fa-shopping-basket text-3xl sm:text-4xl"></i>
                </div>
                <h4 class="text-base sm:text-[17px] font-bold text-gray-900 mb-1">Your cart is empty</h4>
                <p class="text-xs sm:text-[13px] text-gray-500 font-medium">Explore our lab tests and packages.</p>
            </div>

            <!-- Cart Items Wrapper -->
            <div id="cartItemsList" class="p-5 space-y-4 hidden pb-24">
                <!-- Dynamically populated via JS -->
            </div>
        </div>

        <!-- Cart Footer -->
        <div class="p-5 border-t border-gray-100 bg-white">
            <button id="cartActionButton" onclick="handleCartAction()" class="w-full bg-[#1855a9] text-white py-3.5 rounded-[10px] font-bold text-[14px] hover:bg-blue-800 active:scale-95 transition-all shadow-sm">
                Add tests or packages
            </button>
        </div>
    </div>
</nav>

<style>
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

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

// Search Logic
function filterTests() {
    const rawInput = document.getElementById('mainSearch').value.toLowerCase().trim();
    // Assuming cards have a class 'test-card' and inside it an 'h3' with product name
    const cards = document.querySelectorAll('.test-card');
    
    // Optional: Auto-scroll to popular tests section if user starts typing, 
    // only if on index page and section exists
    const popularSection = document.getElementById('popular-tests');
    if(rawInput.length > 0 && popularSection) {
        if (window.scrollY < (popularSection.offsetTop - 100)) {
            window.scrollTo({
                top: popularSection.offsetTop - 100,
                behavior: 'smooth'
            });
        }
    }

    if (cards.length === 0) return;
    
    cards.forEach(card => {
        const titleEl = card.querySelector('h3');
        if (titleEl) {
            const title = titleEl.innerText.toLowerCase();
            card.style.display = title.includes(rawInput) ? "" : "none";
        }
    });
}

// Offer Banner Logic
let offerClosed = false;
function closeOfferBanner() {
    const banner = document.getElementById('topOfferBanner');
    if(banner) {
        banner.style.height = '0';
        banner.style.opacity = '0';
        banner.style.padding = '0';
        setTimeout(() => banner.style.display = 'none', 500);
    }
    offerClosed = true;
}

window.addEventListener('scroll', () => {
    const banner = document.getElementById('topOfferBanner');
    
    if (window.scrollY > 40) {
        if(banner && !offerClosed) {
            banner.style.height = '0';
            banner.style.opacity = '0';
            banner.style.overflow = 'hidden';
            banner.style.padding = '0';
        }
    } else {
        if(banner && !offerClosed) {
            banner.style.height = '';
            banner.style.opacity = '1';
            banner.style.padding = '';
        }
    }
});

// --- LOCATION PICKER LOGIC ---
const locationData = {
    "Gujarat": ["Ahmedabad", "Surat", "Vadodara", "Rajkot", "Bhavnagar", "Jamnagar", "Gandhinagar", "Junagadh", "Anand"],
    "Bihar": ["Patna", "Gaya", "Muzaffarpur", "Bhagalpur", "Darbhanga"],
    "Delhi": ["New Delhi", "Noida", "Gurgaon", "Ghaziabad", "Faridabad"],
    "Maharashtra": ["Mumbai", "Pune", "Nagpur", "Thane", "Nashik", "Aurangabad"],
    "UP": ["Lucknow", "Kanpur", "Varanasi", "Prayagraj", "Agra", "Meerut"]
};

function toggleLocationModal() {
    const modal = document.getElementById('locationModal');
    modal.classList.toggle('hidden');
    if(!modal.classList.contains('hidden')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
}

function updateCities() {
    const state = document.getElementById('stateSelect').value;
    const citySelect = document.getElementById('citySelect');
    citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';
    
    if (state && locationData[state]) {
        locationData[state].forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
        citySelect.disabled = false;
    } else {
        citySelect.disabled = true;
    }
}

function setQuickLocation(city, state) {
    document.getElementById('stateSelect').value = state;
    updateCities();
    document.getElementById('citySelect').value = city;
}

function confirmLocation() {
    const state = document.getElementById('stateSelect').value;
    const city = document.getElementById('citySelect').value;
    
    if (state && city) {
        document.getElementById('currentLocationText').textContent = city;
        localStorage.setItem('userLocationState', state);
        localStorage.setItem('userLocationCity', city);
        toggleLocationModal();
        
        // Highlight pill on update
        const pill = document.querySelector('button[onclick="toggleLocationModal()"]');
        if(pill) {
            pill.classList.add('ring-2', 'ring-blue-400');
            setTimeout(() => pill.classList.remove('ring-2', 'ring-blue-400'), 1000);
        }
    } else {
        alert("Please select both State and City");
    }
}

// Initialize Location
(function() {
    const savedCity = localStorage.getItem('userLocationCity');
    if (savedCity) {
        const locElement = document.getElementById('currentLocationText');
        if (locElement) {
            locElement.textContent = savedCity;
        }
    }
})();

// --- CART SIDEBAR LOGIC ---
let cart = JSON.parse(localStorage.getItem('mylab_cart')) || [];

function toggleCart() {
    const sidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('cartSidebarOverlay');
    
    // Check if cart is currently closed
    if(sidebar.classList.contains('translate-x-full')) {
        // Open
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
        sidebar.classList.remove('translate-x-full');
        document.body.style.overflow = 'hidden';
        renderCart();
    } else {
        // Close
        overlay.classList.add('opacity-0');
        sidebar.classList.add('translate-x-full');
        document.body.style.overflow = '';
        setTimeout(() => overlay.classList.add('hidden'), 300);
    }
}

function addToCart(testName, price) {
    if(!cart.find(item => item.name === testName)) {
        cart.push({ name: testName, price: parseFloat(price) });
        localStorage.setItem('mylab_cart', JSON.stringify(cart));
        renderCart();
        
        // Option to auto open cart or just ping badge
        // toggleCart(); 
        
        // Visual feedback on cart icon
        const cartBadge = document.getElementById('cartCountBadge');
        if(cartBadge) {
            cartBadge.classList.add('animate-ping');
            setTimeout(() => cartBadge.classList.remove('animate-ping'), 500);
        }
    } else {
        alert(testName + " is already in your cart!");
    }
}

function removeFromCart(index) {
    cart.splice(index, 1);
    localStorage.setItem('mylab_cart', JSON.stringify(cart));
    renderCart();
}

function renderCart() {
    const defaultMsg = document.getElementById('emptyCartMessage');
    const itemsList = document.getElementById('cartItemsList');
    const countText = document.getElementById('cartItemsCountText');
    const actionBtn = document.getElementById('cartActionButton');
    const badge = document.getElementById('cartCountBadge');
    
    // Update Badge in Navbar
    if(cart.length > 0) {
        if(badge) {
            badge.classList.remove('hidden');
            badge.classList.add('flex');
            badge.textContent = cart.length;
        }
    } else {
        if(badge) {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
        }
    }

    if (cart.length === 0) {
        if(defaultMsg) defaultMsg.classList.remove('hidden');
        if(itemsList) itemsList.classList.add('hidden');
        if(countText) countText.textContent = 'No tests';
        if(actionBtn) {
            actionBtn.textContent = 'Add tests or packages';
            actionBtn.className = "w-full bg-[#1855a9] text-white py-3.5 rounded-[10px] font-bold text-[14px] hover:bg-blue-800 active:scale-95 transition-all shadow-sm";
        }
    } else {
        if(defaultMsg) defaultMsg.classList.add('hidden');
        if(itemsList) itemsList.classList.remove('hidden');
        
        let html = '';
        let total = 0;
        
        cart.forEach((item, index) => {
            total += item.price;
            html += `
                <div class="flex items-start justify-between p-4 bg-gray-50 border border-gray-100 rounded-xl mb-3">
                    <div class="pr-3">
                        <h5 class="text-sm font-bold text-gray-800 leading-tight mb-2">${item.name}</h5>
                        <p class="text-sm font-black text-blue-600">₹${item.price}</p>
                    </div>
                    <button onclick="removeFromCart(${index})" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;
        });
        
        // Add Total row
        html += `
            <div class="mt-4 p-4 border-t border-dashed border-gray-200 flex justify-between items-center bg-blue-50/30 rounded-xl">
                <span class="text-gray-600 font-bold text-sm">Total Amount:</span>
                <span class="text-xl font-black text-slate-900">₹${total}</span>
            </div>
        `;
        
        if(itemsList) itemsList.innerHTML = html;
        if(countText) countText.textContent = `${cart.length} test${cart.length > 1 ? 's' : ''}`;
        
        if(actionBtn) {
            actionBtn.innerHTML = `Checkout ${cart.length} Test${cart.length > 1 ? 's' : ''} <i class="fas fa-arrow-right ml-2 opacity-70"></i>`;
            actionBtn.className = "w-full bg-emerald-600 text-white py-3.5 rounded-[10px] font-bold text-[14px] hover:bg-emerald-700 active:scale-95 transition-all shadow-md flex justify-center items-center gap-2";
        }
    }
}

function handleCartAction() {
    if(cart.length === 0) {
        toggleCart(); // Close cart
        const pop = document.getElementById('popular-tests');
        if(pop) { window.scrollTo({top: pop.offsetTop - 100, behavior: 'smooth'}); }
        else { window.location.href = '<?php echo $path_prefix; ?>index.php#popular-tests'; }
    } else {
        // Build multiple tests query
        const testNames = cart.map(i => encodeURIComponent(i.name)).join(',');
        window.location.href = '<?php echo $path_prefix; ?>pages/booking.php?test=' + testNames;
    }
}

// Initial render
document.addEventListener('DOMContentLoaded', renderCart);

</script>