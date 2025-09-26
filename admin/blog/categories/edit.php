<?php
require_once __DIR__.'/../../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../../includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$categoryId = (int)$_GET['id'];
$error = '';
$success = '';

$conn = getDBConnection();

// Get category data
$sql = "SELECT * FROM blog_categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $categoryId);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    
    // Validate inputs
    if (empty($name)) {
        $error = 'Category name is required!';
    } else {
        // Generate unique slug if name changed
        $slug = ($name !== $category['name']) ? makeUniqueSlug('blog_categories', $name, $categoryId) : $category['slug'];
        
        // Update database
        $sql = "UPDATE blog_categories SET name = ?, slug = ?, description = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $name, $slug, $description, $status, $categoryId);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Blog category updated successfully!';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Error updating category: ' . $stmt->error;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog Category - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Edit Blog Category</h1>
            <p>Update category information</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           value="<?php echo htmlspecialchars($category['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?php echo $category['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $category['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Category
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