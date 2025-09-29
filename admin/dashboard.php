<?php
require_once '../inc/config.php';
requireAdminAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #00AEEF;">
                    <i class="fas fa-th-large"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php
                        $conn = getDBConnection();
                        $result = $conn->query("SELECT COUNT(*) as total FROM categories");
                        echo $result->fetch_assoc()['total'];
                        $conn->close();
                        ?>
                    </h3>
                    <p>Categories</p>
                </div>
                <a href="<?= ADMIN_URL ?>categories/" class="stat-link">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #28a745;">
                    <i class="fa-solid fa-water"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php
                        $conn = getDBConnection();
                        $result = $conn->query("SELECT COUNT(*) as total FROM fountains");
                        echo $result->fetch_assoc()['total'];
                        $conn->close();
                        ?>
                    </h3>
                    <p>Fountains</p>
                </div>
                <a href="<?= ADMIN_URL ?>fountains/" class="stat-link">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            
          <!--  <div class="stat-card">
                <div class="stat-icon" style="background: #ffc107;">
                    <i class="fas fa-blog"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php
                        $conn = getDBConnection();
                        $result = $conn->query("SELECT COUNT(*) as total FROM blog_posts");
                        echo $result->fetch_assoc()['total'];
                        $conn->close();
                        ?>
                    </h3>
                    <p>Blog Posts</p>
                </div>
                <a href="<?= ADMIN_URL ?>blog/posts/" class="stat-link">View All <i class="fas fa-arrow-right"></i></a>
            </div> -->
            
         <!--   <div class="stat-card">
                <div class="stat-icon" style="background: #dc3545;">
                    <i class="fas fa-folder"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php
                        $conn = getDBConnection();
                        $result = $conn->query("SELECT COUNT(*) as total FROM blog_categories");
                        echo $result->fetch_assoc()['total'];
                        $conn->close();
                        ?>
                    </h3>
                    <p>Blog Categories</p>
                </div>
                <a href="<?= ADMIN_URL ?>blog/categories/" class="stat-link">View All <i class="fas fa-arrow-right"></i></a>
            </div> -->
        </div>
        
        <div class="recent-activity">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <!-- Recent activities will be displayed here -->
                <div class="activity-item">
                    <i class="fas fa-plus-circle text-success"></i>
                    <span>New fountain added</span>
                    <small>2 hours ago</small>
                </div>
                <div class="activity-item">
                    <i class="fas fa-edit text-warning"></i>
                    <span>Category updated</span>
                    <small>5 hours ago</small>
                </div>
            </div>
        </div>
    </main>
    <script src="<?php echo ADMIN_ASSETS_URL; ?>js/admin.js"></script>
</body>
</html>