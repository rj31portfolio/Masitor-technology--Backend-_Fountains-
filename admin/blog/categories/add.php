<?php
require_once __DIR__.'/../../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    
    // Validate inputs
    if (empty($name)) {
        $error = 'Category name is required!';
    } else {
        $conn = getDBConnection();
        
        // Generate unique slug
        $slug = makeUniqueSlug('blog_categories', $name);
        
        // Insert into database
        $sql = "INSERT INTO blog_categories (name, slug, description, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $name, $slug, $description, $status);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Blog category added successfully!';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Error adding category: ' . $stmt->error;
        }
        
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blog Category - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-plus"></i> Add New Blog Category</h1>
            <p>Create a new blog category</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Category
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>