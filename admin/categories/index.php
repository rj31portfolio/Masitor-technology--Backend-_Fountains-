<?php
require_once __DIR__.'/../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../includes/functions.php';

$conn = getDBConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$pagination = getPagination('categories', $page, $perPage);

// Get categories
$sql = "SELECT * FROM categories ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $pagination['offset'], $pagination['perPage']);
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);

// Delete category
if (isset($_GET['delete'])) {
    $categoryId = (int)$_GET['delete'];
    
    // Get category image path
    $getSql = "SELECT image FROM categories WHERE id = ?";
    $getStmt = $conn->prepare($getSql);
    $getStmt->bind_param('i', $categoryId);
    $getStmt->execute();
    $category = $getStmt->get_result()->fetch_assoc();
    
    // Delete image file
    if ($category['image']) {
        deleteFile('uploads/categories/' . $category['image']);
    }
    
    // Delete from database
    $deleteSql = "DELETE FROM categories WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param('i', $categoryId);
    
    if ($deleteStmt->execute()) {
        $_SESSION['success'] = 'Category deleted successfully!';
    } else {
        $_SESSION['error'] = 'Error deleting category!';
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
    <title>Categories - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-th-large"></i> Categories</h1>
            <p>Manage your product categories</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="data-table">
            <div class="table-header">
                <h3>All Categories</h3>
                <a href="add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Category
                </a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No categories found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <?php if ($category['image']): ?>
                                            <img src="<?php echo ADMIN_URL; ?>categories/uploads/categories/<?php echo $category['image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($category['short_description'], 0, 100)) . '...'; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $category['status'] === 'active' ? 'active' : 'inactive'; ?>">
                                            <?php echo ucfirst($category['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="index.php?delete=<?php echo $category['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($pagination['totalPages'] > 1): ?>
                <div class="pagination" style="padding: 20px; text-align: center;">
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
    <script src="<?php echo ADMIN_ASSETS_URL; ?>js/admin.js"></script>
</body>
</html>