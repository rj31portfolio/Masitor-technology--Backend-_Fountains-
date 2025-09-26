<?php
require_once __DIR__.'/../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$categoryId = (int)$_GET['id'];
$error = '';
$success = '';

$conn = getDBConnection();

// Get category data
$sql = "SELECT * FROM categories WHERE id = ?";
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
    $shortDescription = trim($_POST['short_description']);
    $metaTitle = trim($_POST['meta_title']);
    $metaDescription = trim($_POST['meta_description']);
    $status = $_POST['status'];
    $removeImage = isset($_POST['remove_image']);
    
    // Validate inputs
    if (empty($name)) {
        $error = 'Category name is required!';
    } else {
        // Generate unique slug if name changed
        $slug = ($name !== $category['name']) ? makeUniqueSlug('categories', $name, $categoryId) : $category['slug'];
        
        // Handle file upload
        $imageName = $category['image'];
        if ($removeImage && $imageName) {
            // Remove existing image
            deleteFile('uploads/categories/' . $imageName);
            $imageName = null;
        } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            // Remove old image if exists
            if ($imageName) {
                deleteFile('uploads/categories/' . $imageName);
            }
            
            // Upload new image
            $uploadResult = uploadFile($_FILES['image'], 'uploads/categories/');
            if ($uploadResult['success']) {
                $imageName = $uploadResult['filename'];
            } else {
                $error = $uploadResult['error'];
            }
        }
        
        if (!$error) {
            // Update database
            $sql = "UPDATE categories SET name = ?, slug = ?, short_description = ?, image = ?, 
                    meta_title = ?, meta_description = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssssi', $name, $slug, $shortDescription, $imageName, 
                             $metaTitle, $metaDescription, $status, $categoryId);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Category updated successfully!';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Error updating category: ' . $stmt->error;
            }
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
    <title>Edit Category - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Edit Category</h1>
            <p>Update category information</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           value="<?php echo htmlspecialchars($category['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="short_description">Short Description</label>
                    <textarea id="short_description" name="short_description" class="form-control" rows="3"><?php echo htmlspecialchars($category['short_description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Current Image</label>
                    <?php if ($category['image']): ?>
                        <div>
                            <img src="<?php echo ADMIN_UPLOAD_URL; ?>categories/<?php echo $category['image']; ?>" 
                                 alt="Current image" style="max-width: 200px; border-radius: 5px;">
                            <br>
                            <label>
                                <input type="checkbox" name="remove_image" value="1"> Remove image
                            </label>
                        </div>
                    <?php else: ?>
                        <p>No image uploaded</p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="image">New Image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*" 
                           onchange="previewImage(this)">
                    <img id="imagePreview" class="image-preview" src="" alt="Image preview">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?php echo $category['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $category['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" 
                           value="<?php echo htmlspecialchars($category['meta_title']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3"><?php echo htmlspecialchars($category['meta_description']); ?></textarea>
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
</body>
</html>