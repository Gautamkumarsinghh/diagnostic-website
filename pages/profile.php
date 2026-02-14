<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyLab - Modern Dashboard</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        
        /* Sidebar Styling */
        .sidebar-btn { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-btn i { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 12px; transition: inherit; }
        
        /* Individual Icon Colors */
        .icon-bookings { background: #e0f2fe; color: #0ea5e9; }
        .icon-address { background: #fef3c7; color: #d97706; }
        .icon-members { background: #f0fdf4; color: #22c55e; }
        .icon-reports { background: #fae8ff; color: #a855f7; }

        .active-sidebar { background: #ffffff; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04); border-left: 4px solid #2563eb; color: #1e293b !important; font-weight: 700; }
        
        #map-container { height: 100%; width: 100%; z-index: 1; border-radius: 1.5rem; }
        .card-hover { transition: transform 0.2s; }
        .card-hover:hover { transform: translateY(-3px); }
    </style>
</head>
<body class="text-slate-800">

    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 px-8 py-4 flex justify-between items-center sticky top-0 z-40">
        <div class="flex items-center gap-2">
            <div class="bg-blue-600 text-white p-2 rounded-xl font-bold text-xl shadow-lg shadow-blue-200">My</div>
            <span class="text-2xl font-extrabold text-slate-800 tracking-tight">Lab</span>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-2xl pr-4 border border-slate-100">
                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl flex items-center justify-center font-bold shadow-md">SS</div>
                <div class="text-sm">
                    <p class="font-bold leading-none">sumit singh</p>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-black">Premium Member</p>
                </div>
            </div>
        </div>
    </header>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-72 bg-white border-r border-slate-100 p-6 flex flex-col gap-2">
            <nav class="space-y-1">
                <button onclick="switchTab('bookings')" id="btn-bookings" class="sidebar-btn active-sidebar w-full flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-slate-50">
                    <i class="fa-solid fa-calendar-check icon-bookings"></i>
                    <span class="text-sm font-semibold">My Bookings</span>
                </button>
                <button onclick="switchTab('address')" id="btn-address" class="sidebar-btn w-full flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-slate-50">
                    <i class="fa-solid fa-location-dot icon-address"></i>
                    <span class="text-sm font-semibold">My Address</span>
                </button>
                <button onclick="switchTab('members')" id="btn-members" class="sidebar-btn w-full flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-slate-50">
                    <i class="fa-solid fa-users icon-members"></i>
                    <span class="text-sm font-semibold">Manage Members</span>
                </button>
                <button onclick="switchTab('reports')" id="btn-reports" class="sidebar-btn w-full flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-slate-50">
                    <i class="fa-solid fa-file-medical icon-reports"></i>
                    <span class="text-sm font-semibold">My Reports</span>
                </button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10">
            
            <!-- 1. Bookings View -->
            <div id="view-bookings" class="animate-in fade-in duration-500">
                <h2 class="text-3xl font-extrabold text-slate-800 mb-8">My Bookings</h2>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex justify-between items-center max-w-4xl card-hover">
                    <div class="flex gap-5">
                        <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                            <i class="fa-solid fa-microscope"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-xl text-slate-800">Full Body Checkup</h3>
                            <p class="text-sm text-slate-400 mt-1 font-medium"><i class="fa-regular fa-calendar-alt mr-2"></i> 12 Feb 2026</p>
                        </div>
                    </div>
                    <span class="bg-emerald-100 text-emerald-700 px-5 py-2 rounded-xl text-[10px] font-black tracking-widest uppercase border border-emerald-200">Completed</span>
                </div>
            </div>

            <!-- 2. Address View -->
            <div id="view-address" class="hidden animate-in fade-in duration-500">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-extrabold text-slate-800">My Addresses</h2>
                    <button onclick="toggleModal(true)" class="flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-[1.5rem] font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all active:scale-95">
                        <span class="text-xl">+</span> Add Address
                    </button>
                </div>
                
                <div id="empty-address-state" class="flex flex-col items-center justify-center mt-32">
                    <div class="bg-slate-100 p-8 rounded-full opacity-50 mb-6 border-4 border-white shadow-inner">
                        <i class="fa-solid fa-map-location-dot text-6xl text-slate-300"></i>
                    </div>
                    <p class="text-slate-400 font-bold text-xl">No addresses saved yet</p>
                    <p class="text-slate-300 text-sm mt-1">Add your home or office address for home collection</p>
                </div>
                
                <div id="address-list" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
            </div>

            <!-- 3. Manage Members View -->
            <div id="view-members" class="hidden animate-in fade-in duration-500">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-extrabold text-slate-800">Family Members</h2>
                    <button class="bg-emerald-600 text-white px-6 py-3 rounded-[1.5rem] font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition">+ Add Member</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5 card-hover">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl font-black shadow-inner">SS</div>
                        <div>
                            <h4 class="font-bold text-lg text-slate-800">Sumit Singh</h4>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">Self | 28 Years</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. My Reports View -->
            <div id="view-reports" class="hidden animate-in fade-in duration-500">
                <h2 class="text-3xl font-extrabold text-slate-800 mb-8">My Medical Reports</h2>
                <div class="space-y-4 max-w-4xl">
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex justify-between items-center card-hover">
                        <div class="flex gap-5">
                            <div class="w-14 h-14 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-slate-800">CBC (Complete Blood Count)</h3>
                                <p class="text-sm text-slate-400 font-medium">Report Generated: 10 Feb 2026</p>
                            </div>
                        </div>
                        <button class="bg-blue-50 text-blue-600 px-5 py-2 rounded-xl font-bold flex items-center gap-2 hover:bg-blue-600 hover:text-white transition-colors">
                            <i class="fa-solid fa-download"></i> Download
                        </button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Modern Address Modal -->
    <div id="addressModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.2)] transform transition-all scale-95 duration-300">
            <div class="p-6 flex justify-between items-center border-b border-slate-50">
                <h3 class="font-extrabold text-2xl text-slate-800">Add New Address</h3>
                <button onclick="toggleModal(false)" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-500 transition-colors">✕</button>
            </div>
            
            <div class="p-6">
                <div class="relative mb-6">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-blue-500"></i>
                    <input id="search-input" type="text" placeholder="Search area, society or landmark..." 
                           class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl pl-12 focus:ring-4 focus:ring-blue-500/10 focus:outline-none focus:bg-white transition-all">
                </div>
                
                <div class="relative h-64 rounded-[1.5rem] overflow-hidden border border-slate-100 mb-6 shadow-inner">
                    <div id="map-container"></div>
                </div>
                
                <div class="flex gap-5 mb-8 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shrink-0 shadow-sm text-amber-500">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h4 id="display-title" class="font-bold text-slate-800">Vapi Central</h4>
                        <p id="display-desc" class="text-xs text-slate-500 leading-relaxed mt-1">Gujarat, India - 396191</p>
                    </div>
                </div>
                
                <button onclick="saveAddressToList()" class="w-full bg-blue-600 hover:bg-indigo-700 text-white py-4 rounded-[1.5rem] font-bold shadow-xl shadow-blue-200 transition-all active:scale-95">
                    Save Address & Proceed
                </button>
            </div>
        </div>
    </div>

    <script>
        let map;
        let savedAddresses = [];

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
            const modal = document.getElementById('addressModal');
            const modalInner = modal.querySelector('div');
            
            if(show) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalInner.classList.remove('scale-95', 'opacity-0');
                    modalInner.classList.add('scale-100', 'opacity-100');
                    initFreeMap();
                }, 10);
            } else {
                modalInner.classList.add('scale-95', 'opacity-0');
                setTimeout(() => modal.classList.add('hidden'), 200);
            }
        }

        function initFreeMap() {
            if (!map) {
                map = L.map('map-container', { zoomControl: false }).setView([20.3778, 72.9038], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                L.marker([20.3778, 72.9038]).addTo(map);
                L.control.zoom({ position: 'bottomright' }).addTo(map);
            } else { 
                setTimeout(() => map.invalidateSize(), 100); 
            }
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
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm card-hover flex justify-between items-start">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-house"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-slate-800">${item.title}</h4>
                                <p class="text-sm text-slate-400 mt-1 leading-relaxed">${item.address}</p>
                            </div>
                        </div>
                        <button onclick="deleteAddress(${item.id})" class="text-slate-300 hover:text-rose-500 transition-colors">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
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