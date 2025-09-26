<?php
require_once __DIR__.'/../../inc/config.php';
requireAdminAuth();
?>
<header class="admin-header">
    <div class="header-left">
        <h1><i class="fas fa-cog"></i> Admin Panel</h1>
    </div>
    <div class="header-right">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
        </div>
        <a href="<?= ADMIN_URL ?>logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
}
</script>