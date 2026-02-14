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

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .glass-morphism { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .card-hover { transition: all 0.3s ease; cursor: pointer; }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
        .slide { display: none; animation: fade 1s ease-in-out; }
        @keyframes fade { from { opacity: 0; } to { opacity: 1; } }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 10px; }

        /* Modal Animation */
        #testModal { transition: opacity 0.3s ease; }
        .modal-box { transform: scale(0.9); transition: transform 0.3s ease; }
        #testModal.active .modal-box { transform: scale(1); }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

<?php include 'header.php'; ?>

<!-- HERO SECTION (Pehle wala hi hai) -->
<section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 py-16 px-6 lg:px-20 text-white overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400/20 rounded-full -ml-20 -mb-20 blur-3xl"></div>
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between relative z-10">
        <div class="lg:w-1/2 text-center lg:text-left">
            <span class="bg-blue-400/30 backdrop-blur-md text-white px-4 py-1.5 rounded-full text-sm font-semibold mb-6 inline-block border border-white/20">
                <i class="fas fa-check-circle mr-2"></i>NABL Accredited Laboratory
            </span>
            <h1 class="text-4xl lg:text-6xl font-extrabold leading-tight mb-6">Your Health, <br><span class="text-blue-200">Our Top Priority</span></h1>
            <p class="text-lg mb-8 text-blue-50 opacity-90 leading-relaxed max-w-xl mx-auto lg:mx-0">Experience hassle-free diagnostic testing with home sample collection and digital reports delivered within 24 hours.</p>
            <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                <a href="pages/login.php" class="bg-white text-blue-700 px-10 py-4 rounded-2xl font-bold shadow-xl hover:bg-blue-50 transition transform hover:scale-105">Book a Test</a>
                <a href="admin/login.php" class="bg-blue-800/40 backdrop-blur-md border border-white/30 text-white px-10 py-4 rounded-2xl font-bold hover:bg-white hover:text-blue-700 transition">Admin Portal</a>
            </div>
        </div>
        <div class="lg:w-1/2 mt-12 lg:mt-0 flex justify-center">
            <img src="images/yy.png" alt="Lab" class="w-full max-w-lg drop-shadow-[0_35px_35px_rgba(0,0,0,0.3)] floating-animation">
        </div>
    </div>
</section>

<!-- STATS/QUICK LINKS (Pehle wala) -->
<section class="max-w-7xl mx-auto -mt-12 px-6 relative z-20">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-xl text-center card-hover border-b-4 border-red-500">
            <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-droplet text-red-500 text-xl"></i></div>
            <p class="font-bold text-gray-800">Blood Tests</p>
        </div>
        <!-- ... baki ke 3 boxes ... -->
        <div class="bg-white p-6 rounded-2xl shadow-xl text-center card-hover border-b-4 border-blue-500">
            <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-heartbeat text-blue-500 text-xl"></i></div>
            <p class="font-bold text-gray-800">Heart Check</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-xl text-center card-hover border-b-4 border-orange-500">
            <div class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-bacteria text-orange-500 text-xl"></i></div>
            <p class="font-bold text-gray-800">Thyroid</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-xl text-center card-hover border-b-4 border-green-500">
            <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-file-medical text-green-500 text-xl"></i></div>
            <p class="font-bold text-gray-800">Lab Reports</p>
        </div>
    </div>
</section>

