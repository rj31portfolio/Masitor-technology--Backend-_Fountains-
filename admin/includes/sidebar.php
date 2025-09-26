<nav class="sidebar">
    <div class="sidebar-brand">
        <h3>MASITOR TECHNOLOGY</h3>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?= ADMIN_URL ?>dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Website
            </a>
        </li>
        
        <li class="menu-divider"><span>Content Management</span></li>
        
        <li>
            <a href="<?= ADMIN_URL ?>categories/" class="<?php echo strpos($_SERVER['PHP_SELF'], 'categories') !== false ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Categories
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>fountains/" class="<?php echo strpos($_SERVER['PHP_SELF'], 'fountains') !== false ? 'active' : ''; ?>">
                <i class="fa-solid fa-water"></i> Fountains
            </a>
        </li>
        
        <li class="menu-divider"><span>Blog Management</span></li>
        
        <li>
            <a href="<?= ADMIN_URL ?>blog/categories/" class="<?php echo strpos($_SERVER['PHP_SELF'], 'blog/categories') !== false ? 'active' : ''; ?>">
                <i class="fas fa-folder"></i> Blog Categories
            </a>
        </li>
        <li>
            <a href="<?= ADMIN_URL ?>blog/posts/" class="<?php echo strpos($_SERVER['PHP_SELF'], 'blog/posts') !== false ? 'active' : ''; ?>">
                <i class="fas fa-blog"></i> Blog Posts
            </a>
        </li>
        
        <li class="menu-divider"><span>Settings</span></li>
        
       <!-- <li>
            <a href="<?//= ADMIN_URL ?>settings.php" class="<?php //echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i> Settings
            </a>
        </li>  -->
    </ul>
</nav>
