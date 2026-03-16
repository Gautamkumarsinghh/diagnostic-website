<?php 
// Standard Sidebar for Admin - Version 3.0 (Pixel Perfect)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Shared Admin Sidebar -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --side-w: 280px;
    --side-bg: #0f172a; 
    --side-accent: #3b82f6; 
    --side-text: #94a3b8;
    --side-bg-hover: rgba(255, 255, 255, 0.03);
}

.admin-sidebar {
    width: var(--side-w);
    background: var(--side-bg);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    z-index: 9999;
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: white;
    box-shadow: 10px 0 40px rgba(0,0,0,0.3);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-header {
    padding: 40px 24px;
    display: flex;
    align-items: center;
}

.brand-logo {
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
}

.brand-logo .icon-box {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
}

.brand-logo .icon-box i {
    color: white;
    font-size: 20px;
}

.brand-logo .brand-name {
    font-size: 20px;
    font-weight: 800;
    color: white;
    letter-spacing: -0.5px;
}

.nav-section {
    flex-grow: 1;
    padding: 0 16px;
    overflow-y: auto;
}

.nav-label {
    padding: 0 16px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 12px;
    margin-top: 24px;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    color: var(--side-text);
    text-decoration: none;
    border-radius: 16px;
    margin-bottom: 6px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
}

.nav-item i {
    font-size: 18px;
    width: 24px;
    text-align: center;
}

.nav-item:hover {
    background: var(--side-bg-hover);
    color: white;
    padding-left: 22px;
}

.nav-item.active {
    background: var(--side-accent);
    color: white;
    box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
}

.sidebar-footer {
    padding: 24px 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.logout-link {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    color: #fca5a5;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    border-radius: 16px;
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.1);
    transition: all 0.3s;
}

.logout-link:hover {
    background: #ef4444;
    color: white;
    box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
}

/* Page content adjustment */
.main-content, .ml-64, [class*="main-"] {
    margin-left: var(--side-w) !important;
    width: calc(100% - var(--side-w)) !important;
    min-height: 100vh;
    padding: 40px !important;
    box-sizing: border-box;
}

@media (max-width: 1024px) {
    .admin-sidebar { width: 90px; }
    .nav-item span, .brand-name, .nav-label, .logout-link span { display: none; }
    .sidebar-header { justify-content: center; padding: 30px 0; }
    .nav-item { justify-content: center; padding: 18px; }
    .nav-section { padding: 0 10px; }
    .main-content, .ml-64 { margin-left: 90px !important; width: calc(100% - 90px) !important; }
}
</style>

<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="brand-logo">
            <div class="icon-box"><i class="fas fa-microscope"></i></div>
            <span class="brand-name">MyLab Admin</span>
        </a>
    </div>
    
    <div class="nav-section">
        <div class="nav-label">Core Management</div>
        <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> <span>Bookings Dash</span>
        </a>
        <a href="manage_reports.php" class="nav-item <?php echo ($current_page == 'manage_reports.php') ? 'active' : ''; ?>">
            <i class="fas fa-file-medical"></i> <span>Report Center</span>
        </a>
        <a href="booking_calendar.php" class="nav-item <?php echo ($current_page == 'booking_calendar.php') ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i> <span>Booking Calendar</span>
        </a>


        <div class="nav-label">Services & Media</div>
        <a href="packages.php" class="nav-item <?php echo ($current_page == 'packages.php') ? 'active' : ''; ?>">
            <i class="fas fa-flask"></i> <span>Test Packages</span>
        </a>
        <a href="manage_plans.php" class="nav-item <?php echo ($current_page == 'manage_plans.php') ? 'active' : ''; ?>">
            <i class="fas fa-layer-group"></i> <span>Health Plans</span>
        </a>


        <div class="nav-label">Tools & Marketing</div>
        <a href="manage_coupons.php" class="nav-item <?php echo ($current_page == 'manage_coupons.php') ? 'active' : ''; ?>">
            <i class="fas fa-ticket-alt"></i> <span>Coupon Center</span>
        </a>
        <a href="manage_faqs.php" class="nav-item <?php echo ($current_page == 'manage_faqs.php') ? 'active' : ''; ?>">
            <i class="fas fa-question-circle"></i> <span>FAQs Manager</span>
        </a>

        <a href="bulk_messenger.php" class="nav-item <?php echo ($current_page == 'bulk_messenger.php') ? 'active' : ''; ?>">
            <i class="fas fa-paper-plane"></i> <span>Bulk Offers</span>
        </a>

        <div class="nav-label">Administration</div>
        <a href="manage_bank.php" class="nav-item <?php echo ($current_page == 'manage_bank.php') ? 'active' : ''; ?>">
            <i class="fas fa-university"></i> <span>Bank & Payments</span>
        </a>
        <a href="manage_staff.php" class="nav-item <?php echo ($current_page == 'manage_staff.php') ? 'active' : ''; ?>">
            <i class="fas fa-motorcycle"></i> <span>Field Operations</span>
        </a>
        <a href="manage_admins.php" class="nav-item <?php echo ($current_page == 'manage_admins.php') ? 'active' : ''; ?>">
            <i class="fas fa-user-lock"></i> <span>Team Accounts</span>
        </a>
        <a href="users.php" class="nav-item <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
            <i class="fas fa-user-friends"></i> <span>Patient Base</span>
        </a>
        <a href="manage_addresses.php" class="nav-item <?php echo ($current_page == 'manage_addresses.php') ? 'active' : ''; ?>">
            <i class="fas fa-map-marked-alt"></i> <span>User Addresses</span>
        </a>
    </div>

    <div class="sidebar-footer">
        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> <span>Logout Session</span>
        </a>
    </div>
</aside>
