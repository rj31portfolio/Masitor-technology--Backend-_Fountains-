<?php
require_once __DIR__.'/../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../includes/functions.php';

$conn = getDBConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$pagination = getPagination('fountains', $page, $perPage);

// Get fountains with image count
$sql = "SELECT f.*, COUNT(fi.id) as image_count 
        FROM fountains f 
        LEFT JOIN fountain_images fi ON f.id = fi.fountain_id 
        GROUP BY f.id 
        ORDER BY f.created_at DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $pagination['offset'], $pagination['perPage']);
$stmt->execute();
$result = $stmt->get_result();
$fountains = $result->fetch_all(MYSQLI_ASSOC);

// Delete fountain
if (isset($_GET['delete'])) {
    $fountainId = (int)$_GET['delete'];
    
    // Get fountain data
    $getSql = "SELECT feature_image, catalog_file FROM fountains WHERE id = ?";
    $getStmt = $conn->prepare($getSql);
    $getStmt->bind_param('i', $fountainId);
    $getStmt->execute();
    $fountain = $getStmt->get_result()->fetch_assoc();
    
    // Delete feature image
    if ($fountain['feature_image']) {
        deleteFile('uploads/fountains/' . $fountain['feature_image']);
    }
    
    // Delete catalog file
    if ($fountain['catalog_file']) {
        deleteFile('uploads/catalogs/' . $fountain['catalog_file']);
    }
    
    // Get gallery images
    $imagesSql = "SELECT image_path FROM fountain_images WHERE fountain_id = ?";
    $imagesStmt = $conn->prepare($imagesSql);
    $imagesStmt->bind_param('i', $fountainId);
    $imagesStmt->execute();
    $imagesResult = $imagesStmt->get_result();
    
    // Delete gallery images
    while ($image = $imagesResult->fetch_assoc()) {
        deleteFile('uploads/fountains/gallery/' . $image['image_path']);
    }
    
    // Delete from database (cascade will handle fountain_images)
    $deleteSql = "DELETE FROM fountains WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param('i', $fountainId);
    
    if ($deleteStmt->execute()) {
        $_SESSION['success'] = 'Fountain deleted successfully!';
    } else {
        $_SESSION['error'] = 'Error deleting fountain!';
    }
    
    header('Location: index.php');
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fountains - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-fountain"></i> Fountains</h1>
            <p>Manage your fountain products</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="data-table">
            <div class="table-header">
                <h3>All Fountains</h3>
                <a href="add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Fountain
                </a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Gallery</th>
                            <th>Catalog</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fountains)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No fountains found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($fountains as $fountain): ?>
                                <tr>
                                    <td>
                                        <?php if ($fountain['feature_image']): ?>
                                            <img src="<?php echo ADMIN_URL; ?>fountains/uploads/fountains/<?php echo $fountain['feature_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($fountain['title']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($fountain['title']); ?></td>
                                    <td><?php echo htmlspecialchars($fountain['slug']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $fountain['image_count'] > 0 ? 'active' : 'inactive'; ?>">
                                            <?php echo $fountain['image_count']; ?> images
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($fountain['catalog_file']): ?>
                                            <i class="fas fa-file-pdf text-danger"></i>
                                        <?php else: ?>
                                            <span class="status-badge inactive">No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $fountain['status'] === 'active' ? 'active' : 'inactive'; ?>">
                                            <?php echo ucfirst($fountain['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($fountain['created_at'])); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $fountain['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="index.php?delete=<?php echo $fountain['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this fountain? This will also delete all associated images.')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($pagination['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" 
                           class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>