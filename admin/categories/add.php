<?php
require_once __DIR__.'/../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $shortDescription = trim($_POST['short_description']);
    $metaTitle = trim($_POST['meta_title']);
    $metaDescription = trim($_POST['meta_description']);
    $status = $_POST['status'];
    
    // Validate inputs
    if (empty($name)) {
        $error = 'Category name is required!';
    } else {
        $conn = getDBConnection();
        
        // Generate unique slug
        $slug = makeUniqueSlug('categories', $name);
        
        // Handle file upload
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadResult = uploadFile($_FILES['image'], 'uploads/categories/');
            if ($uploadResult['success']) {
                $imageName = $uploadResult['filename'];
            } else {
                $error = $uploadResult['error'];
            }
        }
        
        if (!$error) {
            // Insert into database
            $sql = "INSERT INTO categories (name, slug, short_description, image, meta_title, meta_description, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssss', $name, $slug, $shortDescription, $imageName, $metaTitle, $metaDescription, $status);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Category added successfully!';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Error adding category: ' . $stmt->error;
            }
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
    <title>Add Category - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-plus"></i> Add New Category</h1>
            <p>Create a new product category</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="short_description">Short Description</label>
                    <textarea id="short_description" name="short_description" class="form-control" 
                              rows="3"><?php echo isset($_POST['short_description']) ? htmlspecialchars($_POST['short_description']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Category Image</label>
                    <input type="file" id="image" name="image" class="form-control" 
                           accept="image/*" onchange="previewImage(this)">
                    <img id="imagePreview" class="image-preview" src="" alt="Image preview">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" 
                           value="<?php echo isset($_POST['meta_title']) ? htmlspecialchars($_POST['meta_title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" 
                              rows="3"><?php echo isset($_POST['meta_description']) ? htmlspecialchars($_POST['meta_description']) : ''; ?></textarea>
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
    
    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
    <script src="<?php echo ADMIN_ASSETS_URL; ?>js/admin.js"></script>
</body>
</html>