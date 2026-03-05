<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Diagnostic Lab | Professional Healthcare</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .hero-gradient { background: radial-gradient(circle at top right, #3b82f6, #1e3a8a); }
        .card-hover { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-12px) scale(1.02); box-shadow: 0 30px 60px -12px rgba(0,0,0,0.15); }
        html { scroll-behavior: smooth; }
        .slide { display: none; animation: fade 1s ease-in-out; }
        @keyframes fade { from { opacity: 0; } to { opacity: 1; } }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 10px; }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #3b82f6; }

        /* Modal Animation */
        #testModal { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .modal-box { transform: translateY(30px) scale(0.95); transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
        #testModal.active .modal-box { transform: translateY(0) scale(1); }
        
        .glass-effect { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

<?php include 'header.php'; ?>

<!-- HERO SECTION -->
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 py-16 px-6 lg:px-20 text-white overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400/20 rounded-full -ml-20 -mb-20 blur-3xl"></div>
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between relative z-10 gap-10 lg:gap-0">
        <div class="lg:w-1/2 text-center lg:text-left">
            <span class="bg-blue-400/30 backdrop-blur-md text-white px-4 py-1.5 rounded-full text-xs sm:text-sm font-semibold mb-6 inline-block border border-white/20">
                <i class="fas fa-check-circle mr-2"></i>NABL Accredited Laboratory
            </span>
            <h1 class="text-[2.2rem] sm:text-4xl lg:text-6xl font-extrabold leading-tight lg:leading-tight mb-4 sm:mb-6">Your Health, <br><span class="text-blue-200">Our Top Priority</span></h1>
            <p class="text-xs sm:text-sm lg:text-lg mb-8 text-blue-50 opacity-90 leading-relaxed max-w-xl mx-auto lg:mx-0">Experience hassle-free diagnostic testing with home sample collection and digital reports delivered within 24 hours.</p>
            <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                <a href="pages/login.php" class="bg-white text-blue-700 px-8 lg:px-10 py-3.5 lg:py-4 rounded-xl lg:rounded-2xl font-bold shadow-xl hover:bg-blue-50 transition transform hover:scale-105">Book a Test</a>
                <a href="admin/login.php" class="bg-blue-800/40 backdrop-blur-md border border-white/30 text-white px-8 lg:px-10 py-3.5 lg:py-4 rounded-xl lg:rounded-2xl font-bold hover:bg-white hover:text-blue-700 transition">Admin Portal</a>
            </div>
        </div>
        <div class="lg:w-1/2 flex justify-center">
            <img src="images/yy.png" alt="Lab" class="w-full max-w-[280px] sm:max-w-md lg:max-w-lg drop-shadow-[0_35px_35px_rgba(0,0,0,0.3)] floating-animation">
        </div>
    </div>
</section>

<!-- STATS/QUICK LINKS -->
<section class="max-w-7xl mx-auto -mt-12 px-6 relative z-20">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-xl text-center card-hover border-b-4 border-red-500">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-droplet text-red-500 text-lg sm:text-xl"></i></div>
            <p class="font-bold text-xs sm:text-base text-gray-800">Blood Tests</p>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-xl text-center card-hover border-b-4 border-blue-500">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-heartbeat text-blue-500 text-lg sm:text-xl"></i></div>
            <p class="font-bold text-xs sm:text-base text-gray-800">Heart Check</p>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-xl text-center card-hover border-b-4 border-orange-500">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-bacteria text-orange-500 text-lg sm:text-xl"></i></div>
            <p class="font-bold text-xs sm:text-base text-gray-800">Thyroid</p>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-xl text-center card-hover border-b-4 border-green-500">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-file-medical text-green-500 text-lg sm:text-xl"></i></div>
            <p class="font-bold text-xs sm:text-base text-gray-800">Lab Reports</p>
        </div>
    </div>
</section>

<!-- WHY CHOOSE US (TRUST COUNTERS) -->
<section class="bg-white py-20 px-6 border-b border-gray-100">
    <div class="max-w-7xl mx-auto grid grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="text-center group p-6 rounded-3xl hover:bg-blue-50 transition-all">
            <div class="text-4xl lg:text-5xl font-black text-blue-600 mb-2">10k+</div>
            <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Happy Patients</p>
        </div>
        <div class="text-center group p-6 rounded-3xl hover:bg-blue-50 transition-all">
            <div class="text-4xl lg:text-5xl font-black text-blue-600 mb-2">500+</div>
            <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Tests Available</p>
        </div>
        <div class="text-center group p-6 rounded-3xl hover:bg-blue-50 transition-all">
            <div class="text-4xl lg:text-5xl font-black text-blue-600 mb-2">60m</div>
            <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Home Collection</p>
        </div>
        <div class="text-center group p-6 rounded-3xl hover:bg-blue-50 transition-all">
            <div class="text-4xl lg:text-5xl font-black text-blue-600 mb-2">99.9%</div>
            <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Accuracy Lab</p>
        </div>
    </div>
</section>

<!-- POPULAR TESTS -->
<<<<<<< HEAD
<section id="popular-tests" class="max-w-7xl mx-auto py-24 px-6">
=======


<section id="popular" class="max-w-7xl mx-auto py-24 px-6 scroll-mt-24">
>>>>>>> b311c1453f909a077d5bc7d1e66a490ee131fd11
    <div class="flex flex-col md:flex-row justify-between items-center mb-12">
        <div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">Popular Health Packages</h2>
            <div class="h-1.5 w-24 bg-blue-600 mt-3 rounded-full"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
    <?php
    include_once 'db/config.php';
    $q = mysqli_query($conn, "SELECT * FROM packages ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($q)) {
        $short_desc = !empty($row['description']) ? mb_substr(strip_tags($row['description']), 0, 90).'...' : 'Complete health screening covering essential parameters.';
        // Base64 encode description for safe JS passing
        $safe_desc = base64_encode($row['description']);
    ?>
        <div class="bg-white rounded-[2rem] overflow-hidden shadow-md border border-gray-100 card-hover flex flex-col group test-card transition-all duration-300" 
             onclick='openModal("<?php echo addslashes($row["name"]); ?>", "<?php echo $row["price"]; ?>", "<?php echo $row["image"]; ?>", "<?php echo $safe_desc; ?>", true)'>
            <div class="relative overflow-hidden cursor-pointer">
                <img src="images/<?php echo $row['image']; ?>" class="w-full h-48 sm:h-56 object-cover group-hover:scale-110 transition duration-500">
                <div class="absolute top-4 left-4">
                    <span class="bg-white/95 backdrop-blur-md text-blue-600 text-[10px] font-black px-4 py-1.5 rounded-full shadow-lg uppercase tracking-widest">⚡ FAST REPORT</span>
                </div>
            </div>
            
            <div class="p-6 sm:p-8 flex flex-col flex-grow">
                <div class="flex-grow">
                    <h3 class="text-xl sm:text-2xl font-black text-gray-800 mb-3 group-hover:text-blue-600 transition-colors"><?php echo $row['name']; ?></h3>
                    <p class="text-gray-500 text-xs sm:text-sm mb-6 leading-relaxed line-clamp-3"><?php echo $short_desc; ?></p>
                </div>
                
                <div class="mt-6 sm:mt-8 flex items-center justify-between border-t pt-5 sm:pt-6 border-gray-50">
                    <div>
                        <p class="text-[9px] sm:text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Package Price</p>
                        <span class="text-2xl sm:text-3xl font-black text-blue-600">₹<?php echo $row['price']; ?></span>
                    </div>
                    <div class="bg-gray-100 text-gray-900 w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all transform group-hover:rotate-12">
                        <i class="fas fa-arrow-right text-xs sm:text-sm"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</section>

<!-- HEALTH SCANS AND IMAGING TESTS SECTION -->
<section id="imaging-tests" class="max-w-7xl mx-auto py-16 sm:py-24 px-6 border-t border-gray-100">
    <div class="flex flex-col md:flex-row items-center justify-between mb-10 sm:mb-16 gap-6 text-center md:text-left">
        <div class="flex-grow">
            <p class="text-blue-600 font-black text-[10px] sm:text-xs uppercase tracking-[0.2em] sm:tracking-[0.3em] mb-2 sm:mb-3">Professional Diagnostics</p>
            <h2 class="text-2xl sm:text-3xl md:text-5xl font-black text-[#1e3a8a] leading-[1.1]">Health Scans and <span class="text-blue-500">Imaging Tests</span></h2>
        </div>
        <div class="flex gap-4">
            <button class="w-14 h-14 rounded-full border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:bg-white hover:border-blue-100 hover:text-blue-600 hover:shadow-[0_15px_30px_-10px_rgba(59,130,246,0.3)] transition-all transform active:scale-95">
                <i class="fas fa-arrow-left"></i>
            </button>
            <button class="w-14 h-14 rounded-full border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:bg-white hover:border-blue-100 hover:text-blue-600 hover:shadow-[0_15px_30px_-10px_rgba(59,130,246,0.3)] transition-all transform active:scale-95">
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10">
        <!-- Ultrasound -->
        <div class="group flex flex-col cursor-pointer transform transition-all duration-500 hover:-translate-y-4">
            <div class="mb-6 px-4">
                <h3 class="text-xl font-black text-slate-900 group-hover:text-blue-600 transition-colors">Ultrasound</h3>
                <p class="text-slate-400 text-sm font-bold mt-1 uppercase tracking-widest">Starting @ <span class="text-slate-900 font-black">₹500</span></p>
            </div>
            <div class="relative w-full aspect-[4/5] rounded-[3rem] overflow-hidden shadow-2xl shadow-blue-900/10">
                <img src="images/ultrasound.png" alt="Ultrasound" class="w-full h-full object-cover group-hover:scale-110 transition duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60"></div>
                <!-- Add to Cart Button -->
                <div class="absolute bottom-10 left-0 right-0 px-8">
                    <button onclick="addToCart('Ultrasound', 500); toggleCart();" class="w-full bg-white text-slate-900 text-center py-4 rounded-2xl font-black text-xs shadow-xl hover:bg-blue-600 hover:text-white transition-all transform hover:scale-105 active:scale-95 block">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>

        <!-- X-Ray -->
        <div class="group flex flex-col cursor-pointer transform transition-all duration-500 hover:-translate-y-4">
            <div class="mb-6 px-4">
                <h3 class="text-xl font-black text-slate-900 group-hover:text-blue-600 transition-colors">X-Ray</h3>
                <p class="text-slate-400 text-sm font-bold mt-1 uppercase tracking-widest">Starting @ <span class="text-slate-900 font-black">₹300</span></p>
            </div>
            <div class="relative w-full aspect-[4/5] rounded-[3rem] overflow-hidden shadow-2xl shadow-blue-900/10">
                <img src="images/xray.png" alt="X-Ray" class="w-full h-full object-cover group-hover:scale-110 transition duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60"></div>
                <!-- Add to Cart Button -->
                <div class="absolute bottom-10 left-0 right-0 px-8">
                    <button onclick="addToCart('X-Ray', 300); toggleCart();" class="w-full bg-white text-slate-900 text-center py-4 rounded-2xl font-black text-xs shadow-xl hover:bg-blue-600 hover:text-white transition-all transform hover:scale-105 active:scale-95 block">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>

        <!-- ECG -->
        <div class="group flex flex-col cursor-pointer transform transition-all duration-500 hover:-translate-y-4">
            <div class="mb-6 px-4">
                <h3 class="text-xl font-black text-slate-900 group-hover:text-blue-600 transition-colors">ECG</h3>
                <p class="text-slate-400 text-sm font-bold mt-1 uppercase tracking-widest">Starting @ <span class="text-slate-900 font-black">₹200</span></p>
            </div>
            <div class="relative w-full aspect-[4/5] rounded-[3rem] overflow-hidden shadow-2xl shadow-blue-900/10">
                <img src="images/ecg.jpg" alt="ECG" class="w-full h-full object-cover group-hover:scale-110 transition duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60"></div>
                <!-- Add to Cart Button -->
                <div class="absolute bottom-10 left-0 right-0 px-8">
                    <button onclick="addToCart('ECG', 200); toggleCart();" class="w-full bg-white text-slate-900 text-center py-4 rounded-2xl font-black text-xs shadow-xl hover:bg-blue-600 hover:text-white transition-all transform hover:scale-105 active:scale-95 block">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>

        <!-- Echocardiogram -->
        <div class="group flex flex-col cursor-pointer transform transition-all duration-500 hover:-translate-y-4">
            <div class="mb-6 px-4">
                <h3 class="text-xl font-black text-slate-900 group-hover:text-blue-600 transition-colors">Echocardiogram</h3>
                <p class="text-slate-400 text-sm font-bold mt-1 uppercase tracking-widest">Starting @ <span class="text-slate-900 font-black">₹149</span></p>
            </div>
            <div class="relative w-full aspect-[4/5] rounded-[3rem] overflow-hidden shadow-2xl shadow-blue-900/10">
                <img src="images/echocardiogram.jpg" alt="Echocardiogram" class="w-full h-full object-cover group-hover:scale-110 transition duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60"></div>
                <!-- Add to Cart Button -->
                <div class="absolute bottom-10 left-0 right-0 px-8">
                    <button onclick="addToCart('Echocardiogram', 149); toggleCart();" class="w-full bg-white text-slate-900 text-center py-4 rounded-2xl font-black text-xs shadow-xl hover:bg-blue-600 hover:text-white transition-all transform hover:scale-105 active:scale-95 block">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS SECTION -->
<section class="bg-slate-900 py-24 px-6 text-white overflow-hidden relative">
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center mb-16">
            <p class="text-blue-400 font-black text-xs uppercase tracking-[0.3em] mb-4">The Process</p>
            <h2 class="text-3xl md:text-5xl font-black leading-tight">How It Works</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            <div class="hidden md:block absolute top-[40px] left-[15%] w-[70%] h-0.5 bg-blue-500/20 z-0"></div>
            
            <div class="text-center relative z-10 group">
                <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-blue-500/20 group-hover:scale-110 transition-transform">
                    <i class="fas fa-search text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">1. Select a Test</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Choose from 500+ tests or popular health packages.</p>
            </div>
            
            <div class="text-center relative z-10 group">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center mx-auto mb-8 border border-white/10 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-alt text-2xl text-blue-400"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">2. Book Slot</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Pick a date and convenient time for home collection.</p>
            </div>
            
            <div class="text-center relative z-10 group">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center mx-auto mb-8 border border-white/10 group-hover:scale-110 transition-transform">
                    <i class="fas fa-house-user text-2xl text-blue-400"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">3. Sample Collection</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Our certified phlebotomist visits your home for sample.</p>
            </div>
            
            <div class="text-center relative z-10 group">
                <div class="w-20 h-20 bg-emerald-500 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-emerald-500/20 group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-medical-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">4. Digital Report</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Get accurate results on your WhatsApp within 24 hours.</p>
            </div>
        </div>
    </div>
</section>

<!-- NEW PREMIUM SPLIT-SCREEN MODAL DESIGN -->
<div id="testModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xl opacity-0 transition-all duration-500">
    <div class="modal-box bg-white w-full max-w-5xl rounded-[3rem] overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] relative flex flex-col lg:flex-row max-h-[90vh]">
        
        <!-- Left Side: Visuals & Quick Info (40%) -->
        <div class="lg:w-[40%] relative flex flex-col bg-slate-900 overflow-hidden">
            <img id="modalImg" src="" class="absolute inset-0 w-full h-full object-cover opacity-60">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
            
            <div class="relative z-10 p-10 flex flex-col h-full justify-between">
                <div>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="bg-blue-600/20 backdrop-blur-md border border-white/20 text-blue-200 text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">NABL ACCREDITED</span>
                        <span class="bg-emerald-500/20 backdrop-blur-md border border-white/20 text-emerald-200 text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">ISO CERTIFIED</span>
                    </div>
                    <h2 id="modalTitle" class="text-3xl lg:text-4xl font-black text-white leading-tight mb-4"></h2>
                    <div class="w-20 h-1.5 bg-blue-500 rounded-full"></div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 p-6 rounded-[2rem]">
                        <p class="text-[10px] text-blue-300 font-extrabold uppercase tracking-[0.2em] mb-3">Package Fee</p>
                        <div class="flex items-end gap-2">
                            <span id="modalPrice" class="text-4xl lg:text-5xl font-black text-white"></span>
                            <span class="text-white/50 text-xs mb-2">All Inclusive</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 p-4 rounded-2xl flex items-center gap-3">
                            <div class="text-blue-400"><i class="fas fa-clock"></i></div>
                            <div>
                                <p class="text-[8px] text-white/50 font-bold uppercase tracking-widest">Reports</p>
                                <p class="text-[10px] text-white font-bold">In 24 Hours</p>
                            </div>
                        </div>
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 p-4 rounded-2xl flex items-center gap-3">
                            <div class="text-emerald-400"><i class="fas fa-microscope"></i></div>
                            <div>
                                <p class="text-[8px] text-white/50 font-bold uppercase tracking-widest">Method</p>
                                <p class="text-[10px] text-white font-bold">Bio-Tech AI</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Extensive Details (60%) -->
        <div class="lg:w-[60%] flex flex-col bg-white relative">
            <!-- Close Button -->
            <button onclick="closeModal()" class="absolute top-6 right-6 z-30 bg-slate-50 hover:bg-red-500 w-10 h-10 rounded-xl flex items-center justify-center text-slate-400 hover:text-white transition-all group">
                <i class="fas fa-times group-hover:rotate-90 transition-transform"></i>
            </button>

            <!-- Main Content Area -->
            <div class="flex-grow overflow-y-auto p-8 lg:p-12 custom-scrollbar">
                <div class="mb-10">
                    <h4 class="text-slate-900 font-black text-xs uppercase tracking-[0.3em] mb-8 flex items-center gap-4">
                        <span class="w-10 h-1 bg-blue-600 rounded-full"></span>
                        DETAILED TEST COVERAGE
                    </h4>
                    
                    <div id="modalDesc" class="bg-slate-50/50 rounded-[2.5rem] p-8 border border-slate-100 min-h-[200px]">
                        <!-- Dynamic list will be injected here -->
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="flex items-start gap-4 p-5 rounded-3xl bg-blue-50/50 border border-blue-100/50">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600 shrink-0">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-800 mb-1 uppercase tracking-tight">Home Collection</p>
                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">Free home sample collection by certified phlebotomists.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-5 rounded-3xl bg-emerald-50/50 border border-emerald-100/50">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-emerald-600 shrink-0">
                            <i class="fas fa-file-medical-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-800 mb-1 uppercase tracking-tight">Digital Reports</p>
                            <p class="text-[11px] text-slate-500 leading-relaxed font-medium">High-resolution smart reports with doctor notes & trends.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Action Footer -->
            <div class="p-8 lg:px-12 lg:py-10 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-8 shrink-0">
                <div class="hidden sm:block">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-bolt text-yellow-500 text-xs"></i>
                        <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest">Limited Time Offer</p>
                    </div>
                    <p class="text-sm font-black text-slate-800">Extra 10% Off Applied</p>
                </div>
                
                <div class="flex flex-grow lg:flex-grow-0 items-center gap-4">
                    <button onclick="closeModal()" class="px-8 py-5 border border-slate-200 text-slate-500 rounded-2xl font-bold text-sm hover:bg-white transition-all hidden md:block">
                        Go Back
                    </button>
                    <button id="bookNowBtn" class="flex-grow md:min-w-[240px] bg-slate-950 text-white text-center py-5 lg:py-6 rounded-3xl font-black text-md lg:text-lg shadow-2xl hover:bg-blue-600 hover:-translate-y-1 active:translate-y-0 transition-all duration-300 flex items-center justify-center gap-4">
                        Add to Cart <i class="fas fa-shopping-cart text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- COMPARE PACKAGES SECTION (Unique Feature) -->
<section class="max-w-7xl mx-auto py-24 px-6 border-t border-gray-100">
    <div class="text-center mb-16">
        <h2 class="text-3xl md:text-5xl font-black text-gray-900 mb-4">Choose the Right Plan</h2>
        <p class="text-gray-500 text-lg">Compare our popular packages and find the best one for you.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-0 rounded-[3rem] overflow-hidden border border-gray-200 shadow-2xl">
        <?php
        $plans_res = mysqli_query($conn, "SELECT * FROM plans ORDER BY id ASC");
        while($p = mysqli_fetch_assoc($plans_res)):
            $features_list = explode(',', $p['features']);
            $is_pop = $p['is_popular'];
        ?>
        <div class="<?php echo $is_pop ? 'bg-blue-50 relative' : 'bg-white border-r border-gray-100'; ?> p-10">
            <?php if($is_pop): ?>
                <div class="absolute top-0 right-10 bg-blue-600 text-white px-4 py-1 rounded-b-xl text-xs font-bold uppercase tracking-widest">Most Popular</div>
            <?php endif; ?>
            
            <h3 class="text-xl font-bold text-gray-900 mb-4"><?php echo $p['name']; ?></h3>
            <div class="text-4xl font-black text-blue-600 mb-8">₹<?php echo number_format($p['price']); ?></div>
            
            <ul class="space-y-4 mb-10">
                <?php foreach($features_list as $f): 
                    $f = trim($f);
                    $is_absent = (substr($f, -1) == 'X');
                    $display_f = $is_absent ? substr($f, 0, -1) : $f;
                ?>
                    <li class="flex items-center gap-3 <?php echo $is_absent ? 'text-gray-400 line-through' : ($is_pop ? 'text-gray-800 font-medium' : 'text-gray-600'); ?>">
                        <i class="fas <?php echo $is_absent ? 'fa-times-circle' : 'fa-check-circle text-emerald-500'; ?>"></i> 
                        <?php echo $display_f; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- FAQ SECTION -->
<section class="max-w-7xl mx-auto py-24 px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div>
            <p class="text-blue-600 font-black text-xs uppercase tracking-[0.3em] mb-4">Everything You Need To Know</p>
            <h2 class="text-3xl md:text-5xl font-black text-slate-900 leading-tight mb-8">Frequently Asked <br> Questions</h2>
            <p class="text-slate-500 mb-10 text-lg">Still have questions? Our support team is here to help you 24/7 with expert advice.</p>
            <a href="https://wa.me/918651611893" class="inline-flex items-center gap-4 bg-emerald-500 text-white px-8 py-4 rounded-2xl font-black hover:bg-emerald-600 transition-all shadow-xl shadow-emerald-200">
                <i class="fab fa-whatsapp text-xl"></i> Chat With Expert
            </a>
        </div>
        
        <div class="space-y-4">
            <?php
            $q_faq = mysqli_query($conn, "SELECT * FROM faqs ORDER BY id DESC");
            if(mysqli_num_rows($q_faq) > 0) {
                while($f = mysqli_fetch_assoc($q_faq)) {
            ?>
                    <details class="group bg-white border border-slate-100 rounded-[2rem] p-6 [&_summary::-webkit-details-marker]:hidden cursor-pointer open:bg-blue-50/50 open:border-blue-100 transition-all shadow-sm">
                        <summary class="flex items-center justify-between text-slate-900 list-none">
                            <h5 class="text-lg font-bold"><?php echo $f['question']; ?></h5>
                            <span class="bg-slate-50 group-open:bg-blue-600 group-open:text-white group-open:rotate-180 transition-all w-10 h-10 rounded-xl flex items-center justify-center">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </span>
                        </summary>
                        <div class="overflow-hidden transition-all duration-300 max-h-0 group-open:max-h-40">
                            <p class="mt-6 text-slate-500 leading-relaxed font-medium"><?php echo $f['answer']; ?></p>
                        </div>
                    </details>
            <?php
                }
            } else {
            ?>
                <!-- Fallback Static FAQs -->
                <details class="group bg-white border border-slate-100 rounded-[2rem] p-6 [&_summary::-webkit-details-marker]:hidden cursor-pointer open:bg-blue-50/50 open:border-blue-100 transition-all shadow-sm">
                    <summary class="flex items-center justify-between text-slate-900 list-none">
                        <h5 class="text-lg font-bold">When will I get my reports?</h5>
                        <span class="bg-slate-50 group-open:bg-blue-600 group-open:text-white group-open:rotate-180 transition-all w-10 h-10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </span>
                    </summary>
                    <div class="overflow-hidden transition-all duration-300 max-h-0 group-open:max-h-40">
                        <p class="mt-6 text-slate-500 leading-relaxed font-medium">Majority of our reports are delivered within 12-24 hours.</p>
                    </div>
                </details>
                <details class="group bg-white border border-slate-100 rounded-[2rem] p-6 [&_summary::-webkit-details-marker]:hidden cursor-pointer open:bg-blue-50/50 open:border-blue-100 transition-all shadow-sm">
                    <summary class="flex items-center justify-between text-slate-900 list-none">
                        <h5 class="text-lg font-bold">Is home collection free?</h5>
                        <span class="bg-slate-50 group-open:bg-blue-600 group-open:text-white group-open:rotate-180 transition-all w-10 h-10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </span>
                    </summary>
                    <div class="overflow-hidden transition-all duration-300 max-h-0 group-open:max-h-40">
                        <p class="mt-6 text-slate-500 leading-relaxed font-medium">Yes, home collection is absolutely free for all our packages!</p>
                    </div>
                </details>
            <?php } ?>
        </div>
    </div>
</section>

<!-- TESTIMONIALS SECTION (Modern Refresh) -->
<section class="bg-slate-50 py-32 px-6 relative overflow-hidden">
    <!-- Subtle Background Pattern -->
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="max-w-7xl mx-auto relative z-10 text-center mb-20">
        <span class="text-blue-600 font-extrabold uppercase tracking-[0.3em] text-sm mb-4 block">Patient Experience</span>
        <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6">What Our Patients Say</h2>
        <div class="w-24 h-1.5 bg-blue-600 mx-auto rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-10 relative z-10">
        <!-- Review 1 -->
        <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-blue-900/5 border border-gray-100 relative group hover:-translate-y-2 transition-all duration-500">
            <div class="absolute -top-6 left-10 w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fas fa-quote-left"></i>
            </div>
            <div class="flex gap-1 text-yellow-400 mb-6 mt-4">
                <i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i>
            </div>
            <p class="text-gray-600 leading-relaxed mb-10 font-medium italic">"Very fast home collection. The person was professional and I got my reports on WhatsApp within 12 hours. Highly recommended!"</p>
            <div class="flex items-center gap-4 border-t border-gray-50 pt-8">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center font-black text-blue-600 text-lg">RK</div>
                <div>
                    <h4 class="font-black text-gray-900">Rahul Kumar</h4>
                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Verified Patient</p>
                </div>
            </div>
        </div>

        <!-- Review 2 -->
        <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-blue-900/5 border border-gray-100 relative group hover:-translate-y-2 transition-all duration-500">
            <div class="absolute -top-6 left-10 w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fas fa-quote-left"></i>
            </div>
            <div class="flex gap-1 text-yellow-400 mb-6 mt-4">
                <i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i>
            </div>
            <p class="text-gray-600 leading-relaxed mb-10 font-medium italic">"Their Full Body Checkup package is very affordable compared to other labs. The portal is also very easy to use for reports."</p>
            <div class="flex items-center gap-4 border-t border-gray-50 pt-8">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center font-black text-blue-600 text-lg">PS</div>
                <div>
                    <h4 class="font-black text-gray-900">Priya Singh</h4>
                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Verified Patient</p>
                </div>
            </div>
        </div>

        <!-- Review 3 -->
        <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-blue-900/5 border border-gray-100 relative group hover:-translate-y-2 transition-all duration-500">
            <div class="absolute -top-6 left-10 w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fas fa-quote-left"></i>
            </div>
            <div class="flex gap-1 text-yellow-400 mb-6 mt-4">
                <i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i><i class="fas fa-star text-sm"></i>
            </div>
            <p class="text-gray-600 leading-relaxed mb-10 font-medium italic">"Impressive service! The AI symptoms search really helped me find the right test for my constant fatigue."</p>
            <div class="flex items-center gap-4 border-t border-gray-50 pt-8">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center font-black text-blue-600 text-lg">AM</div>
                <div>
                    <h4 class="font-black text-gray-900">Anand Mishra</h4>
                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Verified Patient</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER & SCRIPTS (Pehle wala) -->
<?php include 'footer.php'; ?>

<script>
    // Modal Functions
    function openModal(name, price, image, desc, isBase64 = false) {
        const modal = document.getElementById('testModal');
        const modalBox = modal.querySelector('.modal-box');
        
        document.getElementById('modalTitle').innerText = name;
        document.getElementById('modalPrice').innerText = '₹' + price;
        document.getElementById('modalImg').src = 'images/' + image;
        
        // Decode if base64 encoded
        let finalDesc = desc;
        if(isBase64) {
            try {
                finalDesc = atob(desc);
            } catch(e) {
                finalDesc = desc;
            }
        }
        
        // Handling empty description
        if (!finalDesc || finalDesc.trim() === "" || finalDesc === "undefined") {
            finalDesc = "This package includes a comprehensive analysis. Please contact our support for a detailed parameters list.";
        }
        
        // Formatted Description (Checking for list items)
        let formattedDesc = "";
        if (finalDesc.includes("\n") || finalDesc.includes(",")) {
            const items = finalDesc.split(/[\n,]+/).filter(i => i.trim() !== "");
            formattedDesc = '<ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">';
            items.forEach(item => {
                formattedDesc += `<li class="flex items-center gap-3 text-slate-600"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> ${item.trim()}</li>`;
            });
            formattedDesc += '</ul>';
        } else {
            formattedDesc = `<p class="text-slate-600">${finalDesc}</p>`;
        }
        
        document.getElementById('modalDesc').innerHTML = formattedDesc;
        
        let cartBtn = document.getElementById('bookNowBtn');
        cartBtn.onclick = function() {
            closeModal();
            addToCart(name, price);
            toggleCart();
        };
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Animation
        setTimeout(() => {
            modal.classList.add('opacity-100');
            modal.classList.add('active'); // CSS hook for box animation
        }, 10);
        
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('testModal');
        const modalBox = modal.querySelector('.modal-box');
        
        modal.classList.remove('opacity-100');
        modalBox.classList.remove('scale-100');
        modalBox.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Modal ke bahar click karne par band ho jaye
    window.onclick = function(event) {
        const modal = document.getElementById('testModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    // Slider Logic (Pehle wala)
    let slideIdx = 0;
    const slides = document.querySelectorAll('.slide');
    function showSlides() {
        slides.forEach(s => s.style.display = 'none');
        slideIdx++;
        if (slideIdx > slides.length) slideIdx = 1;
        slides[slideIdx-1].style.display = 'block';
        setTimeout(showSlides, 4000);
    }
    showSlides();

    // Floating Image Animation (Pehle wala)
    const heroImg = document.querySelector('.floating-animation');
    let val = 0;
    setInterval(() => {
        val += 0.05;
        heroImg.style.transform = `translateY(${Math.sin(val) * 15}px)`;
    }, 30);
</script>

</body>
</html>