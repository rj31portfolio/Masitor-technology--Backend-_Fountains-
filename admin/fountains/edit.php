<?php
require_once __DIR__.'/../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$fountainId = (int)$_GET['id'];
$error = '';
$success = '';

$conn = getDBConnection();

// Get fountain data
$sql = "SELECT * FROM fountains WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $fountainId);
$stmt->execute();
$result = $stmt->get_result();
$fountain = $result->fetch_assoc();

if (!$fountain) {
    header('Location: index.php');
    exit;
}

// Get gallery images
$imagesSql = "SELECT * FROM fountain_images WHERE fountain_id = ? ORDER BY sort_order";
$imagesStmt = $conn->prepare($imagesSql);
$imagesStmt->bind_param('i', $fountainId);
$imagesStmt->execute();
$galleryImages = $imagesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch categories for dropdown
$categories = [];
$catResult = $conn->query("SELECT id, name FROM categories WHERE status='active' ORDER BY name ASC");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $metaDescription = trim($_POST['meta_description']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];
    $removeFeatureImage = isset($_POST['remove_feature_image']);
    $removeCatalogFile = isset($_POST['remove_catalog_file']);
    $categoryId = (int)$_POST['category_id'];
    $youtubeLink = isset($_POST['youtube_link']) ? trim($_POST['youtube_link']) : null; // <-- Add this line

    // Validate inputs
    if (empty($title)) {
        $error = 'Title is required!';
    } else {
        // Generate unique slug if title changed
        $slug = ($title !== $fountain['title']) ? makeUniqueSlug('fountains', $title, $fountainId) : $fountain['slug'];
        
        // Handle feature image
        $featureImage = $fountain['feature_image'];
        if ($removeFeatureImage && $featureImage) {
            deleteFile('uploads/fountains/' . $featureImage);
            $featureImage = null;
        } elseif (isset($_FILES['feature_image']) && $_FILES['feature_image']['error'] === 0) {
            if ($featureImage) {
                deleteFile('uploads/fountains/' . $featureImage);
            }
            $uploadResult = uploadFile($_FILES['feature_image'], 'uploads/fountains/');
            if ($uploadResult['success']) {
                $featureImage = $uploadResult['filename'];
            } else {
                $error = $uploadResult['error'];
            }
        }
        
        // Handle catalog file
        $catalogFile = $fountain['catalog_file'];
        if ($removeCatalogFile && $catalogFile) {
            deleteFile('uploads/catalogs/' . $catalogFile);
            $catalogFile = null;
        } elseif (isset($_FILES['catalog_file']) && $_FILES['catalog_file']['error'] === 0) {
            if ($catalogFile) {
                deleteFile('uploads/catalogs/' . $catalogFile);
            }
            $uploadResult = uploadFile($_FILES['catalog_file'], 'uploads/catalogs/', ALLOWED_DOC_TYPES);
            if ($uploadResult['success']) {
                $catalogFile = $uploadResult['filename'];
            } else {
                $error = $uploadResult['error'];
            }
        }
        
        // Handle gallery images deletion
        if (isset($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $imageId) {
                $imageId = (int)$imageId;
                $imageSql = "SELECT image_path FROM fountain_images WHERE id = ?";
                $imageStmt = $conn->prepare($imageSql);
                $imageStmt->bind_param('i', $imageId);
                $imageStmt->execute();
                $imageResult = $imageStmt->get_result();
                if ($image = $imageResult->fetch_assoc()) {
                    deleteFile('uploads/fountains/gallery/' . $image['image_path']);
                }
                
                $deleteSql = "DELETE FROM fountain_images WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param('i', $imageId);
                $deleteStmt->execute();
            }
        }
        
        // Handle new gallery images
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
                    }
                }
            }
        }
        
        if (!$error) {
            // Update fountain (add youtube_link)
            $sql = "UPDATE fountains SET title = ?, slug = ?, meta_description = ?, feature_image = ?, 
                    content = ?, catalog_file = ?, youtube_link = ?, status = ?, category_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssssii', $title, $slug, $metaDescription, $featureImage, 
                             $content, $catalogFile, $youtubeLink, $status, $categoryId, $fountainId);

            if ($stmt->execute()) {
                $_SESSION['success'] = 'Fountain updated successfully!';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Error updating fountain: ' . $stmt->error;
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
    <title>Edit Fountain - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Edit Fountain</h1>
            <p>Update fountain information</p>
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
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($fountain['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required 
                           value="<?php echo htmlspecialchars($fountain['title']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3"><?php echo htmlspecialchars($fountain['meta_description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Current Feature Image</label>
                    <?php if ($fountain['feature_image']): ?>
                        <div>
                            <img src="<?php echo ADMIN_UPLOAD_URL; ?>fountains/<?php echo $fountain['feature_image']; ?>" 
                                 alt="Current image" style="max-width: 200px; border-radius: 5px;">
                            <br>
                            <label>
                                <input type="checkbox" name="remove_feature_image" value="1"> Remove image
                            </label>
                        </div>
                    <?php else: ?>
                        <p>No feature image uploaded</p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="feature_image">New Feature Image</label>
                    <input type="file" id="feature_image" name="feature_image" class="form-control" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>Gallery Images</label>
                    <?php if (!empty($galleryImages)): ?>
                        <div class="gallery-preview">
                            <?php foreach ($galleryImages as $image): ?>
                                <div style="position: relative; display: inline-block;">
                                    <img src="<?php echo ADMIN_UPLOAD_URL; ?>fountains/gallery/<?php echo $image['image_path']; ?>" 
                                         alt="Gallery image" style="width: 100px; height: 100px; object-fit: cover;">
                                    <label style="position: absolute; top: 5px; right: 5px;">
                                        <input type="checkbox" name="delete_images[]" value="<?php echo $image['id']; ?>"> Delete
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No gallery images uploaded</p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="gallery_images">Add More Gallery Images</label>
                    <input type="file" id="gallery_images" name="gallery_images[]" class="form-control" 
                           accept="image/*" multiple>
                </div>
                
                <div class="form-group">
                    <label>Current Catalog File</label>
                    <?php if ($fountain['catalog_file']): ?>
                        <div>
                            <p><i class="fas fa-file-pdf text-danger"></i> <?php echo $fountain['catalog_file']; ?></p>
                            <label>
                                <input type="checkbox" name="remove_catalog_file" value="1"> Remove file
                            </label>
                        </div>
                    <?php else: ?>
                        <p>No catalog file uploaded</p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="catalog_file">New Catalog File</label>
                    <input type="file" id="catalog_file" name="catalog_file" class="form-control" 
                           accept=".pdf,.doc,.docx">
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" class="form-control"><?php echo htmlspecialchars($fountain['content']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?php echo $fountain['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $fountain['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>

                <!-- YouTube Link Field -->
                <div class="form-group">
                    <label for="youtube_link">YouTube Link</label>
                    <input type="url" id="youtube_link" name="youtube_link" class="form-control"
                        value="<?php echo isset($_POST['youtube_link']) ? htmlspecialchars($_POST['youtube_link']) : htmlspecialchars($fountain['youtube_link'] ?? ''); ?>"
                        placeholder="https://www.youtube.com/watch?v=...">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Fountain
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
    
    <script>
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