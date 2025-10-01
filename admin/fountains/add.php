<?php
require_once __DIR__.'/../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../includes/functions.php';

// Fetch categories for dropdown
$conn = getDBConnection();
$categories = [];
$catResult = $conn->query("SELECT id, name FROM categories WHERE status='active' ORDER BY name ASC");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}
$conn->close();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = $_POST['category_id']; // <-- Add this line
    $title = trim($_POST['title']);
    $metaDescription = trim($_POST['meta_description']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];

    // FIX: Assign missing fields
    $moreInfo = isset($_POST['more_info']) ? $_POST['more_info'] : '';
    $youtubeLink = isset($_POST['youtube_link']) ? trim($_POST['youtube_link']) : '';
    $mrpPrice = isset($_POST['mrp_price']) ? floatval($_POST['mrp_price']) : null;
    $sellingPrice = isset($_POST['selling_price']) ? floatval($_POST['selling_price']) : null;
    
    // Validate inputs
    if (empty($title)) {
        $error = 'Title is required!';
    } elseif (empty($categoryId)) {
        $error = 'Please select a category!';
    } else {
        $conn = getDBConnection();       
        // Generate unique slug
        $slug = makeUniqueSlug('fountains', $title);
        
        // Handle feature image upload
        $featureImage = null;
        if (isset($_FILES['feature_image']) && $_FILES['feature_image']['error'] === 0) {
            $uploadResult = uploadFile($_FILES['feature_image'], 'uploads/fountains/');
            if ($uploadResult['success']) {
                $featureImage = $uploadResult['filename'];
            } else {
                $error = $uploadResult['error'];
            }
        }
        
        // Handle catalog file upload
        $catalogFile = null;
        if (isset($_FILES['catalog_file']) && $_FILES['catalog_file']['error'] === 0) {
            $uploadResult = uploadFile($_FILES['catalog_file'], 'uploads/catalogs/', ALLOWED_DOC_TYPES);
            if ($uploadResult['success']) {
                $catalogFile = $uploadResult['filename'];
            } else {
                $error = $uploadResult['error'];
            }
        }
        
        if (!$error) {
            // Insert into database (add category_id)
            $sql = "INSERT INTO fountains (category_id, title, slug, meta_description, feature_image, content, more_info, catalog_file, youtube_link, mrp_price, selling_price, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('issssssssdss', $categoryId, $title, $slug, $metaDescription, $featureImage, $content, $moreInfo, $catalogFile, $youtubeLink, $mrpPrice, $sellingPrice, $status);
            
            if ($stmt->execute()) {
                $fountainId = $stmt->insert_id;
                
                // Handle multiple images upload
                if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
                    foreach ($_FILES['gallery_images']['name'] as $key => $name) {
                        if ($_FILES['gallery_images']['error'][$key] === 0) {
                            $file = [
                                'name' => $_FILES['gallery_images']['name'][$key],
                                'type' => $_FILES['gallery_images']['type'][$key],
                                'tmp_name' => $_FILES['gallery_images']['tmp_name'][$key],
                                'error' => $_FILES['gallery_images']['error'][$key],
                                'size' => $_FILES['gallery_images']['size'][$key]
                            ];
                            
                            $uploadResult = uploadFile($file, 'uploads/fountains/gallery/');
                            if ($uploadResult['success']) {
                                $insertSql = "INSERT INTO fountain_images (fountain_id, image_path) VALUES (?, ?)";
                                $insertStmt = $conn->prepare($insertSql);
                                $insertStmt->bind_param('is', $fountainId, $uploadResult['filename']);
                                $insertStmt->execute();
                                $insertStmt->close();
                            }
                        }
                    }
                }
                
                $_SESSION['success'] = 'Fountain added successfully!';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Error adding fountain: ' . $stmt->error;
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
    <title>Add Fountain - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-plus"></i> Add New Fountain</h1>
            <p>Create a new fountain product</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Category Dropdown -->
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required 
                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" 
                              rows="3"><?php echo isset($_POST['meta_description']) ? htmlspecialchars($_POST['meta_description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="feature_image">Feature Image</label>
                    <input type="file" id="feature_image" name="feature_image" class="form-control" 
                           accept="image/*" onchange="previewImage(this, 'featurePreview')">
                    <img id="featurePreview" class="image-preview" src="" alt="Feature image preview">
                </div>
                
                <div class="form-group">
                    <label for="gallery_images">Gallery Images (Multiple)</label>
                    <input type="file" id="gallery_images" name="gallery_images[]" class="form-control" 
                           accept="image/*" multiple>
                    <small>Hold Ctrl/Cmd to select multiple images</small>
                </div>
                
                <div class="form-group">
                    <label for="catalog_file">Catalog File (PDF/DOC)</label>
                    <input type="file" id="catalog_file" name="catalog_file" class="form-control" 
                           accept=".pdf,.doc,.docx">
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" class="form-control"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="youtube_link">YouTube Link</label>
                    <input type="url" id="youtube_link" name="youtube_link" class="form-control"
                           value="<?php echo isset($_POST['youtube_link']) ? htmlspecialchars($_POST['youtube_link']) : ''; ?>"
                           placeholder="https://www.youtube.com/watch?v=...">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="mrp_price">MRP Price</label>
                    <input type="number" step="0.01" id="mrp_price" name="mrp_price" class="form-control"
                           value="<?php echo isset($_POST['mrp_price']) ? htmlspecialchars($_POST['mrp_price']) : ''; ?>"
                           placeholder="Enter MRP Price">
                </div>
                <div class="form-group">
                    <label for="selling_price">Selling Price</label>
                    <input type="number" step="0.01" id="selling_price" name="selling_price" class="form-control"
                           value="<?php echo isset($_POST['selling_price']) ? htmlspecialchars($_POST['selling_price']) : ''; ?>"
                           placeholder="Enter Selling Price">
                </div>
                
                <div class="form-group">
                    <label for="more_info">More Info</label>
                    <textarea id="more_info" name="more_info" class="form-control"><?php echo isset($_POST['more_info']) ? htmlspecialchars($_POST['more_info']) : ''; ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Fountain
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
    
    // Initialize CKEditor
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
    
    CKEDITOR.replace('more_info', {
        height: 250
    });
    </script>
    
    <script src="<?php echo ADMIN_ASSETS_URL; ?>js/admin.js"></script>
</body>
</html>