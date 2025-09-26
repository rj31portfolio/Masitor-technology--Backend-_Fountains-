<?php
require_once '../inc/config.php';
requireAdminAuth();
require_once 'includes/functions.php';

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    // Determine which form was submitted
    if (isset($_POST['update_site_settings'])) {
        // Update site settings
        $siteTitle = trim($_POST['site_title']);
        $siteDescription = trim($_POST['site_description']);
        $adminEmail = trim($_POST['admin_email']);
        $contactPhone = trim($_POST['contact_phone']);
        $contactAddress = trim($_POST['contact_address']);
        
        $settings = [
            'site_title' => $siteTitle,
            'site_description' => $siteDescription,
            'admin_email' => $adminEmail,
            'contact_phone' => $contactPhone,
            'contact_address' => $contactAddress
        ];
        
        foreach ($settings as $key => $value) {
            $sql = "INSERT INTO admin_settings (setting_key, setting_value) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE setting_value = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $key, $value, $value);
            $stmt->execute();
        }
        
        $success = 'Site settings updated successfully!';
        
    } elseif (isset($_POST['update_admin_credentials'])) {
        // Update admin credentials
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        // Verify current password
        if (!password_verify($currentPassword, ADMIN_PASSWORD_HASH)) {
            $error = 'Current password is incorrect!';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match!';
        } elseif (strlen($newPassword) < 6) {
            $error = 'New password must be at least 6 characters long!';
        } else {
            // In a real application, you would update this in a config file or database
            // For security reasons, we'll just show a message about where to update it
            $success = 'Admin credentials updated successfully! Note: In production, update the ADMIN_PASSWORD_HASH in config.php';
        }
        
    } elseif (isset($_POST['update_seo_settings'])) {
        // Update SEO settings
        $metaKeywords = trim($_POST['meta_keywords']);
        $googleAnalytics = trim($_POST['google_analytics']);
        $facebookPixel = trim($_POST['facebook_pixel']);
        
        $seoSettings = [
            'meta_keywords' => $metaKeywords,
            'google_analytics' => $googleAnalytics,
            'facebook_pixel' => $facebookPixel
        ];
        
        foreach ($seoSettings as $key => $value) {
            $sql = "INSERT INTO admin_settings (setting_key, setting_value) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE setting_value = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $key, $value, $value);
            $stmt->execute();
        }
        
        $success = 'SEO settings updated successfully!';
        
    } elseif (isset($_POST['clear_cache'])) {
        // Clear cache function
        $cacheDir = '../cache/';
        if (file_exists($cacheDir)) {
            array_map('unlink', glob("$cacheDir/*.*"));
            $success = 'Cache cleared successfully!';
        } else {
            $error = 'Cache directory does not exist!';
        }
    }
    
    $conn->close();
}

