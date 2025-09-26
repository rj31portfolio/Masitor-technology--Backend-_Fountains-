<?php
require_once __DIR__.'/../../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../../includes/functions.php';

$error = '';
$success = '';

$conn = getDBConnection();

// Get categories for dropdown
$categoriesSql = "SELECT * FROM blog_categories WHERE status = 'active' ORDER BY name";
$categoriesResult = $conn->query($categoriesSql);
$categories = $categoriesResult->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $metaDescription = trim($_POST['meta_description']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];
    $publishNow = isset($_POST['publish_now']);
    
    // Validate inputs
    if (empty($title)) {
        $error = 'Title is required!';
    } else {
        // Generate unique slug
        $slug = makeUniqueSlug('blog_posts', $title);
        
        // Handle feature image upload
        $featureImage = null;
        if (isset($_FILES['feature_image']) && $_FILES['feature_image']['error'] === 0) {
            $uploadResult = uploadFile($_FILES['feature_image'], 'uploads/blog/');
            if ($uploadResult['success']) {
                $featureImage = $uploadResult['filename'];
            } else {
                $error = $uploadResult['error'];
            }
        }
        
        // Set published date
        $publishedAt = $publishNow && $status === 'published' ? date('Y-m-d H:i:s') : null;
        
        if (!$error) {
            // Insert into database
            $sql = "INSERT INTO blog_posts (category_id, title, slug, meta_description, feature_image, content, status, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('isssssss', $categoryId, $title, $slug, $metaDescription, $featureImage, $content, $status, $publishedAt);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Blog post added successfully!';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Error adding blog post: ' . $stmt->error;
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
    <title>Add Blog Post - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-plus"></i> Add New Blog Post</h1>
            <p>Create a new blog post</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required 
                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">Uncategorized</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3"><?php echo isset($_POST['meta_description']) ? htmlspecialchars($_POST['meta_description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="feature_image">Feature Image</label>
                    <input type="file" id="feature_image" name="feature_image" class="form-control" 
                           accept="image/*" onchange="previewImage(this, 'featurePreview')">
                    <img id="featurePreview" class="image-preview" src="" alt="Feature image preview">
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" class="form-control"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="publish_now" value="1" 
                               <?php echo (isset($_POST['publish_now']) || (isset($_POST['status']) && $_POST['status'] === 'published')) ? 'checked' : ''; ?>>
                        Publish immediately
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Post
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
    
    <script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    CKEDITOR.replace('content', {
        toolbar: [
            { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
            { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
            { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
            { name: 'forms', items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'] },
            '/',
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
            { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
            { name: 'insert', items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'] },
            '/',
            { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize', 'ShowBlocks'] },
            { name: 'about', items: ['About'] }
        ],
        height: 400
    });
    </script>
</body>
</html>