<!-- POPULAR TESTS -->
<section class="max-w-7xl mx-auto py-24 px-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-12">
        <div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">Popular Health Packages</h2>
            <div class="h-1.5 w-24 bg-blue-600 mt-3 rounded-full"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
    <?php
    include 'db/config.php';
    $q = mysqli_query($conn, "SELECT * FROM packages ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($q)) {
        // Pura description modal ke liye ready rakhein
        $full_desc = htmlspecialchars($row['description']);
        $short_desc = !empty($row['description']) ? substr($row['description'], 0, 90).'...' : 'Complete health screening covering essential parameters.';
    ?>
        <!-- Modal kholne ke liye click event add kiya -->
        <div class="bg-white rounded-[2rem] overflow-hidden shadow-md border border-gray-100 card-hover flex flex-col group" 
             onclick="openModal('<?php echo addslashes($row['name']); ?>', '<?php echo $row['price']; ?>', '<?php echo $row['image']; ?>', '<?php echo addslashes($full_desc); ?>')">
            <div class="relative overflow-hidden">
                <img src="images/<?php echo $row['image']; ?>" class="w-full h-56 object-cover group-hover:scale-110 transition duration-500">
                <div class="absolute top-4 left-4">
                    <span class="bg-white/90 backdrop-blur-md text-blue-600 text-xs font-bold px-4 py-1.5 rounded-full shadow-lg">⚡ FAST REPORT</span>
                </div>
            </div>
            
            <div class="p-8 flex flex-col flex-grow">
                <h3 class="text-2xl font-bold text-gray-800 mb-3"><?php echo $row['name']; ?></h3>
                <p class="text-gray-500 text-sm mb-6 leading-relaxed"><?php echo $short_desc; ?></p>
                
                <div class="mt-auto flex items-center justify-between border-t pt-6 border-gray-50">
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Package Price</p>
                        <span class="text-3xl font-black text-blue-600">₹<?php echo $row['price']; ?></span>
                    </div>
                    <div class="bg-gray-900 text-white w-12 h-12 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</section>

<!-- MODAL SECTION (Naya Addition) -->
<div id="testModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="modal-box bg-white w-full max-w-2xl rounded-[2.5rem] overflow-hidden shadow-2xl relative">
        <!-- Close Button -->
        <button onclick="closeModal()" class="absolute top-6 right-6 z-10 bg-white/80 backdrop-blur-md w-10 h-10 rounded-full flex items-center justify-center text-gray-800 hover:bg-red-500 hover:text-white transition-all">
            <i class="fas fa-times"></i>
        </button>

        <div class="flex flex-col md:flex-row">
            <!-- Modal Image -->
            <div class="md:w-1/2 h-64 md:h-auto overflow-hidden">
                <img id="modalImg" src="" class="w-full h-full object-cover">
            </div>
            
            <!-- Modal Content -->
            <div class="md:w-1/2 p-8 md:p-10 flex flex-col justify-center">
                <span class="text-blue-600 font-bold text-sm tracking-widest uppercase mb-2">Diagnostic Test</span>
                <h2 id="modalTitle" class="text-3xl font-extrabold text-gray-900 mb-4 leading-tight"></h2>
                
                <div class="mb-6">
                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Total Package Price</p>
                    <span id="modalPrice" class="text-4xl font-black text-blue-600"></span>
                </div>

                <div class="bg-gray-50 p-4 rounded-2xl mb-8">
                    <p class="text-gray-600 text-sm leading-relaxed" id="modalDesc"></p>
                </div>

                <a id="bookNowBtn" href="pages/booking.php" class="bg-blue-600 text-white text-center py-5 rounded-2xl font-bold text-lg shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all duration-300">
                    <i class="fas fa-shopping-cart mr-2"></i> Book Now
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER & SCRIPTS (Pehle wala) -->
<?php include 'footer.php'; ?>

<script>
    // Modal Functions
    function openModal(name, price, image, desc) {
        const modal = document.getElementById('testModal');
        document.getElementById('modalTitle').innerText = name;
        document.getElementById('modalPrice').innerText = '₹' + price;
        document.getElementById('modalImg').src = 'images/' + image;
        document.getElementById('modalDesc').innerText = desc;
        
        // Modal ko dikhane ke liye logic
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.add('opacity-100');
            modal.classList.add('active');
        }, 10);
        document.body.style.overflow = 'hidden'; // Background scroll stop
    }

    function closeModal() {
        const modal = document.getElementById('testModal');
        modal.classList.remove('opacity-100');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto'; // Scroll vapas chalu
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