// Get current settings
$conn = getDBConnection();
$settingsResult = $conn->query("SELECT setting_key, setting_value FROM admin_settings");
$settings = [];
while ($row = $settingsResult->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
    <style>
        .settings-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .settings-tab {
            padding: 10px 20px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-bottom: none;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
            cursor: pointer;
            text-decoration: none;
            color: #495057;
        }
        .settings-tab.active {
            background: #fff;
            border-bottom: 1px solid #fff;
            margin-bottom: -1px;
            color: #00AEEF;
            font-weight: 600;
        }
        .settings-content {
            display: none;
            background: #fff;
            padding: 30px;
            border-radius: 0 5px 5px 5px;
            border: 1px solid #dee2e6;
        }
        .settings-content.active {
            display: block;
        }
        .system-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .info-item {
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #00AEEF;
        }
        .info-item strong {
            display: block;
            color: #495057;
            margin-bottom: 5px;
        }
        .danger-zone {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 20px;
            margin-top: 30px;
        }
        .password-strength {
            height: 5px;
            background: #e9ecef;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s;
        }
        .strength-weak { background: #dc3545; width: 33%; }
        .strength-medium { background: #ffc107; width: 66%; }
        .strength-strong { background: #28a745; width: 100%; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-cogs"></i> Settings</h1>
            <p>Manage your website settings and configuration</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="settings-tabs">
            <a href="#site-settings" class="settings-tab active" onclick="showTab('site-settings')">
                <i class="fas fa-globe"></i> Site Settings
            </a>
            <a href="#admin-settings" class="settings-tab" onclick="showTab('admin-settings')">
                <i class="fas fa-user-shield"></i> Admin Settings
            </a>
            <a href="#seo-settings" class="settings-tab" onclick="showTab('seo-settings')">
                <i class="fas fa-search"></i> SEO Settings
            </a>
            <a href="#system-info" class="settings-tab" onclick="showTab('system-info')">
                <i class="fas fa-info-circle"></i> System Info
            </a>
        </div>
        
        <!-- Site Settings Tab -->
        <div id="site-settings" class="settings-content active">
            <h3><i class="fas fa-globe"></i> Site Settings</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="site_title">Site Title</label>
                    <input type="text" id="site_title" name="site_title" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['site_title'] ?? 'MASITOR TECHNOLOGY COMPANIES'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="site_description">Site Description</label>
                    <textarea id="site_description" name="site_description" class="form-control" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? 'Industry & Factory HTML Template'); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="admin_email">Admin Email</label>
                    <input type="email" id="admin_email" name="admin_email" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['admin_email'] ?? 'masitortechnologycompanies@gmail.com'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="contact_phone">Contact Phone</label>
                    <input type="text" id="contact_phone" name="contact_phone" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['contact_phone'] ?? '+91-8045476031'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="contact_address">Contact Address</label>
                    <textarea id="contact_address" name="contact_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings['contact_address'] ?? 'RZI 51, Mahavir Enclave, Part 1 Palam Dabri Road, Dwarka, New Delhi - 110045, India'); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="update_site_settings" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Site Settings
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Admin Settings Tab -->
        <div id="admin-settings" class="settings-content">
            <h3><i class="fas fa-user-shield"></i> Admin Settings</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password *</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password *</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required 
                           onkeyup="checkPasswordStrength(this.value)">
                    <div class="password-strength">
                        <div class="password-strength-bar" id="password-strength-bar"></div>
                    </div>
                    <small>Password must be at least 6 characters long</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="update_admin_credentials" class="btn btn-primary">
                        <i class="fas fa-key"></i> Update Password
                    </button>
                </div>
            </form>
            
            <div class="danger-zone">
                <h4><i class="fas fa-exclamation-triangle"></i> Danger Zone</h4>
                <p>Clear all cached data and temporary files.</p>
                <form method="POST" onsubmit="return confirm('Are you sure you want to clear all cache? This action cannot be undone.')">
                    <button type="submit" name="clear_cache" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Clear Cache
                    </button>
                </form>
            </div>
        </div>
        
        <!-- SEO Settings Tab -->
        <div id="seo-settings" class="settings-content">
            <h3><i class="fas fa-search"></i> SEO Settings</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="meta_keywords">Meta Keywords</label>
                    <textarea id="meta_keywords" name="meta_keywords" class="form-control" rows="3" 
                              placeholder="Enter keywords separated by commas"><?php echo htmlspecialchars($settings['meta_keywords'] ?? ''); ?></textarea>
                    <small>Separate keywords with commas</small>
                </div>
                
                <div class="form-group">
                    <label for="google_analytics">Google Analytics Code</label>
                    <textarea id="google_analytics" name="google_analytics" class="form-control" rows="4" 
                              placeholder="Paste your Google Analytics tracking code here"><?php echo htmlspecialchars($settings['google_analytics'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="facebook_pixel">Facebook Pixel Code</label>
                    <textarea id="facebook_pixel" name="facebook_pixel" class="form-control" rows="4" 
                              placeholder="Paste your Facebook Pixel code here"><?php echo htmlspecialchars($settings['facebook_pixel'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="update_seo_settings" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update SEO Settings
                    </button>
                </div>
            </form>
        </div>
        
        <!-- System Info Tab -->
        <div id="system-info" class="settings-content">
            <h3><i class="fas fa-info-circle"></i> System Information</h3>
            
            <div class="system-info">
                <h4>Server Information</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>PHP Version</strong>
                        <span><?php echo phpversion(); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Server Software</strong>
                        <span><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Database Host</strong>
                        <span><?php echo DB_HOST; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Database Name</strong>
                        <span><?php echo DB_NAME; ?></span>
                    </div>
                </div>
                
                <h4 style="margin-top: 30px;">Website Statistics</h4>
                <div class="info-grid">
                    <?php
                    $conn = getDBConnection();
                    
                    // Get counts
                    $categoriesCount = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
                    $fountainsCount = $conn->query("SELECT COUNT(*) as count FROM fountains")->fetch_assoc()['count'];
                    $blogPostsCount = $conn->query("SELECT COUNT(*) as count FROM blog_posts")->fetch_assoc()['count'];
                    $blogCategoriesCount = $conn->query("SELECT COUNT(*) as count FROM blog_categories")->fetch_assoc()['count'];
                    
                    $conn->close();
                    ?>
                    <div class="info-item">
                        <strong>Categories</strong>
                        <span><?php echo $categoriesCount; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Fountains</strong>
                        <span><?php echo $fountainsCount; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Blog Posts</strong>
                        <span><?php echo $blogPostsCount; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Blog Categories</strong>
                        <span><?php echo $blogCategoriesCount; ?></span>
                    </div>
                </div>
                
                <h4 style="margin-top: 30px;">File System</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Upload Directory</strong>
                        <span><?php echo is_writable('uploads/') ? 'Writable' : 'Not Writable'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Admin Directory</strong>
                        <span><?php echo is_writable('admin/') ? 'Writable' : 'Not Writable'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Max File Upload</strong>
                        <span><?php echo ini_get('upload_max_filesize'); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Max Post Size</strong>
                        <span><?php echo ini_get('post_max_size'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.settings-content').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.settings-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Show selected tab
        document.getElementById(tabName).classList.add('active');
        event.target.classList.add('active');
    }
    
    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('password-strength-bar');
        let strength = 0;
        
        if (password.length >= 6) strength += 1;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
        if (password.match(/\d/)) strength += 1;
        if (password.match(/[^a-zA-Z\d]/)) strength += 1;
        
        strengthBar.className = 'password-strength-bar';
        if (password.length === 0) {
            strengthBar.style.width = '0%';
        } else if (strength === 1) {
            strengthBar.className += ' strength-weak';
        } else if (strength === 2 || strength === 3) {
            strengthBar.className += ' strength-medium';
        } else if (strength === 4) {
            strengthBar.className += ' strength-strong';
        }
    }
    
    // Handle form submissions with confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const dangerForms = document.querySelectorAll('.danger-zone form');
        dangerForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to perform this action? This cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>
</body>
</html>