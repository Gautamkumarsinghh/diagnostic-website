<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyLab - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .active-sidebar { background-color: #ebf5ff; color: #2563eb; border-right: 4px solid #2563eb; }
        #map-container { height: 100%; width: 100%; z-index: 1; }
    </style>
</head>
<body class="bg-gray-50 font-sans">

    <!-- Header -->
    <header class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-40">
        <div class="flex items-center gap-2">
            <div class="bg-blue-600 text-white p-2 rounded-lg font-bold text-xl">My</div>
            <span class="text-2xl font-bold text-slate-800">Lab</span>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3 bg-gray-100 p-2 rounded-full pr-4 border">
                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">SS</div>
                <div class="text-sm font-bold">sumit singh</div>
            </div>
        </div>
    </header>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r pt-8 flex flex-col gap-2">
            <button onclick="switchTab('bookings')" id="btn-bookings" class="sidebar-btn flex items-center gap-3 px-6 py-4 text-gray-600 active-sidebar">
                <i class="fa-solid fa-calendar-check w-5"></i> My Bookings
            </button>
            <button onclick="switchTab('address')" id="btn-address" class="sidebar-btn flex items-center gap-3 px-6 py-4 text-gray-600">
                <i class="fa-solid fa-location-dot w-5"></i> My Address
            </button>
            <button onclick="switchTab('members')" id="btn-members" class="sidebar-btn flex items-center gap-3 px-6 py-4 text-gray-600">
                <i class="fa-solid fa-users w-5"></i> Manage Members
            </button>
            <button onclick="switchTab('reports')" id="btn-reports" class="sidebar-btn flex items-center gap-3 px-6 py-4 text-gray-600">
                <i class="fa-solid fa-file-medical w-5"></i> My Reports
            </button>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10">
            
            <!-- 1. Bookings View -->
            <div id="view-bookings">
                <h2 class="text-2xl font-bold mb-6">My Bookings</h2>
                <div class="bg-white p-6 rounded-2xl border shadow-sm flex justify-between items-center max-w-4xl">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center"><i class="fa-solid fa-file-invoice"></i></div>
                        <div><h3 class="font-bold text-gray-800">Full Body Checkup</h3><p class="text-sm text-gray-500">12 Feb 2026</p></div>
                    </div>
                    <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-xs font-bold">COMPLETED</span>
                </div>
            </div>

            <!-- 2. Address View -->
            <div id="view-address" class="hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">My Addresses</h2>
                    <button onclick="toggleModal(true)" class="flex items-center gap-2 border-2 border-blue-100 text-blue-600 px-6 py-2 rounded-full font-bold hover:bg-blue-50 transition">+ Add a new address</button>
                </div>
                <div id="empty-address-state" class="flex flex-col items-center justify-center mt-32">
                    <div class="bg-gray-100 p-6 rounded-full opacity-40 mb-4"><i class="fa-solid fa-file-lines text-5xl text-gray-400"></i></div>
                    <p class="text-gray-400 font-medium text-lg">No addresses found</p>
                </div>
                <div id="address-list" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
            </div>

            <!-- 3. Manage Members View (New) -->
            <div id="view-members" class="hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Manage Members</h2>
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-full font-bold hover:bg-blue-700 transition">+ Add Member</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Sample Member Card -->
                    <div class="bg-white p-6 rounded-2xl border shadow-sm flex items-center gap-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-2xl text-blue-600 font-bold">SS</div>
                        <div>
                            <h4 class="font-bold text-gray-800">Sumit Singh</h4>
                            <p class="text-sm text-gray-500">Self | Male | 28 Years</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. My Reports View (New) -->
            <div id="view-reports" class="hidden">
                <h2 class="text-2xl font-bold mb-6">My Reports</h2>
                <div class="space-y-4 max-w-4xl">
                    <!-- Sample Report Card -->
                    <div class="bg-white p-6 rounded-2xl border shadow-sm flex justify-between items-center">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-lg flex items-center justify-center text-xl">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Complete Blood Count (CBC)</h3>
                                <p class="text-sm text-gray-500">Booked on: 10 Feb 2026</p>
                            </div>
                        </div>
                        <button class="text-blue-600 font-bold flex items-center gap-2 hover:underline">
                            <i class="fa-solid fa-download"></i> Download Report
                        </button>
                    </div>
                    
                    <div class="bg-white p-6 rounded-2xl border shadow-sm flex justify-between items-center opacity-60">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-lg flex items-center justify-center text-xl">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Lipid Profile</h3>
                                <p class="text-sm text-gray-500">Booked on: 12 Feb 2026</p>
                            </div>
                        </div>
                        <span class="text-gray-400 font-bold italic">Processing...</span>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal for Address (Keep same) -->
    <div id="addressModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl transform transition-all scale-95">
            <div class="p-5 flex justify-between items-center border-b">
                <h3 class="font-bold text-xl">Add Address</h3>
                <button onclick="toggleModal(false)" class="text-gray-500 text-xl">✕</button>
            </div>
            <div class="p-5">
                <input id="search-input" type="text" placeholder="Search area..." class="w-full p-4 bg-gray-50 border rounded-xl">
            </div>
            <div class="relative h-64 mx-5 rounded-xl overflow-hidden border"><div id="map-container"></div></div>
            <div class="p-6">
                <div class="flex gap-4 mb-6">
                    <i class="fa-solid fa-location-dot text-gray-400"></i>
                    <div>
                        <h4 id="display-title" class="font-bold">Home Location</h4>
                        <p id="display-desc" class="text-xs text-gray-500">Vapi, Gujarat, India</p>
                    </div>
                </div>
                <button onclick="saveAddressToList()" class="w-full bg-blue-700 text-white py-4 rounded-xl font-bold">Confirm and proceed</button>
            </div>
        </div>
    </div>

    <script>
        let map;
        let savedAddresses = [];

        // Tab Switching Logic (Sare buttons handle karega)
        function switchTab(view) {
            const views = ['bookings', 'address', 'members', 'reports'];
            
            views.forEach(v => {
                document.getElementById('view-' + v).classList.add('hidden');
                document.getElementById('btn-' + v).classList.remove('active-sidebar');
            });

            document.getElementById('view-' + view).classList.remove('hidden');
            document.getElementById('btn-' + view).classList.add('active-sidebar');
        }

        function toggleModal(show) {
            document.getElementById('addressModal').classList.toggle('hidden', !show);
            if (show) setTimeout(initFreeMap, 100);
        }

        function initFreeMap() {
            if (!map) {
                map = L.map('map-container').setView([20.3778, 72.9038], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                L.marker([20.3778, 72.9038]).addTo(map);
            } else { map.invalidateSize(); }
        }

        function saveAddressToList() {
            const title = document.getElementById('display-title').innerText;
            const fullAddress = document.getElementById('display-desc').innerText;
            savedAddresses.push({ id: Date.now(), title, address: fullAddress });
            renderAddresses();
            toggleModal(false);
        }

        function renderAddresses() {
            const listDiv = document.getElementById('address-list');
            const emptyState = document.getElementById('empty-address-state');
            if (savedAddresses.length === 0) {
                emptyState.classList.remove('hidden');
                listDiv.innerHTML = '';
            } else {
                emptyState.classList.add('hidden');
                listDiv.innerHTML = savedAddresses.map(item => `
                    <div class="bg-white p-5 rounded-2xl border shadow-sm">
                        <h4 class="font-bold text-gray-800">${item.title}</h4>
                        <p class="text-sm text-gray-500 mt-1">${item.address}</p>
                        <button onclick="deleteAddress(${item.id})" class="text-red-500 text-xs mt-3 font-bold">DELETE</button>
                    </div>
                `).join('');
            }
        }

        function deleteAddress(id) {
            savedAddresses = savedAddresses.filter(a => a.id !== id);
            renderAddresses();
        }
    </script>
</body>
</html>